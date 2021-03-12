<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{

    /**
     * @Route(
     *      path="/api/notifications/mark_all_as_read",
     *      name="notifications_mark_all_as_read",
     *      methods={"PUT"}
     * )
     */
    public function markAllAsRead()
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('App:Notification')->findBy(['user' => $this->getUser()]);

        foreach ($notifications as $notification) {
            $notification->setReadDate(new \DateTime());
            $em->persist($notification);
        }
        $em->flush();
        return $this->json([], 200);
    }

    /**
     * @Route(
     *      path="/api/notifications/get_header_notifications",
     *      name="get_header_notifications",
     *      methods={"PUT"}
     * )
     */
    public function getHeaderNotifications(Request $request)
    {

        $page = $request->query->get('page');
        $itemsPerPage = $request->query->get('itemsPerPage');

        $repo = $this->getDoctrine()->getManager()->getRepository(Notification::class);

        $notifications = $repo->getHeaderNotifications($this->getUser(), $itemsPerPage);

        $meta = [
            'itemsPerPage' => $itemsPerPage,
            'currentPage' => $page,
            'totalItems' => $repo->countAllByUser($this->getUser())
        ];

        return $this->json(['meta' => $meta, 'data' => $notifications], 200, [], ['groups' => ['notification_read', "update_date"]]);
    }


    /**
     * @Route(
     *      path="/api/notifications/{id}/mark_as_read",
     *      name="notifications_mark_as_read",
     *     methods={"PUT"},
     *      defaults={
     *          "_api_resource_class"=Notification::class,
     *          "_api_item_operation_name"="mark_as_read"
     *       }
     *     )
     *
     */
    public function markAsRead($data)
    {
        /**
         * @var Notification $data
         */
        $data->setReadDate(new \DateTime());
        return $data;
    }

}
