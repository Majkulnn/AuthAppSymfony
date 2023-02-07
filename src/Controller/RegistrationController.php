<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
    /**
     * List the rewards of the specified user.
     *
     * This call takes into account all confirmed awards, but not pending or refused awards.
     *
     * @Route("/api/registration", methods={"POST"})
     * @OA\Post(
     *     description="Register user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *             property="name",
     *             type="string"
     *             ),
     *              @OA\Property(
     *             property="email",
     *             type="email|string"
     *              ),
     *              @OA\Property(
     *             property="password",
     *             type="string"
     *              ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Created",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      description="status",
     *                  ),
     *              ),
     *         ),
     *     ),
     * )
     * @OA\Tag(name="User")
     */

class RegistrationController extends AbstractController
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $passwordHasher
    ){}
    #[\OpenApi\Attributes\Post  ]

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
