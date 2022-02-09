<?php

declare(strict_types=1);

namespace Test;

use Ortnit\Entity\Entity;
use Ortnit\Entity\EntityField;
use Ortnit\Entity\EntityStoreInterface;
use Ortnit\Entity\Exception\EntityFieldValidationException;
use Ortnit\Entity\Exception\EntityNotFoundException;
use Ortnit\Json\Exception\JsonException;
use Ortnit\Json\Json;
use Ortnit\Validator\Rule\ArrayType;
use Ortnit\Validator\Rule\IntType;
use Ortnit\Validator\Rule\StringType;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    protected array $entityFields = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->entityFields = [
            'state' => [
                'default' => 0,
                'rule' => new IntType(),
            ],
            'id' => [
                'default' => 0,
                'rule' => new IntType(),
            ],
            'name' => [
                'default' => null,
                'rule' => new StringType(),
            ],
            'list' => [
                'default' => [],
                'rule' => new ArrayType(),
            ],
        ];
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity
    {
        $entity = $this->getMockForAbstractClass(Entity::class);
        foreach ($this->entityFields as $name => $entityField) {
            $entity->addField(new EntityField($name, $entityField['default'], $entityField['rule']));
        }

        return $entity;
    }

    /**
     * @return EntityStoreInterface
     */
    public function getEntityStore(): EntityStoreInterface
    {
        return $this->getMockBuilder(EntityStoreInterface::class)->getMock();
    }

    /**
     */
    public function testIfEntityInstance()
    {
        $entity = $this->getEntity();
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertTrue($entity->isField('state'));
        $this->assertFalse($entity->isField('rocket'));
    }

    /**
     */
    public function testIfCanSetValue()
    {
        $entity = $this->getEntity();

        /**
         * setting a known field with a interger should work
         */
        $value = 2;
        $entity->state = $value;
        $this->assertEquals($value, $entity->state);
    }

    /**
     */
    public function testSetNonInt()
    {
        $entity = $this->getEntity();

        $this->expectException(EntityFieldValidationException::class);

        /**
         * using a float should not work
         */
        $value = 2.5;
        $entity->state = $value;
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetUnknownField()
    {
        $entity = $this->getEntity();

        $this->expectException(EntityNotFoundException::class);

        $this->assertNull($entity->rocket);
        /**
         * setting a unknown field should not work
         */
        $value = 2;
        $entity->rocket = $value;
    }

    /**
     * @throws \ReflectionException
     */
    public function testDirtyStuff()
    {
        $entity = $this->getEntity();

        $name = 'state';
        $value1 = 1;
        $value2 = 2;
        $this->assertTrue($entity->isField($name));

        /**
         * setting values to entity
         */
        $entity->$name = $value1;
        $this->assertEquals($value1, $entity->$name);

        $entity->$name = $value2;
        $this->assertEquals($value2, $entity->$name);

        /**
         * testing the dirty methods
         */
        $this->assertTrue($entity->isDirty());
        $fields = $entity->getDirtyFields();
        $this->assertCount(1, $fields);

        $values = $entity->getDirtyFieldValues();
        $this->assertCount(1, $values);
        $this->assertEquals($value2, $values[$name]);

        /**
         * reset dirty
         */
        $entity->resetDirty();

        $this->assertFalse($entity->isDirty());
        $fields = $entity->getDirtyFields();
        $this->assertCount(0, $fields);

        $values = $entity->getDirtyFieldValues();
        $this->assertCount(0, $values);
    }

    /**
     */
    public function testSetFromArray()
    {
        $entity = $this->getEntity();
        $fields = [
            'id' => 1,
            'state' => 2,
        ];

        $entity->setFromArray($fields);
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $entity->$key);
        }
    }

    /**
     */
    public function testSetFromArrayNoField()
    {
        $entity = $this->getEntity();
        $fields = [
            'id' => 1,
            'state' => 2,
            'rocket' => 3,
        ];
        $this->expectException(EntityNotFoundException::class);

        $entity->setFromArray($fields);
    }

    /**
     */
    public function testDebugInfo()
    {
        $entity = $this->getEntity();
        /**
         * also tests toArray(), since __debugInfo() just wraps toArray()
         */
        $fields = $entity->__debugInfo();

        foreach ($fields as $key => $value) {
            $this->assertTrue($entity->isField($key));
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetNoEntityStore()
    {
        $entity = $this->getEntity();

        $this->assertNull($entity->getEntityStore());
    }

    /**
     * @throws \ReflectionException
     */
    public function testFieldIsset()
    {
        $entity = $this->getEntity();

        $this->assertFalse(isset($entity->anyField));
        $this->assertTrue(isset($entity->state));

        $this->assertFalse(isset($entity->name)); // value is null
        $entity->name = 'test123';
        $this->assertTrue(isset($entity->name)); // value is not null
    }

    /**
     * @throws \ReflectionException
     */
    public function testStoreInStore()
    {
        $entity = $this->getEntity();
        $store = $this->getEntityStore();

        $entity->store();
        $entity->setEntityStore($store);
        $entity->store();

        $this->assertInstanceOf(EntityStoreInterface::class, $entity->getEntityStore());
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsEmpty()
    {
        $entity = $this->getEntity();

        $this->assertEmpty($entity->name);
        $this->assertEmpty($entity->anyField);
        $this->assertEmpty($entity->state);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsNotEmpty()
    {
        $entity = $this->getEntity();

        $entity->name = 'test123';
        $entity->state = 3;

        $this->assertNotEmpty($entity->name);
        $this->assertNotEmpty($entity->state);
    }

    /**
     * @throws \ReflectionException
     */
    public function testPopulate()
    {
        $entity = $this->getEntity();
        $fields = [
            'id' => 1,
            'state' => 2,
        ];

        $entity->populate($fields);
        foreach ($fields as $key => $value) {
            $this->assertEquals($value, $entity->$key);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetEntityField()
    {
        $entity = $this->getEntity();

        foreach ($this->entityFields as $name => $values) {
            $this->assertInstanceOf(EntityField::class, $entity->getEntityField($name));
        }

        $this->assertNotInstanceOf(EntityField::class, $entity->getEntityField('noFieldName'));
    }

    /**
     * @throws \ReflectionException
     * @throws JsonException
     */
    public function testJsonSerialize()
    {
        $entity = $this->getEntity();

        $encodeToJson = Json::encode($entity);
        $this->assertIsString($encodeToJson);

        $decodeFromJson = Json::decode($encodeToJson);
        $this->assertIsArray($decodeFromJson);

        $this->assertEquals($entity->toArray(), $decodeFromJson);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsFieldDirty()
    {
        $entity = $this->getEntity();

        $this->assertEmpty($entity->getDirtyFields());

        $entity->id = 5;

        $this->assertTrue($entity->isFieldDirty('id'));
        $this->assertFalse($entity->isFieldDirty('state'));
        $this->assertFalse($entity->isFieldDirty('name'));
    }

    /**
     * trying to access a field which is not set, and catch the exception thrown
     *
     * @throws \ReflectionException
     */
    public function testIsUnknownFieldDirty()
    {
        $this->expectException(EntityNotFoundException::class);
        $entity = $this->getEntity();

        $this->assertEmpty($entity->getDirtyFields());

        $entity->isFieldDirty('unknown');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldNames()
    {
        $entity = $this->getEntity();

        $fieldNames = array_keys($this->entityFields);

        $this->assertEquals($fieldNames, $entity->getFieldNames());
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function testChangeArrayMakesFieldDirty()
    {
        $entity = $this->getEntity();

        $key = 'newKey';
        $value = 'new value';

        $entity->list = [
            $key => $value,
        ];

        $this->assertTrue($entity->isDirty());
    }
}
