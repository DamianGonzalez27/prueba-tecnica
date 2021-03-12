<?php

namespace App\Command;

use App\Entity\User;
use App\Helpers\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChangeUserPasswordCommand extends Command
{
    protected static $defaultName = 'user:change-password';

    private $userManager;

    private $em;


    public function __construct( UserManager $userManager, EntityManagerInterface $em) {
    	$this->userManager = $userManager;
    	$this->em = $em;
	    parent::__construct();
    }

	protected function configure()
    {


        $this
            ->setDescription('Change user password')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        /**
         * @var User $user
         */
        $user = $this->em->getRepository("App:User")->findOneBy(["username"=>$username]);

        if( !$user ){
            throw new \Exception("Usuario $username no encontrado.");
        }

        try{

            $newPass = $this->userManager->encodePassword($user, $password);
            $user->setPassword($newPass);
            $this->em->persist($user);
            $this->em->flush();

	        $io->success("Password changed for user $username.");
	        return 0;
        }catch (\Exception $exception){
	        $io->error($exception->getMessage());
	        return 1;
        }




    }
}
