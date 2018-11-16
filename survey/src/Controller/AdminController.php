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
use controller\TeacherController;
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

                if ($data) {

                    $isSuccess = true;
                    for ($i = 0; $i < count($data); ++$i) {

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
            $response = new Response(json_encode(['ok' => $data], JSON_UNESCAPED_UNICODE));
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
        return $this->addFromExcel($request, $entityManager, function ($entityManager, $file) {

            $data = Excel::readTeacherExcel($file);

            if ($data) {

                $isSuccess = true;

                for ($i = 0; $i < count($data); ++$i) {

                    $teacher = new Teacher();

                    $teacher->setFullname($data[$i][2]);
                    $teacher->setVnuemail($data[$i][3]);
                    $teacher->setIdteacher($data[$i][4]);


                    $user = new User();
                    $role = new Role('ROLE_TEACHER');
                    $user->setUsername($data[$i][0]);
                    $user->setPassword($data[$i][1]);
                    $user->setRoles([$role->getRole()]);
                    $teacher->setUserdb($user);

                    $entityManager->persist($teacher);

                }

            }
            return $data;
        });
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param $func
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     */
    private function addFromExcel(Request $request, EntityManagerInterface $entityManager, $func)
    {
        $entityManager->getConnection()->beginTransaction();
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

                $isSuccess = $func($entityManager, $file);

            }


            $entityManager->flush();
            $entityManager->getConnection()->commit();


            $response = new Response(json_encode(['ok' => $isSuccess], JSON_UNESCAPED_UNICODE));
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
        }
    }

    /**
     * @Route("/admin/class/add", name="admin_class_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     */

    public function addClass(Request $request, EntityManagerInterface $entityManager)
    {


        try {
            return $this->addFromExcel($request, $entityManager, function ($entityManager, $file) {

                $data = Excel::readSubjectExcel($file);

                if ($data) {


                    // class
                    $class = new ClassSubject();
                    $class->setIdclass($data[Excel::SubSubjectId]);
                    $class->setIdsubject($data[Excel::SubjectId]);
                    $class->setLocation($data[Excel::LectureLocation]);
                    $class->setNumberlesson($data[Excel::TinChi]);
                    $class->setNamesubject($data[Excel::SubjectName]);


                    // find teacher
                    $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['idteacher' => $data[Excel::TeacherId]]);

                    if ($teacher) {
                        $class->setTeacher($teacher);
                        $studentData = $data['data'];

                        foreach ($studentData as $value) {
                            // student
                            $student = new Student();
                            $student->setIdstudent($value[0]);
                            $student->setFullname($value[1]);
                            $student->setCourse($value[3]);
                            $student->setVnuemail($value[0] . '@vnu.edu.vn');

                            // find if this student have account
                            $userStudent = $entityManager->getRepository(User::class)->findOneBy(['username' => $student->getIdstudent()]);


                            if ($userStudent) {

                            } else {
                                // not had account then add account
                                $user = new User();
                                $user->setUsername($student->getIdstudent());
                                $user->setPassword(SRCConfig::DEFAULT_PASSWORD);
                                $user->setRoles([(new Role('ROLE_STUDENT'))->getRole()]);
                                $student->setIduserdb($user);
                            }

                            // add formsurvey
                            $surveyForm = new SurveyForm();
                            $surveyForm->setContent([]);
                            $surveyForm->addClassSubject($class);
                            $surveyForm->addStudent($student);
                            $entityManager->persist($class);
                            $entityManager->persist($student);
                            $entityManager->persist($surveyForm);

                        }


                    } else {
                        throw new NotFoundTeacherException();
                    }


                    $isSuccess = true;


                }
                return $isSuccess;
            });
        } catch (NotFoundTeacherException $e) {
            $response = new Response(json_encode(['ok' => "false", 'message' => 'NotFoundTeacherException '], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    }

    // one

    /**
     * @Route("/admin/student/delete", name="admin_student_delete")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function deleteStudent(Request $request, EntityManagerInterface $entityManager)
    {
        $response = new Response();
        try {
            if (!$request->request->has('id')) {
                throw new BadRequestHttpException();
            }
            return $this->verifyTemplate($request, $entityManager, function ($request, $entityManager) {

                $student = $entityManager->getRepository(Student::class)->findOneBy(['idstudent' => $request->request->get('id')]);
                if (!$student) {
                    throw new NotFoundException();
                }
                $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $student->getIduserdb()]);
                if(!$user) {
                    throw new NotFoundException();
                }
                $entityManager->remove($student);
                $entityManager->remove($user);
                $entityManager->flush();
                $entityManager->getConnection()->commit();
                return true;
            });
        } catch (BadRequestHttpException $e) {
            $response->setContent(json_encode(['ok' => 'false', 'message' => 'BadRequestHttpException'], JSON_UNESCAPED_UNICODE));
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (NotFoundException $e) {
            $response->setContent(json_encode(['ok' => 'false', 'message' => 'NotFoundException'], JSON_UNESCAPED_UNICODE));
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


    }

    /**
     * @Route("/admin/teacher/delete", name="admin_teacher_delete")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function deleteTeacher(Request $request, EntityManagerInterface $entityManager)
    {
        $response = new Response();
        try {
            if (!$request->request->has('id')) {
                throw new BadRequestHttpException();
            }
            return $this->verifyTemplate($request, $entityManager, function ($request, $entityManager) {

                $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['idteacher' => $request->request->get('id')]);
                if (!$teacher) {
                    throw new NotFoundException();
                }
                $user = $teacher->getUserdb();
//                if(!$user) {
//                    throw new NotFoundException();
//                }
                $entityManager->remove($teacher);
                $entityManager->remove($user);
                $entityManager->flush();
                $entityManager->getConnection()->commit();
                return true;
            });
        } catch (BadRequestHttpException $e) {
            $response->setContent(json_encode(['ok' => 'false', 'message' => 'BadRequestHttpException'], JSON_UNESCAPED_UNICODE));
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (NotFoundException $e) {
            $response->setContent(json_encode(['ok' => 'false', 'message' => 'NotFoundException'], JSON_UNESCAPED_UNICODE));
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


    }
    private function verifyTemplate(Request $request, EntityManagerInterface $entityManager, $func)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, AdminController::$role);
            $isSuccess = false;

            $isSuccess = $func($request, $entityManager);

            $response = new Response(json_encode(['ok' => $isSuccess], JSON_UNESCAPED_UNICODE));
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
    }







}
