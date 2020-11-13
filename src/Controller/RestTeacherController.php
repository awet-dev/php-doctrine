<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Teacher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RestTeacherController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response {
        return $this->render('rest_teacher/index.html.twig');
    }


    /**
     * @Route("/rest/teacher", name="rest_teacher")
     * @param Request $request
     * @return Response
     */
    public function createTeacher(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $teacher = new Teacher();
        $teacher->setName($request->get('name'));
        $teacher->setEmail($request->get('email'));

        $street = $request->get('street');
        $postCode = $request->get('postCode');
        $city = $request->get('city');
        $country = $request->get('country');

        $address = new Address($street, $postCode, $city, $country);

        $teacher->setAddress($address);

        $entityManager->persist($teacher);
        $entityManager->flush();
        return $this->render('rest_teacher/index.html.twig');
    }





    // ============================================================//

}

