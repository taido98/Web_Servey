<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 25/11/2018
 * Time: 13:41
 */

namespace App\Controller;


use App\Entity\User;
use App\Security\Authenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\NotFoundJWTException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use http\Exception\UnexpectedValueException;


class LogOutController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse | Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        try {
            Authenticator::verifyForWithoutRole($request, $entityManager);
            $user = $entityManager->getRepository(User::class)->findOneBy(['jwt'=>$request->request->get('jwt')]);
            $user->setJWT('NULL');
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('login_form');

        } catch (AuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "AuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (UnexpectedValueException | SignatureInvalidException |
        BeforeValidException | ExpiredException $e) {

            $response = new Response(json_encode(['ok' => "UnexpectedValueException | SignatureInvalidException |BeforeValidException | ExpiredException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (NotFoundJWTException $e) {
            $response = new Response(json_encode(['ok' => "NotFoundJWTException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }
}