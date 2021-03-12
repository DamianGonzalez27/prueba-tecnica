<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    //Utiliza la primer acción definida de tipo get para linkear los resources, ejemplo en employee
	/**
	 * @Route(name="user_get",
	 *      path="/api/users/{id}",
	 *      methods={"GET"},
	 *      defaults={
	 *          "_api_resource_class"=User::class,
	 *          "_api_item_operation_name"="get"
	 *       })
	 * @return User||null
	 */
	public function getAction($data)
	{
		return $data;
	}

	/**
	 * @Route(name="user_me",
	 *      path="/api/me", methods={"GET"})
	 */
	public function meAction()
	{
		return $this->json(['data'=>$this->getUser()], 200, [], [
            'groups' =>[
                'user_read'
            ]]);
	}

    /**
	 * @Route(name="user_logout", path="/logout", methods={"OPTIONS", "GET"})
	 */
	public function logoutAction()
	{
		$response = new Response();
		$response->headers->setCookie(new Cookie( 'token',''));
		$response->headers->set( 'Access-Control-Allow-Credentials', 'true' );
		return $response;
	}


}