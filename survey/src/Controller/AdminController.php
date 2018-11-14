<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Role\Role;

class AdminController extends AbstractController
{
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

        return new Response('Saved new product with id '.$adminUser->getId());

    }

    public function start() {

    }
}
