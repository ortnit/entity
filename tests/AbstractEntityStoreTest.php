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
    public function getInstance(): AbstractEntityStore
    {
        $mock = $this->getMockForAbstractClass(AbstractEntityStore::class);
        $this->assertInstanceOf(AbstractEntityStore::class, $mock);

        return $mock;
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetEntityName()
    {
        $mock = $this->getInstance();

        //$mock->setEntityClassName($this->entityName);
        $this->assertIsString($mock->getEntityClassName());
        $this->assertEquals(Entity::class, $mock->getEntityClassName());
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsEntity()
    {
        $mock = $this->getInstance();
        $entity = new ServiceEntity();

        $this->assertTrue($mock->isEntity($entity));

        $mock->setEntityClassName(ServiceEntity::class);
        $this->assertTrue($mock->isEntity($entity));

        $baseEntity = $this->getMockForAbstractClass(Entity::class);
        $this->assertFalse($mock->isEntity($baseEntity));
    }

    /**
     * @throws \ReflectionException
     * @throws EntityArgumentException
     */
    public function testAssert()
    {
        $mock = $this->getInstance();
        $baseEntity = $this->getMockForAbstractClass(Entity::class);
        $entity = new ServiceEntity();

        $mock->setEntityClassName(ServiceEntity::class);
        $mock->assertEntity($entity);
        $this->assertFalse($mock->isEntity($baseEntity));

        $this->expectException(EntityArgumentException::class);

        $mock->assertEntity($baseEntity);
    }
}
