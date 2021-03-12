<?php

namespace App\Command;

use App\Helpers\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'user:create';

    private $userManager;

    public function __construct( UserManager $userManager) {
    	$this->userManager = $userManager;
	    parent::__construct();
    }

	protected function configure()
    {

        $this
            ->setDescription('Create a user')
            ->addArgument('username', InputArgument::REQUIRED, 'Uername')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role name')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
	    $role = $input->getArgument('role');



        try{
            $this->userManager->create($username, $email, $password, $role);
	        $io->success("User $username created correctly with role");
	        return 0;
        }catch (\Exception $exception){
	        $io->error($exception->getMessage());
	        return 1;
        }




    }
}
