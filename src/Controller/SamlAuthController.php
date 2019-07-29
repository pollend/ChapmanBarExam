<?php


namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OneLogin\Saml2\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SamlAuthController extends  AbstractController {

    protected $entityManager;
    protected $jwtManager;
    protected $oneLoginAuth;
    public function __construct(Auth $oneLoginAuth,EntityManagerInterface $entityManager,JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->jwtManager = $JWTTokenManager;
        $this->oneLoginAuth = $oneLoginAuth;
    }

    /**
     * @Route("/saml/login", name="saml_login")
     */
    public function loginAction(Request $request){
        $this->oneLoginAuth->login();
    }

    /**
     * @Route("/saml/metadata", name="saml_metadata")
     */
    public function metadataAction(Request $request)
    {

        $metadata = $this->oneLoginAuth->getSettings()->getSPMetadata();

        $response = new Response($metadata);
        $response->headers->set('Content-Type', 'xml');
        return $response;
    }

    /**
     * @Route("/saml/auth", name="saml_authenticate")
     */
    public function attemptAuthentication(Request $request){
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->oneLoginAuth->processResponse();
        if($this->oneLoginAuth->getErrors()){
//              $this->logger->error($this->oneLoginAuth->getLastErrorReason());
            throw new AuthenticationException($this->auth->getLastErrorReason());
        }
        $attributes = $this->oneLoginAuth->getAttribute();

        if(!($user = $userRepository->findByEmail($attributes['mail']))){
            $user = new User();
            $user->setEmail($attributes['mail']);
            $user->setRoles([User::ROLE_USER]);
            $user->setUsername($attributes['cn'] . ' ' . $attributes['sn']);
            // TODO: not using remember token
            $user->setRememberToken('asfnaiw3am3o');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $token = $this->jwtManager->create($user);
        $response = $this->redirect('/');
        $response->headers->setCookie(new Cookie('AUTH_TOKEN',$token,strtotime('now + 10 minutes')));
        return $response;
    }
}