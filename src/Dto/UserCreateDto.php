<?php

declare(strict_types=1);

namespace App\Dto;

use App\Interface\DtoInterface;
use App\Trait\DtoMapperTrait;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Uid\Uuid;

class UserCreateDto implements DtoInterface {
    use DtoMapperTrait;

    public ?string $id;
    #[Length(max: 128)]
    #[Email]
    public string $email;
    public ?string $uuid;
    #[Length(min: 6, max: 128)]
    public string $password;
    public array $roles = ["ROLE_USER"];
    public \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $updatedAt;
    public string $token;
    public \DateTimeImmutable $tokenExpirationAt;

    public function __construct() {
        $this->token = Uuid::v4()->toRfc4122();
        $this->tokenExpirationAt = new \DateTimeImmutable("+1 day");
        $this->uuid = Uuid::v4()->toRfc4122();
    }
}
