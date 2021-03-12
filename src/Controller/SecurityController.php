<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{


    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        // replace this line with your own code!
        return $this->render('login.html.twig');
    }

    /**
     * @Route("/api/login_check", name="login_check")
     */
    public function loginCheck()
    {
        // Never enter here
        throw new \Exception( 'Missconfiguration in login' );
    }
}
