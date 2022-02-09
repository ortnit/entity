<?php

declare(strict_types=1);

namespace Ortnit\Entity;

use Ortnit\Entity\Exception\EntityArgumentException;

/**
 * Class AbstractEntityStore
 *
 * @package GPortal\Lib\Entity\Exception
 */
abstract class AbstractEntityStore implements EntityStoreInterface
{
    protected string $entityClassName;

    /**
     * @param Entity $entity
     */
    abstract public function store(Entity $entity): void;

    /**
     * @param $key
     *
     * @return Entity
     */
    abstract public function load($key): Entity;

    /**
     * @param string $className
     */
    public function setEntityClassName(string $className): void
    {
        $this->entityClassName = $className;
    }

    /**
     * @return string
     */
    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    /**
     * @param Entity $entity
     *
     * @return bool
     */
    public function isEntity(Entity $entity): bool
    {
        if (!isset($this->entityClassName)) {
            return true;
        }

        if ($entity instanceof $this->entityClassName) {
            return true;
        }

        return false;
    }

    /**
     * @param Entity $entity
     *
     * @throws EntityArgumentException
     */
    public function assertEntity(Entity $entity): void
    {
        if (!$this->isEntity($entity)) {
            throw new EntityArgumentException('wrong class name');
        }
    }
}
