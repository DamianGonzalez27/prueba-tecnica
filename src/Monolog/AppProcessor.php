<?php

namespace App\Monolog;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class AppProcessor
{
    private $tokenStorage;
    private $requestStack;
    private $security;


    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack, Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function __invoke(array $records)
    {
        try {
            $records['extra']['authenticated'] = false;
            if (null !== $token = $this->tokenStorage->getToken()) {
                $records['extra']['userId'] = $this->security->getUser() ? $this->security->getUser()->getId() : 'No user';
                $records['extra'] ['authenticated'] = $token->isAuthenticated();
            }

            $request = $this->requestStack->getCurrentRequest();
            if ($request) {
                $records['extra']['uri'] = $request->getUri();
                $records['extra']['method'] = $request->getMethod();
                $records['extra']['referer'] = $request->headers->get('referer');
                $records['extra']['rawData'] = $request->getContent();
                $records['extra']['decodedJson'] = json_decode($request->getContent(), true);
                //$records['extra']['ip'] = $request->getClientIps();
            }

        } catch (\Throwable $exception) {
            $records['extra']['error'] = 'Problema al agregar información adicional del error: ' . $exception->getMessage();
        }


        return $records;
    }
}


