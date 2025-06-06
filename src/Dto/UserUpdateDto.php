<?php

namespace App\Dto;


use App\Interface\DtoInterface;
use App\Trait\DtoMapperTrait;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class UserUpdateDto implements DtoInterface {
    use DtoMapperTrait;
    #[Length(max: 128)]
    #[Email]
    public string $email;
    #[Length(min: 6, max: 128)]
    public string $password;
    public \DateTimeImmutable $updatedAt;
}
