<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestStudentController extends AbstractController
{
    /**
     * @Route("/student", name="student")
     */
    public function index(): Response
    {
        return $this->render('rest_student/student.html.twig');
    }


    /**
     * @Route("/rest/student", name="rest_student")
     * @param Request $request
     * @return Response
     */
    public function createStudent(Request $request): Response{

        $entityManager = $this->getDoctrine()->getManager();

        $student = new Student();
        $student->setFirstName($request->get('firstName'));
        $student->setLastName($request->get('lastName'));
        $student->setEmail($request->get('email'));

        $street = $request->get('street');
        $postCode = $request->get('postCode');
        $city = $request->get('city');
        $country = $request->get('country');

        $address = new Address($street, $postCode, $city, $country);

        $student->setAddress($address);

        $entityManager->persist($student);
        $entityManager->flush();
        return $this->render('rest_student/student.html.twig');
    }


    /**
     * @param StudentRepository $studentRepository
     * @return Response
     * @Route("/student/result", name="posts", methods={"GET"})
     */
    public function getStudents(StudentRepository  $studentRepository){
        $data = $studentRepository->findAll();
        var_dump($data);
        return $this->render('rest_student/result.html.twig', [
           'students' => $data
        ]);
    }


    /**
     * @param StudentRepository $studentRepository
     * @param $id
     * @return Response
     * @Route("/student/{id}", name="posts_get", methods={"GET"})
     */
    public function getStudent(StudentRepository $studentRepository, $id){
        $data = $studentRepository->find($id);

       var_dump($data);
        return $this->render('rest_student/result.html.twig', [
            'students' => $data
        ]);
    }





    //============================================================//

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param StudentRepository $studentRepository
     * @param $id
     * @return Response
     * @Route("update/studet/{id}", name="student_put", methods={"PUT"})
     */
    public function updateStudent(Request $request, EntityManagerInterface $entityManager, StudentRepository $studentRepository, $id) : Response
    {
        try{
            $student = $studentRepository->find($id);

            if (!$student){
                $data = [
                    'status' => 404,
                    'errors' => "Post not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('firstName') || !$request->request->get('lastName')){
                throw new \Exception();
            }

            $student->setFirstName($request->get('firstName'));
            $student->setLastName($request->get('lastName'));
            $student->setEmail($request->get('email'));

            $street = $request->get('street');
            $postCode = $request->get('postCode');
            $city = $request->get('city');
            $country = $request->get('country');

            $address = new Address($street, $postCode, $city, $country);

            $student->setAddress($address);
            $entityManager->flush();

            var_dump($student);
            return $this->render('rest_student/result.html.twig', [
                'students' => $student
            ]);
        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }


    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response(array $data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}
