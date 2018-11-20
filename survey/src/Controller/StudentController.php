<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 20/11/2018
 * Time: 22:39
 */

namespace App\Controller;


use App\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
class StudentController extends AbstractController
{
    public static $role = 'ROLE_STUDENT';

    /**
     * @Route("/student/submit_survey_form", name="student_submit_survey_form")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws
     */
    public function getClasses(Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, StudentController::$role);

            if(!$request->request->has('survey_form'))
            {
                throw new BadRequestHttpException();
            }
            $survey_form = $request->request->get('survey_form');

            if(!$request->request->has('idClass'))
            {
                throw new NotFoundException();
            }



            $retData = [$survey_form];

            $entityManager->getConnection()->commit();

            $response = new Response(json_encode(['ok' => 'true', 'data'=>$retData], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch
        (AuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "AuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch
        (CustomUserMessageAuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "CustomUserMessageAuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (UnexpectedValueException | SignatureInvalidException |
        BeforeValidException | ExpiredException $e) {
//            $response = new Response(json_encode(['ok' => "SignatureInvalidException"], JSON_UNESCAPED_UNICODE));
//            $response->headers->set('Content-Type', 'application/json');
//            return $response;
            return $this->redirectToRoute('/');
        } catch (NotTrueRoleException $e) {
            $loginForm = new MyLoginFormAuthenticator($entityManager);
            $credentials = $loginForm->getCredentials($request);
            $user = $loginForm->getUserByJWT($credentials);
            $response = new Response(json_encode(['ok' => 'NotTrueRoleException'], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (NotFoundJWTException $e) {
            $response = new Response(json_encode(['ok' => "NotFoundJWTException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            $response = new Response(json_encode(['ok' => "\PhpOffice\PhpSpreadsheet\Exception"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
        } finally {
            $entityManager->getConnection()->close();
        }
    }
}