<?php

declare(strict_types=1);

namespace Test;

use Ortnit\Entity\EntityField;
use Ortnit\Entity\Exception\EntityFieldValidationException;
use Ortnit\Validator\Rule\IntType;
use PHPUnit\Framework\TestCase;

class EntityFieldTest extends TestCase
{
    public function testEntityField()
    {
        $name = 'state';
        $default = 0;
        $field = new EntityField($name, $default, new IntType());
        $this->assertInstanceOf(EntityField::class, $field);
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($default, $field->getDefault());
    }

    /**
     * @throws EntityFieldValidationException
     */
    public function testSettingAndValidation()
    {
        $name = 'state';
        $default = 0;
        $field = new EntityField($name, $default, new IntType());

        $value = 2;
        $this->assertFalse($field->isDirty());
        $this->assertEquals($default, $field->getOldValue());
        $field->setValue($value);
        $this->assertEquals($value, $field->getValue());
        $this->assertTrue($field->isDirty());
        $field->resetDirty();
        $this->assertFalse($field->isDirty());
        $this->assertEquals($value, $field->getOldValue());

        $value = 2.5;
        $this->expectException(EntityFieldValidationException::class);
        $field->setValue($value);
    }
}
