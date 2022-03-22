<?php

declare(strict_types=1);

namespace Ortnit\Entity;

interface EntityStoreInterface
{
    /**
     * @param Entity $entity
     */
    public function store(Entity $entity): void;

    /**
     * @param mixed ...$params
     * @return Entity
     */
    public function load(...$params): Entity;
}
