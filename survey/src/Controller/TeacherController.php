<?php
/**
 * Created by PhpStorm.
 * User: vanminh
 * Date: 20/11/2018
 * Time: 20:48
 */

namespace App\Controller;

use App\Config\SRCConfig;
use App\Entity\ClassSubject;
use App\Entity\CriteriaLevel;
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
use App\Security\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->render('teacher/teacher.html.twig', [
            'controller_name' => 'TeacherController',
        ]);
    }


    /**
     * @Route("/teacher/getAll", name="teacher_getAll")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getAll(Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, TeacherController::$role);

            $user = $entityManager->getRepository(User::class)->findOneBy(['jwt'=>$request->request->get('jwt')]);
            if($user === null) {
                throw new NotFoundException();
            }
            $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['userdb'=>$user]);
            if($teacher === null) {
                throw new NotFoundException();
            }


            //get profile
            $profile = $teacher->getProfile();


            // get criterialLevel
//            $criterialLevels = [];
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
            $appendix = CriteriaLevel::convertArrayCriterialLevelObjectsToArray($criterialLevels);

            $classes = $teacher->getStatisticAndClassInfo($appendix, $entityManager);


            $entityManager->getConnection()->commit();

            $response = new Response(json_encode(['ok' => 'true', 'data'=>['profile'=>$profile, 'classes'=>$classes,'appendix'=>$appendix]], JSON_UNESCAPED_UNICODE));
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
            $response = new Response(json_encode(['ok' => "false", 'route'=>'login_form'], JSON_UNESCAPED_UNICODE));
//            $response->headers->set('Content-Type', 'application/json');
            return $response;
//            return $this->redirectToRoute('login_form');
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
     * @Route("/teacher/updateProfile", name="teacher_updateProfile")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function updateProfile(Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, TeacherController::$role);

            $user = $entityManager->getRepository(User::class)->findOneBy(['jwt'=>$request->request->get('jwt')]);
            if($user === null) {
                throw new NotFoundException();
            }
            $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['userdb'=>$user]);
            if($teacher === null) {
                throw new NotFoundException();
            }

            if(!$request->request->has('data')) {
                throw new BadRequestHttpException();
            }

            $data = json_decode($request->request->get('data'), JSON_UNESCAPED_UNICODE);
            if(array_key_exists('fullname', $data)) {
                $teacher->setFullname($data['fullname']);
            }
            if(array_key_exists('vnuemail', $data)) {
                $teacher->setVnuemail($data['vnuemail']);
            }

            if(array_key_exists('password', $data)) {
                $passwordEncoder= new PasswordEncoder();
                $user->setPassword($passwordEncoder->encode($user, $data['password']));
            }
            $entityManager->persist($user);
            $entityManager->persist($teacher);
            $entityManager->getConnection()->commit();

            $response = new Response(json_encode(['ok' => 'true'], JSON_UNESCAPED_UNICODE));
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
            $response = new Response(json_encode(['ok' => "false", 'route'=>'login_form'], JSON_UNESCAPED_UNICODE));
//            $response->headers->set('Content-Type', 'application/json');
            return $response;
//            return $this->redirectToRoute('login_form');
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