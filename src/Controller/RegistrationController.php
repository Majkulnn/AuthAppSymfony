<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */

class RegistrationController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $passwordHasher
    ){}

    #[Route('/registration', name: 'registration', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(""));

        if ($this->userRepository->findOneBy(["email"=>(string)$data->email])){
            return $this->json("Email is occupied");
        }

        $user = new User();
        $user->setEmail((string)$data->email);
        $user->setPassword(($this->passwordHasher->hashPassword($user,(string)$data->password)));
        $user->setUsername((string)$data->name);

        $this->userRepository->save($user,true);

        return $this->json($user,201);
    }
}
