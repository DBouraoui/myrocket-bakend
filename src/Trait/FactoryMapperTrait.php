<?php
declare(strict_types=1);

namespace App\Trait;

use App\Interface\DtoInterface;
use Doctrine\ORM\Mapping\Entity;

trait FactoryMapperTrait
{
    public function mapDtoToEntity(DtoInterface $dto, Object $entity): object
    {
        $reflectionEntity = new \ReflectionClass($entity);
        if (!$reflectionEntity->getAttributes(Entity::class)) {
            throw new \InvalidArgumentException('The provided entity is not a valid Doctrine entity.');
        }

        $reflexionClass = new \ReflectionClass(get_class($dto));
        $DtoMethods = $reflexionClass->getProperties();

        foreach ($DtoMethods as $method) {
            $methodName = $method->getName();

                $setter = 'set' . \ucfirst($methodName);
            if (method_exists($entity, $setter)) {
                $entity->{$setter}($dto->{$methodName});
            }
        }
        return $entity;
    }
}
