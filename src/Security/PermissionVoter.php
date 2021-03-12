<?php

namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{

    private $decisionManager;
    private $entityManager;


    public function __construct(AccessDecisionManagerInterface $decisionManager, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->decisionManager = $decisionManager;
    }


    protected function supports($attribute, $subject=null)
    {
        $permissionRepository = $this->entityManager->getRepository( 'App:Permission' );
        if(!$permissionRepository->findOneBy([ 'name' =>$attribute]))
            return false;

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();
        if(!$user->getRole())
            return false;
        if($user->getRole()->getName() === 'SUPER_ADMIN')
            return true;

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('SUPER_ADMIN'))) {
            return true;
        }

        /**
         * @var User $user
         */
        $user = $token->getUser();
        $userPermissions =  $user->getPermissionsArray();

        if(!in_array($attribute, $userPermissions))
            return false;

        return true;
    }


}
