<?php namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Helpers\Mailer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Validators\UserRegisterParamsValidator;
use App\Validators\ValidateMyAcountValidator;

class AuthController extends AbstractController
{

    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $encoder;
    private Mailer $mailer;
    private Environment $twg;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Mailer $mailer, Environment $twg)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->twg = $twg;
    }
    
    /**
     * @Route(
     *      name="register",
     *      path="/register",
	 *      methods={"POST"},
     *      defaults={
     *          "_api_resource_class"=User::class,
	 *          "_api_collection_operation_name"="register"
	 *       }
     * )
     */
    public function publicRegister(Request $request)
    {
        if(is_null(json_decode($request->getContent(), true)))
            return new JsonResponse(['error' => 'Parametros inexistentes'], 400);

        $validator = new UserRegisterParamsValidator(json_decode($request->getContent(), true), $this->em);
        
        if(!empty($validator->getErrors()))
            return new JsonResponse($validator->getErrors(), $validator->getStatus());

        $user = new User();
        $user->setUsername($validator->getUsername());
        $encoded = $this->encoder->encodePassword($user, $validator->getPassword());
        $user->setPassword($encoded);
        $user->setEmail($validator->getEmail());
        $user->setIsActive(false);
        $user->setRecoveryToken(
            bin2hex(openssl_random_pseudo_bytes(4, $cstrong))
        );
    
        $profile = new Profile();
        $profile->setLastNames($validator->getLastNames());
        $profile->setUser($user);
    
        $this->em->persist($user);
        $this->em->persist($profile);
        $this->em->flush();
    
        $html = $this->twg->render('mail/welcome.html.twig', [
            'user_name' => $user->getEmail(), 
            'link' => $_ENV['FRONTEND_BASE_ROUTE']."/validate_acount?t=".$user->getRecoveryToken()
            ]);
    
        $text = $this->twg->render('mail/welcome.txt.twig', [
            'user_name' => $user->getEmail(), 
            'link' => $_ENV['FRONTEND_BASE_ROUTE']."/validate_acount?t=".$user->getRecoveryToken()
            ]);
    
        $this->mailer->sendEmailMessage($html, $text, $user->getEmail(), 'Bienvenido', 'Mentita Baby');
    
        return new JsonResponse(['success' => 'Usuario creado exitosamente'], 200);
    }

    /**
     * @Route(
     *      name="validate_my_acount",
     *      path="/validate_my_acount",
	 *      methods={"POST"},
     *      defaults={
     *          "_api_resource_class"=User::class,
	 *          "_api_collection_operation_name"="validate_my_acount"
	 *       }
     * )
     */
    public function validateMyAcount(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        if(is_null($data))
            return new JsonResponse(['error' => 'Parametros inexistentes'], 400);

        $validator = new ValidateMyAcountValidator($data, $this->em);

        if(!empty($validator->getErrors()))
            return new JsonResponse($validator->getErrors(), $validator->getStatus());

        $user = $validator->getUser();
        $user->setRecoveryToken(null);
        $user->setIsActive(true);
        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse([
            'success' => 'Cuenta validada exitosamente',
            'username' => $user->getUsername()
        ], 200);
    }
}
