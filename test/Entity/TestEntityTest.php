<?php

namespace Test\Entity;

use Ortnit\Entity\Entity;
use Ortnit\Entity\TestEntity;
use PHPUnit\Framework\TestCase;

class TestEntityTest extends TestCase
{
    /** @test */
    public function create_a_test_entity()
    {
        $entity = new TestEntity();
        $this->assertInstanceOf(Entity::class, $entity);
    }
}
