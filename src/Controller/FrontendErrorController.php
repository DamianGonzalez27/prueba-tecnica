<?php

namespace App\Controller;

use App\Helpers\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontendErrorController extends AbstractController
{
    /**
     * @Route(
     *     name="frontend_error",
     *     path="/frontend_error",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param Mailer $mailer
     * @return JsonResponse|void
     */
    public function frontendErrorAction( Request $request, Mailer $mailer )
    {
        if(getenv('APP_ENV')=='dev'){
            return new JsonResponse([]);
        }

        if ($request->getContentType() != 'json' || !$request->getContent()) {
            return;
        }

        $data = json_decode($request->getContent(), true);

        $html = $this->renderView( "mail/frontendError.html.twig", $data );

        $mailer->sendEmailMessage( $html, "errors@tide.company", "TGLE - Error de frontend", "no-reply@grandlounge.com.mx" );

        return new JsonResponse(["saved"=>true]);
    }
}
