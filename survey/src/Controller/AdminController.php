<?php

namespace App\Controller;

use App\Config\SRCConfig;
use App\Entity\Admin;
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
use function Clue\StreamFilter\remove;
use Doctrine\DBAL\Driver\PDOException;
use exception\NotFoundClassIdInDataBase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use function Symfony\Component\DependencyInjection\Tests\Fixtures\factoryFunction;
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
        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/criterialLevel/setdefault", name="admin_criterialLevel_setdefault")
     */
    public function setDefaultCriterialLevels(Request $request, EntityManagerInterface $entityManager)
    {
        for ($i = 0; $i < count(SRCConfig::DEFAULT_FORM); ++$i) {
            $criterialLevel = new CriteriaLevel();
            $criterialLevel->setName(SRCConfig::DEFAULT_FORM[$i]);
            $entityManager->persist($criterialLevel);

            $criterialLevels[] = $criterialLevel;
        }
        $entityManager->flush();
        return new Response();
    }

    /**
     * @Route("/admin/admin/add", name="admin_add")
     */
    public function addAdmin()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $passwordEncoder = new PasswordEncoder();
        $adminUser = new User();
        $adminUser->setUsername('vanminh');
        $adminUser->setPassword($passwordEncoder->encode($adminUser, '12345'));

        $role = new Role('ROLE_ADMIN');
        $adminUser->setRoles([$role->getRole()]);

        $admin = new Admin();
        $admin->setFullname('Ta Van Minh');
        $admin->setUserdb($adminUser);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($adminUser);
        $entityManager->persist($admin);


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
                    $passwordEncoder = new PasswordEncoder();
                    $isSuccess = true;
                    for ($i = 0; $i < count($data); ++$i) {

                        $student = new Student();
                        $student->setIdstudent($data[$i][0]);

                        $student->setFullname($data[$i][2]);
                        $student->setVnuemail($data[$i][3]);
                        $student->setCourse($data[$i][4]);

                        $userStudent = new User();
                        $userStudent->setUsername($student->getIdstudent());
                        $userStudent->setPassword($passwordEncoder->encode($userStudent,
                            $data[$i][1]));

                        $role = new Role('ROLE_STUDENT');
                        $userStudent->setRoles([$role->getRole()]);
                        $entityManager->persist($userStudent);
                        $entityManager->flush();
                        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $userStudent->getUsername()]);
                        $student->setIduserdb($user);
                        $student->deleteAllSurveyForm();
                        $entityManager->persist($student);

                    }
                    $entityManager->flush();
                }


            }
            $response = new Response(json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE));
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
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function addTeachers(Request $request, EntityManagerInterface $entityManager)
    {
        return $this->addFromExcel($request, $entityManager, function ($entityManager, $file) {

            $data = Excel::readTeacherExcel($file);

            if ($data) {
                $passwordEncoder = new PasswordEncoder();

                $isSuccess = true;

                for ($i = 0; $i < count($data); ++$i) {

                    $teacher = new Teacher();

                    $teacher->setFullname($data[$i][2]);
                    $teacher->setVnuemail($data[$i][3]);
                    $teacher->setIdteacher($data[$i][4]);


                    $user = new User();
                    $role = new Role('ROLE_TEACHER');
                    $user->setUsername($data[$i][0]);
                    $user->setPassword($passwordEncoder->encode($user,
                        $data[$i][1]));
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
                unlink($file);
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
            $response = new Response(json_encode(['ok' => "false", 'route' => "login_form"], JSON_UNESCAPED_UNICODE));
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
        } catch (PDOException $e) {
            $response = new Response(json_encode(['ok' => "PDOException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
        } finally {
            $entityManager->getConnection()->close();
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
                $isSuccess = false;
                if ($data) {

                    $teacher = $entityManager->getRepository(Teacher::class)->findOneBy(['idteacher' => $data[Excel::TeacherId]]);
                    $class = null;
                    if ($teacher !== null) {
                        $class = $entityManager->getRepository(ClassSubject::class)->findOneBy(
                            ['idclass' => $data[Excel::SubSubjectId], 'teacher' => $teacher]);
                    }


                    if ($class === null) {
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
                            $passEncoder = new PasswordEncoder();
                            foreach ($studentData as $value) {

                                $student = $entityManager->getRepository(Student::class)->findOneBy(['idstudent' => $value[0]]);
                                // student
                                if ($student === null) {
                                    $student = new Student();
                                    $student->setIdstudent($value[0]);
                                    $student->setFullname($value[1]);
                                    $student->setCourse($value[3]);
                                    $student->setVnuemail($value[0] . '@vnu.edu.vn');
                                }


                                // find if this student have account
                                $userStudent = $entityManager->getRepository(User::class)->findOneBy(['username' => $student->getIdstudent()]);


                                if ($userStudent) {

                                } else {
                                    // not had account then add account
                                    $user = new User();
                                    $user->setUsername($student->getIdstudent());
                                    $user->setPassword($passEncoder->encode($user,SRCConfig::DEFAULT_PASSWORD));
                                    $user->setRoles([(new Role('ROLE_STUDENT'))->getRole()]);

                                    $student->setIduserdb($user);
                                    $entityManager->persist($user);
                                    $entityManager->persist($student);
                                }

                                // add formsurvey
                                $surveyForm = new SurveyForm();
                                $surveyForm->setContent([]);
                                $surveyForm->addClassSubject($class);
                                $surveyForm->addStudent($student);

                                $entityManager->persist($class);
                                $entityManager->persist($surveyForm);

                            }
                        } else {
                            throw new NotFoundTeacherException();
                        }
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
                if (!$user) {
                    throw new NotFoundException();
                }
                foreach ($student->getSurveyForms() as $surveyForm) {
                    $entityManager->remove($surveyForm);
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

                // delete class subject
                foreach ($teacher->getTeacher() as $class) {
                    $class->deleteSurveyForm($entityManager);
                }
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
            return $this->redirectToRoute('default');

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
     * @Route("/admin/students/getall", name="admin_students_getall")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getAllStudents(Request $request, EntityManagerInterface $entityManager)
    {

        $response = $this->verifyTemplateForGet($request, $entityManager, function ($request, $entityManager) {

            $student = $entityManager->getRepository(Student::class)->findAll();
            $retData = [];
            foreach ($student as $value) {
                $retData[] = ['idStudent' => $value->getIdstudent(),
                    'fullname' => $value->getFullname(),
                    'vnuemail' => $value->getVnuemail(),
                    'course' => $value->getCourse()];
            }
            return $retData;
        });

        return $response;

    }


    private function verifyTemplateForGet(Request $request, EntityManagerInterface $entityManager, $func)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, AdminController::$role);

            $data = $func($request, $entityManager);
            $response = new Response(json_encode(['ok' => 'true', 'data' => $data], JSON_UNESCAPED_UNICODE));
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
            $response = new Response(json_encode(['ok' => "false", 'route' => 'login_form'], JSON_UNESCAPED_UNICODE));
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
        }
    }


    /**
     * @Route("/admin/teachers/getall", name="admin_teachers_getall")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getAllTeachers(Request $request, EntityManagerInterface $entityManager)
    {

        $response = $this->verifyTemplateForGet($request, $entityManager, function ($request, $entityManager) {

            $teachers = $entityManager->getRepository(Teacher::class)->findAll();
            $retData = [];
            foreach ($teachers as $value) {
                $retData[] = ['idTeacher' => $value->getIdteacher(),
                    'fullname' => $value->getFullname(),
                    'vnuemail' => $value->getVnuemail()];
            }
            return $retData;
        });

        return $response;

    }

    /**
     * @Route("/admin/getProfile", name="admin_get_profile")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getProfile(Request $request, EntityManagerInterface $entityManager)
    {
        $response = $this->verifyTemplateForGet($request, $entityManager, function ($request, $entityManager) {

            $user = $entityManager->getRepository(User::class)->findOneBy(['jwt' => $request->request->get('jwt')]);
            $admin = $entityManager->getRepository(Admin::class)->findOneBy(['userdb' => $user]);
            $retData = [
                'username' => $user->getUsername(),
                'fullname' => $admin->getFullname()];
            return $retData;
        });

        return $response;
    }


    /**
     * @Route("/admin/classes/getall", name="admin_classes_getall")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getAllClasses(Request $request, EntityManagerInterface $entityManager)
    {

        $response = $this->verifyTemplateForGet($request, $entityManager, function ($request, $entityManager) {

            $classes = $entityManager->getRepository(ClassSubject::class)->findAll();
            $retData = [];
            foreach ($classes as $c) {
                $retData[] = $c->getFullInfo();
            }


            return $retData;
        });

        return $response;

    }

    /**
     * @Route("/admin/class/getresult", name="admin_classes_getResult")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     */

    public function getClassResult(Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->getConnection()->beginTransaction();
        try {
            Authenticator::verifyFor($request, $entityManager, AdminController::$role);

            if ($request->request->get('id') === null) {
                throw new BadRequestHttpException();
            }
            $class = $entityManager->getRepository(ClassSubject::class)->findOneBy(['idclass' => $request->request->get('id')]);
            if($class === null) {
                throw new NotFoundClassIdInDataBase();
            }


            // get criterialLevel
            $criterialLevels = $entityManager->getRepository(CriteriaLevel::class)->findAll();
            if ($criterialLevels === null || count($criterialLevels) === 0) {
                $criterialLevels = [];
                for ($i = 0; $i < count(SRCConfig::DEFAULT_FORM); ++$i) {
                    $criterialLevel = new CriteriaLevel();
                    $criterialLevel->setName(SRCConfig::DEFAULT_FORM[$i]);
                    $entityManager->persist($criterialLevel);

                    $criterialLevels[] = $criterialLevel;
                }
                $entityManager->flush();

                $criterialLevels = $entityManager->getRepository(CriteriaLevel::class)->findAll();
            }
            $appendix = CriteriaLevel::convertArrayCriterialLevelObjectsToArray($criterialLevels);

            $retData = $class->getStatistic($appendix);
            $retData['idClass'] = $class->getIdclass();
            $retData['nameSubject'] = $class->getNamesubject();
            $retData['teacher'] = $class->getTeacher()->getFullname();

            $entityManager->getConnection()->commit();

            $response = new Response(json_encode(['ok' => 'true', 'data' => ['class' => $retData, 'appendix' => $appendix]], JSON_UNESCAPED_UNICODE));
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
            $response = new Response(json_encode(['ok' => "false", 'route' => 'login_form'], JSON_UNESCAPED_UNICODE));
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
        } catch(BadRequestHttpException $e) {
            $response = new Response(json_encode(['ok' => "BadRequestHttpException"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch(NotFoundClassIdInDataBase $e) {
            $response = new Response(json_encode(['ok' => "NotFoundClassIdInDataBase"], JSON_UNESCAPED_UNICODE));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
        } finally {
            $entityManager->getConnection()->close();
        }

    }


    /**
     * @Route("/admin/class/delete", name="admin_class_delete")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function deleteClass(Request $request, EntityManagerInterface $entityManager)
    {
        $response = new Response();
        try {
            if (!$request->request->has('id')) {
                throw new BadRequestHttpException();
            }
            return $this->verifyTemplate($request, $entityManager, function ($request, $entityManager) {

                $class = $entityManager->getRepository(ClassSubject::class)->findOneBy(['idclass' => $request->request->get('id')]);
                if (!$class) {
                    throw new NotFoundException();
                }
                $class->deleteSurveyForm($entityManager);
                $entityManager->remove($class);
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
     * @Route("/admin/criterias/getall", name="admin_criterias_getall")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function getAllCriterias(Request $request, EntityManagerInterface $entityManager)
    {

        $response = $this->verifyTemplateForGet($request, $entityManager, function ($request, $entityManager) {

            $criterias = $entityManager->getRepository(CriteriaLevel::class)->findAll();
            $retData = [];
            foreach ($criterias as $value) {
                $retData[] = ['id' => $value->getId(),
                    'name' => $value->getName()];
            }
            return $retData;
        });

        return $response;

    }

    /**
     * @Route("/admin/criteria/delete", name="admin_criteria_delete")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */

    public function deleteCriteria(Request $request, EntityManagerInterface $entityManager)
    {
        $response = new Response();
        try {
            if (!$request->request->has('id')) {
                throw new BadRequestHttpException();
            }
            return $this->verifyTemplate($request, $entityManager, function ($request, $entityManager) {

                if(!$request->request->has('id')) {
                    throw new BadRequestHttpException();
                }
                $criteria = $entityManager->getRepository(CriteriaLevel::class)->findOneBy(['id' => $request->request->get('id')]);
                if (!$criteria) {
                    throw new NotFoundException();
                }
                $entityManager->remove($criteria);
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

}
