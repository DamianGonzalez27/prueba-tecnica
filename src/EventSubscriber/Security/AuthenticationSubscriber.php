<?php
namespace App\EventSubscriber\Security;

use App\Helpers\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

final class AuthenticationSubscriber implements EventSubscriberInterface
{
	/**
	 * @var UserManager
	 */
	private $userManager;


    /**
     * @var RequestStack
     */
    private $requestStack;

	public function __construct(UserManager $userManager,  RequestStack $requestStack)
	{
		$this->userManager = $userManager;
		$this->requestStack = $requestStack;
	}

	public static function getSubscribedEvents()
	{
		return [
			Events::AUTHENTICATION_SUCCESS => ['onJWTAuthenticationSuccess', 3],
			Events::AUTHENTICATION_FAILURE => ['onJWTAuthenticationFailure', 10]
        ];
	}

	public function onJWTAuthenticationSuccess(AuthenticationSuccessEvent $event){
        $response = $event->getResponse();
		$data = $event->getData();
        $expiration = new \DateTime();
        $expiration->add(new \DateInterval( 'P2D'));
        $response->headers->setCookie(new Cookie( 'token',$data['token'], $expiration));

		$response->headers->set( 'Access-Control-Allow-Credentials', 'true' );
	}

    public function onJWTAuthenticationFailure(AuthenticationFailureEvent $event){
        $response = $event->getResponse();
        $response->headers->set( 'Access-Control-Allow-Credentials', 'true' );
    }


}