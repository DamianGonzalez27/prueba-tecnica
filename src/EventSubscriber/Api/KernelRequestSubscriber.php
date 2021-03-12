<?php
namespace App\EventSubscriber\Api;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelRequestSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['requestHandler', 300],//Before Api platform
            KernelEvents::RESPONSE => ['responseHandler', 1]//Before Api platform
            //KernelEvents::VIEW => ['postRequestHandler', EventPriorities::PRE_WRITE],
        ];
    }

    public function responseHandler(ResponseEvent $event){
    	if(isset($_SERVER['APP_VERSION']))
            $event->getResponse()->headers->add(['X-App-Version'=>$_SERVER['APP_VERSION']]);
    }

    public function requestHandler(RequestEvent $event){
        /**
         * @var Request $request
         */
        $request = $event->getRequest();

        if(strpos($request->headers->get('Content-Type'), 'multipart/form-data')!==false) {
            $request->initialize(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->request->get( 'data' )
            );
            $request->headers->set( 'content-type', 'application/json' );
            $request->headers->set( 'accept', 'application/json' );
        }
    }

}