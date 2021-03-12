<?php
namespace App\Controller;


use App\Entity\User;
use App\Helpers\Mailer;
use App\Helpers\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PasswordRecoveryController extends AbstractController
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /** @var Mailer $mailer */
    private $mailer;

    /** @var Environment $twig */
    private $twig;

    private $userManager;

    public function __construct(EntityManagerInterface $em, Environment $twig, Mailer $mailer, UserManager $userManager)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    /**
     * @Route(
     *     name="/recover_password_request",
     *     path="recover_password_request",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=User::class,
	 *          "_api_collection_operation_name"="recover_password_request"
	 *       }
     * )
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function passwordRecoveryRequest(Request $request)
    {
        $data = 'test';
        $data = json_decode($request->getContent(), true);
        $email = isset($data['email'])?$data['email']:null;

        if (!$email)
            return new JsonResponse(['data' => ['message' => 'No email provided']], 400);

        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => $email]);

        if ($user) {
            $now = new \DateTime();
            $user->setRecoveryToken($this->generateRandomToken());
            $user->setRecoveryTokenCreationDate($now);
            $this->em->persist($user);
            $this->em->flush();
            $this->sendRecoveryPasswordEmail($user);
        }
        
        return new JsonResponse(['data' => ['message' => 'The verification email has been sent']], 200);
    }

    /**
     * @Route(
     *     name="/reset_password",
     *     path="reset_password",
     *     methods={"POST"},
     *     defaults={
     *          "_api_resource_class"=User::class,
	 *          "_api_collection_operation_name"="reset_password"
	 *       }
     * )
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $password = isset($data['password'])?$data['password']:null;
        $token = isset($data['token'])?$data['token']:null;

        if (!$password || !$token)
            return new JsonResponse(['data' => ['message' => 'Password and token not received']], 400);


        $userRepo = $this->em->getRepository(User::class);

        /**
         * @var User|null $user
         */
        $user = $userRepo->findOneBy(['recoveryToken' => $token]);

        if (!$user) {
            throw new HttpException(400, 'Invalid or expired token');
        }

        $user->setPassword($this->userManager->encodePassword($user, $password));
        $user->setRecoveryToken(null);
        $this->em->persist($user);
        $this->em->flush();

        $this->sendRecoveryPasswordSuccessEmail($user);
        return new JsonResponse(['data' => ['message' => 'The password has been changed successfully']], 200);
    }

    /**
     * @return string
     */
    private function generateRandomToken()
    {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < 200; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    /**
     * @param User $user
     */
    private function sendRecoveryPasswordEmail(User $user)
    {
        $link = $_ENV['FRONTEND_BASE_ROUTE'].'/reset_password?_token='.$user->getRecoveryToken();
        $renderMail = $this->twig->render('mail/passwordRecovery.html.twig', ['link' => $link]);
        $renderMailText = $this->twig->render('mail/passwordRecovery.txt.twig', ['link' => $link]);
        $this->mailer->sendEmailMessage($renderMail, $renderMailText, $user->getEmail(), 'Recuperación de contraseña', 'Mentita baby');

    }

    /**
     * @param User $user
     */
    private function sendRecoveryPasswordSuccessEmail(User $user)
    {
        $renderMail = $this->twig->render('mail/passwordChanged.html.twig');
        $renderMailText = $this->twig->render('mail/passwordChanged.txt.twig');
        $this->mailer->sendEmailMessage($renderMail, $renderMailText, $user->getEmail(), 'Contraseña restablecida', 'Sistema');

    }
}