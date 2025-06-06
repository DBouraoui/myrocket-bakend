<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\UserCreateDto;
use App\Dto\UserForgetPasswordEmailSendingDto;
use App\Dto\UserForgetPasswordResetDto;
use App\Dto\UserUpdateDto;
use App\Dto\UserValidateAccountDto;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * User controller for different user management actions
 */
#[Route('/api/user', name: 'api_user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Create a new user
     */
    #[Route(name: '_post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserCreateDto();
            $userDto->toDto($data);

            $this->userService->createUser($userDto);

            return $this->json([
                'success' => true,
                'message' => 'user created'
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update user information
     */
    #[Route(name: '_put', methods: ['PUT'])]
    public function update(#[CurrentUser] User $user, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserUpdateDto();
            $userDto->toDto($data);

            $this->userService->updateUser($userDto, $user);

            return $this->json([
                'success' => true,
                'message' => 'user updated'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete user account
     */
    #[Route(name: '_delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] User $user): JsonResponse
    {
        try {
            $this->userService->deleteUser($user);

            return $this->json([
                'success' => true,
                'message' => 'user deleted'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validate user account with token
     * Requires: token & uuid
     */
    #[Route(name: '_patch', methods: ['PATCH'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function patch(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserValidateAccountDto();
            $userDto->toDto($data);

            $this->userService->validateAccountUser($userDto);

            return $this->json([
                'success' => true,
                'message' => 'user account activated'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send email with token for password reset
     * Requires: email
     */
    #[Route(path: '/forget-password', name: '_patch_forget_password_email_sending', methods: ['PATCH'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function forgetPasswordEmailSending(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userDto = new UserForgetPasswordEmailSendingDto();
            $userDto->toDto($data);

            $this->userService->forgetPasswordEmailSending($userDto);

            return $this->json([
                'success' => true,
                'message' => 'email sending for create new password'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reset forgotten password
     * Requires: token & uuid & password
     */
    #[Route(path: '/reset-password', name: '_patch_forget_password_reset', methods: ['PATCH'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function forgetPasswordReset(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data['token'] = $request->query->get('token');
            $data['uuid'] = $request->query->get('uuid');

            $userDto = new UserForgetPasswordResetDto();
            $userDto->toDto($data);

            $this->userService->resetPasswordForget($userDto);

            return $this->json([
                'success' => true,
                'message' => 'email sending for create new password'
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
