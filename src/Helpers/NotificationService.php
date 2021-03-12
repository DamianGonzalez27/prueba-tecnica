<?php

namespace App\Helpers;

use App\Entity\Notification;
use App\Entity\NotificationTopic;
use App\Entity\NotificationUserEntity;
use App\Entity\Permission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;


class NotificationService
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var Environment $templating
     */
    protected $templating;

    /**
     * @var Mailer $mailer
     */
    protected $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $em, Environment $templating, Mailer $mailer, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * @param User $toUser
     * @param $notificationTopicName
     * @param $group
     * @param $html
     * @param $link
     * @param bool $withFlush
     * @throws \Exception
     */
    private function dispatch(User $toUser, $notificationTopicName, $html, $link, $group = null, $withFlush = false)
    {
        $notificationTopicEntity = $this->getNotificationTopicByName($notificationTopicName);
        $notificationRepo = $this->em->getRepository(Notification::class);

        if ($group && $notification = $notificationRepo->findOneBy([
                    'user' => $toUser,
                    'groupName' => $group]
            )) {

            $notification->setQuantity($notification->getQuantity() + 1);
            $notification->setReadDate(null);
        } else {
            $notification = new Notification();
            $notification->setNotificationTopic($notificationTopicEntity);
            $notification->setGroupName($group);
            $notification->setUser($toUser);
            $notification->setLink($link);
            $notification->setQuantity(1);
            $notification->setHtml($html);
        }

        $html = str_replace("{count}", $notification->getQuantity(), $html);

        $notification->setHtml($html);

        if ($this->isNotificationTopicNotificationEnabled($toUser, $notificationTopicName, Notification::DELIVER_BY_MAIL)) {
            $this->em->persist($notification);
            $this->mailer->sendNotification($notification);
        }

        if ($withFlush) $this->em->flush();
    }

    /**
     * @param User $toUsers []
     * @param $notificationTopicName
     * @param $group
     * @param $html
     * @param $link
     * @param bool $withFlush
     * @throws \Exception
     */
    public function dispatchToUsers($toUsers, $notificationTopicName, $html, $link, $group = null, $withFlush = false)
    {
        foreach ($toUsers as $user) {
            $this->dispatchIfCan($user, $notificationTopicName, $html, $link, $group, $withFlush);
        }
    }

    /**
     * @param User $user
     * @param string $notificationTopicName
     * @param null $entity
     * @return bool
     * @throws \Exception
     */
    public function canReceiveNotification(User $user, string $notificationTopicName, $entity = null)
    {

        if (!$this->isNotificationTopicNotificationEnabled($user, $notificationTopicName, Notification::DELIVER_BY_MAIL)) {
            return false;
        }

        if ($entity) {
            $notificationTopicEntity = $this->getNotificationTopicByName($notificationTopicName);
            $notificationUserEntityRepo = $this->em->getRepository(NotificationUserEntity::class);
            $entityClass = get_class($entity);
            $entityId = $entity->getId();
            if ($notificationUserEntityRepo->findOneBy(['user' => $user,
                    'notificationTopic' => $notificationTopicEntity,
                    'entityClass' => $entityClass,
                    'entityId' => $entityId]
            )) return false;
        }

        return true;
    }


    /**
     * @param User $toUser
     * @param $notificationTopicName
     * @param $html
     * @param $link
     * @param null $entity
     * @param null $group
     * @param bool $withFlush
     * @throws \Exception
     */
    public function dispatchIfCan(User $toUser, $notificationTopicName, $html, $link, $entity = null, $group = null, $withFlush = false)
    {
        if ($this->canReceiveNotification($toUser, $notificationTopicName, $entity)) {
            $this->dispatch($toUser, $notificationTopicName, $html, $link, $group, $withFlush);
        }
    }

    /**
     * @param User $user
     * @param string $notificationTopicName
     * @param null $entity
     * @param bool $withFlush
     * @throws \Exception
     */
    public function unsubscribe(User $user, string $notificationTopicName, $entity = null, $withFlush = false)
    {
        $notificationTopicEntity = $this->getNotificationTopicByName($notificationTopicName);
        if (!$entity) {
            $user->addDisabledNotificationTopic($notificationTopicEntity);
            $this->em->persist($user);
        } else {
            $entityClass = get_class($entity);
            $entityId = $entity->getId();
            if (!$this->em->getRepository(NotificationUserEntity::class)->findOneBy(['notificationTopic',
                    $notificationTopicEntity,
                    'user' => $user,
                    'entityClass' => $entityClass,
                    'entityId' => $entityId]
            )) {
                $notificationUserEntity = new NotificationUserEntity();
                $notificationUserEntity->setUser($user);
                $notificationUserEntity->setEntityClass($entityClass);
                $notificationUserEntity->setEntityId($entityId);
                $notificationUserEntity->setNotificationTopic($notificationTopicEntity);
                $this->em->persist($notificationUserEntity);
            }
        }

    }

    public function getNotificationTopicByName($notificationTopicName)
    {
        $notificationTopicEntity = $this->em->getRepository(NotificationTopic::class)
            ->findOneBy(['name' => $notificationTopicName]);
        if (!$notificationTopicEntity) throw new \Exception('Notification topic not found');
        return $notificationTopicEntity;
    }


    public function isNotificationTopicNotificationEnabled(User $user, $notificationTopicName, $deliverBy)
    {
        $permissionsRepo = $this->em->getRepository(Permission::class);

        $notificationTopic = $this->em->getRepository(NotificationTopic::class)->findOneBy(['name' => $notificationTopicName]);

        if (!$notificationTopic) {
            $this->logger->critical('Notification topic no encontrado ' . $notificationTopicName);
            return false;
        }

        if (count($notificationTopic->getPermissions()) > 0) {

            $userHasPermissionToReceiveNotification = false;

            foreach ($user->getPermissionsArray() as $permissionName) {
                $permissionEntity = $permissionsRepo->findOneBy(['name' => $permissionName]);
                foreach ($permissionEntity->getNotificationTopics() as $notTopic) {
                    if ($notTopic->getName() === $notificationTopicName)
                        $userHasPermissionToReceiveNotification = true;
                }
            }

            if (!$userHasPermissionToReceiveNotification)
                return false;

        }

        //Email notifications disabled by default
        if ($deliverBy === Notification::DELIVER_BY_MAIL)
            $enabled = false;

        foreach ($user->getUserNotificationTopics() as $userNotificationTopic) {
            if ($userNotificationTopic->getNotificationTopic()->getName() === $notificationTopicName) {
                if ($deliverBy === Notification::DELIVER_BY_MAIL) {
                    $enabled = $userNotificationTopic->getIsMailEnabled();
                }
            }
        }

        return $enabled;
    }


    public function markNotificationsAsReadByTopicAndLink($notificationTopicName, $link)
    {

        $notificationTopic = $this->em->getRepository(NotificationTopic::class)->findOneBy([
            'name' => $notificationTopicName
        ]);

        $notifications = $this->em->getRepository(Notification::class)->findBy([
            'notificationTopic' => $notificationTopic,
            'link' => $link
        ]);

        foreach ($notifications as $notification) {
            /**
             * @var Notification $notification
             */
            $notification->setReadDate(new \DateTime());
            $this->em->persist($notification);
        }
    }

}