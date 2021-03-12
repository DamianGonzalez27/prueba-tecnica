<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

//Solution to avoid api platform forcing getting id parameter https://github.com/api-platform/api-platform/issues/337, they said it is a bad practice :(
final class UserItemDataProvider implements ItemDataProviderInterface
{
	/**
	 * @var UserRepository
	 */
	private $repository;

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * UserMe constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param TokenStorageInterface   $tokenStorage
	 */
	public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
	{
		$this->repository   = $entityManager->getRepository( 'App:User' );
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * @param string      $resourceClass
	 * @param int|string  $id
	 * @param string|null $operationName
	 * @param array       $context
	 *
	 * @return User|null
	 * @throws ResourceClassNotSupportedException
	 */
	public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
	{
		if (User::class !== $resourceClass) {
			throw new ResourceClassNotSupportedException();
		}

		// retrieves User from the security when hitting the route 'api_users_me' (no id needed)
		if ($operationName === 'user_me') {
			return $this->tokenStorage->getToken()->getUser();
		}

		return $this->repository->find($id); // Retrieves User normally for other actions
	}
}