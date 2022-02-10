<?php

declare(strict_types=1);

namespace Test;

use Ortnit\Entity\AbstractEntityStore;
use Ortnit\Entity\Entity;
use Ortnit\Entity\Exception\EntityArgumentException;
use PHPUnit\Framework\TestCase;

class AbstractEntityStoreTest extends TestCase
{
    /**
     * @return AbstractEntityStore
     */
    public function getInstanceEntityStore(): AbstractEntityStore
    {
        $mock = $this->getMockForAbstractClass(AbstractEntityStore::class);
        $this->assertInstanceOf(AbstractEntityStore::class, $mock);

        return $mock;
    }

    public function getInstanceEntity(): Entity
    {
        $this->getMockForAbstractClass(Entity::class);
        $mock = $this->getMockBuilder(Entity::class)
            ->setMockClassName('TestEntity')
            ->getMockForAbstractClass();
        $this->assertInstanceOf(Entity::class, $mock);

        return $mock;
    }

    /**
     */
    public function testSetEntityName()
    {
        $mock = $this->getInstanceEntityStore();

        $mock->setEntityClassName(Entity::class);
        $this->assertIsString($mock->getEntityClassName());
        $this->assertEquals(Entity::class, $mock->getEntityClassName());
    }

    /**
     */
    public function testIsEntity()
    {
        $mock = $this->getInstanceEntityStore();
        $entity = new class extends Entity {
        };

        $this->assertTrue($mock->isEntity($entity));

        $mock->setEntityClassName(get_class($entity));
        $this->assertTrue($mock->isEntity($entity));

        $baseEntity = $this->getMockForAbstractClass(Entity::class);
        $this->assertFalse($mock->isEntity($baseEntity));
    }

    /**
     * @throws EntityArgumentException
     */
    public function testAssert()
    {
        $mock = $this->getInstanceEntityStore();
        $baseEntity = $this->getMockForAbstractClass(Entity::class);
        $entity = new class extends Entity {
        };

        $mock->setEntityClassName(get_class($entity));
        $mock->assertEntity($entity);
        $this->assertFalse($mock->isEntity($baseEntity));

        $this->expectException(EntityArgumentException::class);

        $mock->assertEntity($baseEntity);
    }
}
