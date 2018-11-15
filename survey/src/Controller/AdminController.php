<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\User;
use App\Security\Authenticator;
use App\Security\MyLoginFormAuthenticator;
use App\Security\NotFoundJWTException;
use App\Security\NotTrueRoleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

class AdminController extends AbstractController
{
    public static $role = 'ROLE_ADMIN';

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/admin/add", name="admin_add")
     */
    public function addAdmin()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $adminUser = new User();
        $adminUser->setUsername('vanminh');
        $adminUser->setPassword('12345');

        $role = new Role('ROLE_ADMIN');
        $adminUser->setRoles([$role->getRole()]);


        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($adminUser);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id ' . $adminUser->getId());

    }

    /**
     * @Route("/admin/students/add", name="admin_students_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addStudents(Request $request, EntityManagerInterface $entityManager)
    {
        try {
            Authenticator::verifyFor($request, $entityManager, AdminController::$role);
            $isSuccess = false;
//            if ($request->request->has('file')) {
                $dir = dirname(__DIR__) . '/../web/uploads/';
                $name = uniqid() . '.xlsx';

                foreach ($request->files as $uploadedFile) {
                    $uploadedFile->move($dir, $name);
                }
                $file = dirname(__DIR__) . "/../web/uploads/" . $name;

                if (file_exists($file)) {


                    $data = Excel::readStudentExcel($file);

                    if($data) {

                        $isSuccess = true;
                        for($i=0; $i < count($data); ++$i) {

                            $student = new Student();
                            $student->setIdstudent($data[$i][0]);

                            $student->setFullname($data[$i][2]);
                            $student->setVnuemail($data[$i][3]);
                            $student->setCourse($data[$i][4]);

                            $userStudent = new User();
                            $userStudent->setUsername($student->getIdstudent());
                            $userStudent->setPassword($data[$i][1]);

                            $role = new Role('ROLE_STUDENT');
                            $userStudent->setRoles([$role->getRole()]);
                            $entityManager->persist($userStudent);
                            $entityManager->flush();
                            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $userStudent->getUsername()]);
                            $student->setIduserdb($user);
                            $entityManager->persist($student);
                        }
                        $entityManager->flush();
                    }


                }
//            }
            $response = new Response(json_encode(['ok' => $data ], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        } catch (AuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "AuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (CustomUserMessageAuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "CustomUserMessageAuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (UnexpectedValueException | SignatureInvalidException |
        BeforeValidException | ExpiredException $e) {
            $response = new Response(json_encode(['ok' => "SignatureInvalidException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

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
        }
    }


    /**
     * @Route("/admin/teachers/add", name="admin_teachers_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addTeachers(Request $request, EntityManagerInterface $entityManager)
    {
        try {
            Authenticator::verifyFor($request, $entityManager, AdminController::$role);
            $isSuccess = false;
//            if ($request->request->has('file')) {
            $dir = dirname(__DIR__) . '/../web/uploads/';
            $name = uniqid() . '.xlsx';

            foreach ($request->files as $uploadedFile) {
                $uploadedFile->move($dir, $name);
            }
            $file = dirname(__DIR__) . "/../web/uploads/" . $name;

            if (file_exists($file)) {


                $data = Excel::readTeacherExcel($file);

                if($data) {

                    $isSuccess = true;
                    for($i=0; $i < count($data); ++$i) {


//                        $entityManager->persist($student);
                    }
                    $entityManager->flush();
                }


            }
//            }
            $response = new Response(json_encode(['ok' => $data ], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        } catch (AuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "AuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (CustomUserMessageAuthenticationException $e) {
            $response = new Response(json_encode(['ok' => "CustomUserMessageAuthenticationException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (UnexpectedValueException | SignatureInvalidException |
        BeforeValidException | ExpiredException $e) {
            $response = new Response(json_encode(['ok' => "SignatureInvalidException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

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
        }
    }
}
