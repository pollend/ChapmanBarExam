<?php


namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\EventListener\AttachRefreshTokenOnSuccessListener;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManager;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Illuminate\Support\Str;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Error;
use Prophecy\Argument\Token\TokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SamlAuthController extends  AbstractController
{

    static $TENANTID = 'http://schemas.microsoft.com/identity/claims/tenantid'; // 00000000-0000-0000-0000-000000000000
    static $OBJECT_IDENTIFIER = 'http://schemas.microsoft.com/identity/claims/objectidentifier'; // // 00000000-0000-0000-0000-000000000000
    static $DISPLAY_NAME = 'http://schemas.microsoft.com/identity/claims/displayname'; // <Last Name>, <First Name> (<occupation>)
    static $IDENTITYPROVIDER = 'http://schemas.microsoft.com/identity/claims/identityprovider'; // ttps://sts.windows.net/<tenantid>
    static $METHOD_IDENTIFICATION = 'http://schemas.microsoft.com/claims/authnmethodsreference'; // method of auth
    static $GIVEN_NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname'; // <First Name>
    static $SURNAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname'; // <Last Name>
    static $EMAIL_ADDRESS = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'; // <EMAIL>
    static $NAME = 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'; // <email>


    protected $entityManager;
    protected $jwtManager;
    protected $oneLoginAuth;
    protected $refreshTokenManager;
    protected $attachRefreshTokenOnSuccessListener;
    protected $session;

    public function __construct(Auth $oneLoginAuth, AttachRefreshTokenOnSuccessListener $attachRefreshTokenOnSuccessListener, RefreshTokenManager $refreshTokenManager, EntityManagerInterface $entityManager, JWTTokenManagerInterface $JWTTokenManager,SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->jwtManager = $JWTTokenManager;
        $this->oneLoginAuth = $oneLoginAuth;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->attachRefreshTokenOnSuccessListener = $attachRefreshTokenOnSuccessListener;
        $this->session = $session;
    }

    /**
     * @Route("/saml/login", name="saml_login")
     */
    public function loginAction(Request $request)
    {
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
     * @Route("/saml/acs", name="saml_authenticate")
     */
    public function attemptAuthentication(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->oneLoginAuth->processResponse();
        if ($this->oneLoginAuth->getErrors()) {
//              $this->logger->error($this->oneLoginAuth->getLastErrorReason());
            throw new AuthenticationException($this->auth->getLastErrorReason());
        }

        $attributes = $this->oneLoginAuth->getAttributes();
        $user = null;
        //no user exist to create a new user
        if (!($user = $userRepository->findByEmail(Str::lower($attributes[SamlAuthController::$EMAIL_ADDRESS][0])))) {
            $user = new User();
            $user->setEmail(Str::lower($attributes[SamlAuthController::$EMAIL_ADDRESS][0]));
            $user->setRoles([User::ROLE_USER]);
            $user->setUsername($attributes[SamlAuthController::$GIVEN_NAME][0] . ' ' . $attributes[SamlAuthController::$SURNAME][0]);
            // TODO: not using remember token
            $user->setRememberToken('asfnaiw3am3o');
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $token = $this->jwtManager->create($user);

        $jwtSuccessEvent = new AuthenticationSuccessEvent(array(), $user, new Response());
        $this->attachRefreshTokenOnSuccessListener->attachRefreshToken($jwtSuccessEvent);
        $refreshToken = $this->refreshTokenManager->getLastFromUsername($user->getEmail());

        $response = $this->redirect('/');
        $response->headers->setCookie(new Cookie('AUTH_REFRESH_TOKEN', $refreshToken, strtotime('now + 10 minutes')));
        $response->headers->setCookie(new Cookie('AUTH_TOKEN', $token, strtotime('now + 10 minutes')));
        return $response;
    }

//    /**
//     * @Route("/saml/logout", name="saml_logout")
//     */
//    public function attemptLogout(Request $request,TokenInterface $token)
//    {
//        $user = $request->getUser();
//
//        try{
//            $this->oneLoginAuth->processSLO();
//        }
//        $this->oneLoginAuth->logout();
//    }
}