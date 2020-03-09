<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $entityManager;
    private $router;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $route)
    {
        $this->entityManager = $entityManager;
        $this->router = $route;
    }
//    private $userRepository;
//    public function __construct(UserRepository $userRepository)
//    {
//        $this->userRepository = $userRepository;
//    }

    //Este mètode s'executa després de cada petició
    public function supports(Request $request)
    {
        // do your work when we're POSTing to the login page
        return ($request !== null ? $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST'): false);
    }

    //Si supports() retorna true s'executa este a continuació, que retorna un array en les dades de connexió
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );
        return $credentials;
    }

    //A continuació este mètode rep les credencials de l'anterior i busca l'usuari a la BD gràcies al repositori
    //Si el troba retorna l'usuari i sinó null
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

//        if (!$user) {
//            // fail authentication with a custom error
//            //throw new CustomUserMessageAuthenticationException('Email could not be found.');
//            die;
//        }

        return $user;        //return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }


    public function checkCredentials($credentials, UserInterface $user)
    {
        return strcmp($user->getPassword(), $credentials['password'])===0;

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // todo
        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        # We only throw an error if the credentials provided were wrong or the user doesn't exist
        # Otherwise we pass along to the next authenticator
//        if ($exception instanceof BadCredentialsException || $exception instanceof UsernameNotFoundException) {
//            $resp = new Response('', Response::HTTP_UNAUTHORIZED);
//            $resp->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Secured Area'));
//            return $resp;
//        }
//
//        // Let another guard authenticator handle it
//        return null;
    }

    protected function getLoginUrl()
    {
        // TODO: Implement getLoginUrl() method.
        dd($this);
        //return $this->router->generate('app_login');
    }
}
