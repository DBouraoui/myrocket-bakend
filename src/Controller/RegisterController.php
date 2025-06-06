<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


#[Route('/api/user', name: 'api_user')]
final class RegisterController extends AbstractController
{

    public function __construct(
        private readonly UserService $userService
    ) {}


    #[Route( name: '_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserCreateDto();
            $userDto->toDto($data);

            $this->userService->createUser($userDto);

            return $this->json(['success'=>true, 'message'=>'user created'], Response::HTTP_CREATED);
        } catch(\Throwable $e) {
            return $this->json([
                "success" => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route( name: '_put', methods: ['PUT'])]
    public function update(#[CurrentUser]User $user, Request $request): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserUpdateDto();
            $userDto->toDto($data);

            $this->userService->updateUser($userDto, $user);

            return $this->json([
                "success" => true,
                "message" => "user updated",
            ], Response::HTTP_OK);
        } catch(\Throwable $e) {
            return $this->json([
                "success" => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
