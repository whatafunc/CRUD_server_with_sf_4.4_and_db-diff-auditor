<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractFOSRestController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/users", methods={"GET"})
     * @return View
     */
    public function getUsersAction(): View
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        return $this->view($users, Response::HTTP_OK);
    }

    /**
     * @Route("/api/users", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postUsersAction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['username']) || empty($data['email'])) {
            return new JsonResponse(['message' => 'Username and email are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User($data['username'], $data['email']);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userData = ['id' => $user->getId(), 'username' => $user->getUsername(), 'email' => $user->getEmail()];

        return new JsonResponse($userData, Response::HTTP_CREATED);
    }
}
