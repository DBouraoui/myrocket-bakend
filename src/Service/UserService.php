<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Interface\DtoInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class UserService {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private ValidatorInterface     $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private UserFactory           $userFactory,
    ) {}

    public function createUser(UserCreateDto $userCreateDto): User {
        $this->validateDTO($userCreateDto);

        $this->checkEmailUser($userCreateDto->email);

        $user = $this->userFactory->create($userCreateDto);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(UserUpdateDto $userUpdateDto, $user): User {
        $this->validateDTO($userUpdateDto);

        if (!empty($userUpdateDto->email) && $user->getEmail() !== $userUpdateDto->email) {
            $this->checkEmailUser($userUpdateDto->email);
        }

        if (!empty($userUpdateDto->password)) {
            $userUpdateDto->password = $this->passwordHasher->hashPassword($user, $userUpdateDto->password);
        }

        $this->userFactory->update($userUpdateDto, $user);

        $this->entityManager->flush();

        return $user;
    }

    private function checkEmailUser(string $email): void {
        $email = $this->userRepository->findBy(['email' => $email]);

        if (!empty($email)) {
            Throw new \Exception('Email already exists');
        }
    }

    private function validateDTO(DtoInterface $dto): bool
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            throw new ValidationFailedException($dto, $violations);
        }

        return true;
    }
}
