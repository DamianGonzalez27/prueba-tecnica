<?php
namespace App\EventSubscriber;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface{

    public static function getSubscribedEvents() {
        return [
            KernelEvents::EXCEPTION => 'notifyException'
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function notifyException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        //Solución temporal a la excepción
        if($exception instanceof ForeignKeyConstraintViolationException)
            $event->setResponse(new JsonResponse([
                'type'=> 'https://tools.ietf.org/html/rfc2616#section-10',
                'title'=>'No es posible eliminar con relaciones',
                'detail'=>'Otros elementos del sistema dependen de este elemento por lo que no es posible eliminarlo. Si aún así es necesario eliminarlo, favor de contactar al administrador del sistema.'
            ],409));
    }
}
