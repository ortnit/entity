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
     * @param $key
     *
     * @return Entity
     */
    public function load($key): Entity;
}
