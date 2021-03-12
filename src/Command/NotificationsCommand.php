<?php

namespace App\Command;

use App\Entity\NotificationTopic;
use App\Entity\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationsCommand extends Command
{
    // Notificaciones de topics sin permisos pueden ser enviadas a cualquier usuario
    /**
     * @var array
     */
    private $notificationPermissions = [

        //NotificationTopic::ADMIN_NEW_RESTAURANT_ACCOUNT_REQUEST=> [
        //    'description' => '',
        //    'permissions' => []
        //]
    ];

    protected static $defaultName = 'notifications:update';

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    protected function configure()
    {
        $this
            ->setDescription('Create notification topic relations with permissions');
    }

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $permissionRepo = $this->em->getRepository('App:Permission');
        $notificationTopicRepo = $this->em->getRepository('App:NotificationTopic');

        /**
         * @var Permission $permission
         */
        $permissions = $permissionRepo->findAll();
        foreach ($permissions as $permission) {
            foreach ($permission->getNotificationTopics() as $notificationTopic) {
                $permission->removeNotificationTopic($notificationTopic);
            }
            $this->em->persist($permission);
        }
        $this->em->flush();


        foreach ($this->notificationPermissions as $notificationTopicName => $config) {
            $description = $config['description'];

            if (!$notificationTopicEntity = $notificationTopicRepo->findOneBy(['name' => $notificationTopicName])) {
                $notificationTopicEntity = new NotificationTopic();
            }
            $notificationTopicEntity->setName($notificationTopicName);
            $notificationTopicEntity->setDescription($description);

            $this->em->persist($notificationTopicEntity);
            $this->em->flush();


            $permissions = $config['permissions'];
            foreach ($permissions as $permissionName) {

                if (!$permissionEntity = $permissionRepo->findOneBy(['name' => $permissionName])) {
                    $permissionEntity = new Permission();
                    $permissionEntity->setName($permissionName);
                }

                $notificationTopicEntity->addPermission($permissionEntity);

                $this->em->persist($permissionEntity);
                $this->em->persist($notificationTopicEntity);
                $this->em->flush();

            }
        }

        return 0;
    }
}
