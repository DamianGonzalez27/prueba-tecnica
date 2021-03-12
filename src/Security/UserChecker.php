<?php
namespace App\Security;

use App\Entity\User as AppUser;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{

	}

	public function checkPostAuth(UserInterface $user)
	{
		if (!$user instanceof AppUser) {
			return;
		}

		// user account is expired, the user may be notified
		if (!$user->isActive()) {
			throw new AccountExpiredException('Cuenta inactiva');
		}
	}
}