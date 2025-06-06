<?php
declare(strict_types=1);

namespace App\Trait;

trait DtoMapperTrait {

    public function toDto(array $data): object
    {
        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if (property_exists($this, 'createdAt') && empty($this->createdAt) ) {
            $this->createdAt = new \DateTimeImmutable('now');
        }

        if (property_exists($this, 'updatedAt') && empty($this->updatedAt) ) {
            $this->updatedAt = new \DateTimeImmutable('now');
        }

        return $this;
    }
}
