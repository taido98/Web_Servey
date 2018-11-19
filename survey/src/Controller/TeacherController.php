<?php

namespace App\Controller;

use App\Config\SRCConfig;
use App\Entity\ClassSubject;
use App\Entity\Student;
use App\Entity\SurveyForm;
use App\Entity\Teacher;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Exception\NotFoundTeacherException;
use App\Security\Authenticator;
use App\Security\MyLoginFormAuthenticator;
use App\Security\NotFoundJWTException;
use App\Security\NotTrueRoleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use http\Exception\UnexpectedValueException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;

class TeacherController extends AbstractController
{
    public static $role = 'ROLE_TEACHER';
    /**
     * @Route("/teacher", name="teacher")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {

        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, Teacher::$role);
//            $isSuccess = false;

//            $isSuccess = $func($request, $entityManager);

            $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['idteacher'=>$request->request->get('id')]);

            if($teacher === null) {
                throw new NotFoundException();
            }

            $classes = $entityManager->getRepository(ClassSubject::class)->find(['teacher_id'=>$teacher->getId()]);

            $retData = [];
            foreach ($classes as $class) {

                $statistic = [];

                $surveyForms = $class->getSurveyForm();



                $retData[] = ['idClass'=> $class->getIdclass(),
                    'namesubject'=>$class->getNamesubject(),
                    'location'=>$class->getLocation(),
                    'numberLesson'=>$class->getNumberlesson(),
                    'statistic'=>$statistic];
            }


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
        }

//        return $this->render('teacher/index.html.twig', [
//            'controller_name' => 'TeacherController',
//        ]);
    }
}
