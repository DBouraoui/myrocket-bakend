<?php
declare(strict_types=1);

namespace App\Dto;

use App\Interface\DtoInterface;
use App\Trait\DtoMapperTrait;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserForgetPasswordResetDto implements DtoInterface
{
    use DtoMapperTrait;
    #[NotBlank]
    #[NotNull]
    public string $token;
    #[NotBlank]
    #[NotNull]
    public string $uuid;

    #[NotNull]
    #[NotBlank]
    #[Length(min:6)]
    public string $password;
}
