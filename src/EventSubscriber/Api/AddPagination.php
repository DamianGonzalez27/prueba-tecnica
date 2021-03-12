<?php

declare(strict_types=1);

namespace App\EventSubscriber\Api;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddPagination implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'addPagination',
        ];
    }

    /**
     * @param ResponseEvent $event
     * @throws \UnexpectedValueException
     */
    public function addPagination(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($response->getStatusCode() >= 400)
            return;

        if ($data = $request->attributes->get('data')) {
            $response = $event->getResponse();
            $meta = '';
            if ($data instanceof Paginator)
                $meta = '"meta": {"totalItems":' . $data->getTotalItems() . ', "itemsPerPage":' . $data->getItemsPerPage() . ', "currentPage":' . $data->getCurrentPage() . '}, ';
            $response->setContent('{' . $meta . '"data":' . $response->getContent() . '}');
        }
    }


}