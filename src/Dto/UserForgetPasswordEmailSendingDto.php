<?php
declare(strict_types=1);

namespace App\Dto;

use App\Interface\DtoInterface;
use App\Trait\DtoMapperTrait;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class UserForgetPasswordEmailSendingDto implements DtoInterface
{
    use DtoMapperTrait;

    #[Email]
    #[NotNull]
    #[NotBlank]
    public string $email;
}
