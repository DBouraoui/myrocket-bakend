<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UserCreateDto;
use App\Dto\UserForgetPasswordEmailSendingDto;
use App\Dto\UserForgetPasswordResetDto;
use App\Dto\UserUpdateDto;
use App\Dto\UserValidateAccountDto;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Interface\DtoInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Uid\Uuid;


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

    public function deleteUser(User $user): void {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function validateAccountUser(UserValidateAccountDto $userValidateAccountDto): User {
        $this->validateDTO($userValidateAccountDto);

        $user = $this->userRepository->findOneBy(['uuid' => $userValidateAccountDto->uuid]);

        if (empty($user)) {
            throw new \Exception("User identifiant invalide");
        }

        if ($user->getToken() !== $userValidateAccountDto->token) {
            throw new \Exception("Token is not valide");
        }

        if ($user->getTokenExpirationAt() < new \DateTimeImmutable()) {
            $user = $this->regenerateTokenUser($user);
            throw new \Exception("Token expired, a new token was genereted");
        }

        $user = $this->userFactory->activateAccount($user);

        $this->entityManager->flush();

        return $user;
    }

    public function forgetPasswordEmailSending(UserForgetPasswordEmailSendingDto $userDto): void {
        $this->validateDTO($userDto);

       $user = $this->userRepository->findOneBy(['email' => $userDto->email]);

       if (empty($user)) {
           throw new \Exception("Email inconnue");
       }

       if (!empty($user->getToken())) {
           Throw new \Exception("Token is already used");
       }

        $user =$this->userFactory->forgetPasswordeEmailSending($user);

        //TODO -> sending email here with new token and expiration date

       $this->entityManager->flush();
    }

    public function resetPasswordForget(UserForgetPasswordResetDto $userDto): User {
        $this->validateDTO($userDto);

        $user = $this->userRepository->findOneBy(['uuid' => $userDto->uuid]);

        if (empty($user)) {
            throw new \Exception("User identifiant invalide");
        }

        if ($user->getToken() !== $userDto->token) {
            throw new \Exception("Token is not valide");
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $userDto->password));
        $this->userFactory->activateAccount($user);

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

    private function regenerateTokenUser(User $user): User {
        $user->setToken(Uuid::v4()->toRfc4122());
        $user->setTokenExpirationAt(new \DateTimeImmutable("+1 day"));

        $this->entityManager->flush();

        return $user;
    }
}
