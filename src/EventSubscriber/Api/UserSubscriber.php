<?php
namespace App\EventSubscriber\Api;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Helpers\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class UserSubscriber implements EventSubscriberInterface
{
	/**
	 * @var UserManager
	 */
	private $userManager;

    /**
     * @var EntityManagerInterface  $entityManager
     */
	private $entityManager;

	public function __construct(UserManager $userManager, EntityManagerInterface $entityManager)
	{
	    $this->entityManager = $entityManager;
		$this->userManager = $userManager;
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::VIEW => ['preWrite', EventPriorities::PRE_WRITE],
		];
	}

	public function preWrite(ViewEvent $event)
	{
		$user = $event->getControllerResult();
		$method = $event->getRequest()->getMethod();
		$requestParameters = json_decode($event->getRequest()->getContent());

		if (!$user instanceof User || Request::METHOD_DELETE == $method) {
			return;
		}

		if(@$requestParameters->password){
		    $user->setPlainPassword($user->getPassword());
			$user->setPassword($this->userManager->encodePassword($user,$user->getPassword()));
		}



	}
}