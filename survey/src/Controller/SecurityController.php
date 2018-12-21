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
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

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
            if(!$loginForm->checkCredentials($credentials, $user)) {
                throw new AuthenticationException();
            };
            $authenticator = new Authenticator();
            $authenticator->generateJWTFor($user);
            $this->updateJWT($entityManager);
            $response =null;
            switch ($user->getRoles()[0]) {
                case AdminController::$role:
                    $response = new Response(json_encode(['ok'=>true,
                        'jwt' => $user->getJwt(),
                        'route'=>'admin'],
                        JSON_UNESCAPED_UNICODE));
                    break;

                case TeacherController::$role:
                    $response = new Response(json_encode(['ok'=>true,
                        'jwt' => $user->getJwt(),
                        'route'=>'teacher'],
                        JSON_UNESCAPED_UNICODE));
                    break;
                case StudentController::$role:
                    $response = new Response(json_encode(['ok'=>true,
                        'jwt' => $user->getJwt(),
                        'route'=>'student'],
                        JSON_UNESCAPED_UNICODE));
                    break;
            }


            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (AuthenticationException| CustomUserMessageAuthenticationException $e) {
            $response = new Response(json_encode(['ok' => false], JSON_UNESCAPED_UNICODE));
            return $response;
        }



    }
    private function updateJWT(EntityManagerInterface $entityManager) {
        $entityManager->flush();
    }


    /**
     * @Route("/route", name="app_route")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function getRoute(Request $request, EntityManagerInterface $entityManager): Response
    {

    }
}
