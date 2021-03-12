<?php
namespace App\Controller;

use App\Entity\NotificationTopic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NotificationTopicController extends AbstractController
{

    /**
     * @Route(
     *     name="notification_topics_user_can_receive",
     *     path="/api/notification_topics/user_can_receive",
     *     methods={"GET"}
     * )
     */
    public function notificationTopicsUserCanReceive()
    {

        $em = $this->getDoctrine()->getManager();


        $permissionsArray = $this->getUser()->getPermissionsArray();
        $notificationsTopics = $em->getRepository(NotificationTopic::class)->findTopicsByPermissionsArray($permissionsArray);

        return $this->json([
            'meta' => [
                'totalItems' => count($notificationsTopics),
                'itemsPerPage' => count($notificationsTopics),
                'currentPage' => 1
            ],
            'data' => $notificationsTopics
        ], 200, [], [
            'groups' => [
                'notification_topic_read'
            ]
        ]);
    }

}
