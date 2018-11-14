<?php

namespace App\Controller;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Security\MyLoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
//        // get the login error if there is one
//        $error = $authenticationUtils->getLastAuthenticationError();
//        // last username entered by the user
//        $lastUsername = $authenticationUtils->getLastUsername();
//        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

//        $this->getDoctrine()->getRepository()

        $loginForm = new MyLoginFormAuthenticator($entityManager);

        $credentials = $loginForm->getCredentials($request);
        try {
            $user = $loginForm->getUser($credentials);
            $authenticator = new Authenticator();
            $authenticator->generateJWTFor($user);
            $this->updateJWT($entityManager);
            $response = new Response(json_encode(['jwt' => $user->getJwt()], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (AuthenticationException $e) {
            $response = new Response('');
            $response->setStatusCode(401, 'Unauthorized');
            return $response;
        }



    }


    private function updateJWT(EntityManagerInterface $entityManager) {
        $entityManager->flush();
    }
}
