<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 20/11/2018
 * Time: 22:39
 */

namespace App\Controller;


use App\Config\SRCConfig;
use App\Entity\ClassSubject;
use App\Entity\CriteriaLevel;
use App\Entity\Student;
use App\Entity\SurveyForm;
use App\Entity\User;
use App\Exception\NotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Authenticator;
use App\Security\MyLoginFormAuthenticator;
use App\Security\NotFoundJWTException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use App\Security\NotTrueRoleException;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Config\Definition\Exception\Exception;
use http\Exception\UnexpectedValueException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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

            $class = $entityManager->getRepository(ClassSubject::class)->findOneBy(['idclass'=>$request->request->get('idClass')]);


            if($class === null) {
                throw new NotFoundException();
            }

            $surveyForm = new SurveyForm();
            $surveyForm->setContent(json_decode($survey_form,true, 512, JSON_UNESCAPED_UNICODE));
            $class->addSurveyForm($surveyForm);

            $entityManager->persist($surveyForm);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            $response = new Response(json_encode(['ok' => 'true'], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch
        (AuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "AuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (CustomUserMessageAuthenticationException $e) {
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


    /**
     * @Route("/student", name="student")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     */
    public function index(Request $request, EntityManagerInterface $entityManager) {
        try {
            Authenticator::verifyFor($request, $entityManager, StudentController::$role);

            $user = $entityManager->getRepository(User::class)->findOneBy(['jwt'=>$request->request->get('jwt')]);
            if($user === null) {
                throw new NotFoundException();
            }
            $student = $entityManager->getRepository(Student::class)->findOneBy(['iduserdb'=>$user]);

            $profile = $student->getProfile();


            $criterialLevels = $entityManager->getRepository(CriteriaLevel::class)->findAll();
            if($criterialLevels === null) {
                $criterialLevels = [];
                for($i = 0; $i < count(SRCConfig::DEFAULT_FORM); ++$i) {
                    $criterialLevel = new CriteriaLevel();
                    $criterialLevel->setName(SRCConfig::DEFAULT_FORM[$i]);
                    $entityManager->persist($criterialLevel);

                    $criterialLevels[] = $criterialLevel;
                }
                $entityManager->flush();
                $criterialLevels = $entityManager->getRepository(CriteriaLevel::class)->findAll();

            }
            $classes = $student->getNecessarySurveyFormsInfo(CriteriaLevel::convertArrayCriterialLevelObjectsToArray($criterialLevels));

            $response = new Response(json_encode(['ok' => 'true', 'data'=>['profile'=>$profile, 'classes'=>$classes]], JSON_UNESCAPED_UNICODE));
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
            return $this->redirectToRoute('login_form');
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
    }
}