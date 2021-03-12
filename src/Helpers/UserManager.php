<?php
namespace App\Helpers;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager{
	/**
	 * @var EntityManagerInterface $em
	 */
	private $em;

	/** @var UserPasswordEncoderInterface $encoder */
	private $encoder;

	/**
	 * @var \App\Repository\UserRepository|\Doctrine\Common\Persistence\ObjectRepository $userRepo
	 */
	private $userRepo;

	public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder) {
		$this->em = $em;
		$this->userRepo = $em->getRepository( 'App:User' );
		$this->encoder = $encoder;
	}

	public function create(string $username, string $email, string $password, $role=null ){
		if($this->userRepo->findOneBy([ 'username' =>$username]))
			throw new \Exception( 'User alredy exists' );


        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->encodePassword($user, $password));
        $user->setIsActive(true);

		if($role) {
            $role = $this->em->getRepository('App:Role')->findOneBy(['name' => $role]);
            if (!$role)
                throw new \Exception('Role not found');

            $user->setRole($role);

        }

		$this->em->persist($user);
		$this->em->flush();
		return $user;
	}

	public function encodePassword($user, $password){
		return $this->encoder->encodePassword($user, $password);
	}
}