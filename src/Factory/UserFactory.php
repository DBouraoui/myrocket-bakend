<?php
declare(strict_types=1);

namespace App\Factory;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Interface\FactoryInterface;
use App\Trait\FactoryMapperTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory implements FactoryInterface {
    use FactoryMapperTrait;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function create(UserCreateDto $dto): Object {
        $user = new User();
        $user = $this->mapDtoToEntity($dto, $user);

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

        return $user;
    }

    public function update(UserUpdateDto $userUpdateDto, User $user): object
    {
        $user = $this->mapDtoToEntity($userUpdateDto, $user);

        return $user;

    }
}
