<?php
declare(strict_types=1);

namespace App\Dto;

use App\Interface\DtoInterface;
use App\Trait\DtoMapperTrait;
use Symfony\Component\Validator\Constraints\NotNull;

class UserValidateAccountDto implements DtoInterface {
    use DtoMapperTrait;
    #[NotNull]
    public string $token;

    #[NotNull]
    public string $uuid;
}
