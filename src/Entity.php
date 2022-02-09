<?php

declare(strict_types=1);

namespace Ortnit\Entity;

use JsonSerializable;
use Ortnit\Entity\Exception\EntityFieldValidationException;
use Ortnit\Entity\Exception\EntityNotFoundException;

abstract class Entity implements JsonSerializable
{
    /**
     * @var EntityField[]
     */
    protected array $entityHiddenFields = [];

    /**
     * @var EntityStoreInterface
     */
    protected EntityStoreInterface $entityStore;

    /**
     * add a new field to the entity
     *
     * @param EntityField $field
     */
    public function addField(EntityField $field): void
    {
        $name = $field->getName();
        $this->entityHiddenFields[$name] = $field;
    }

    /**
     * @return array
     */
    public function getFieldNames(): array
    {
        return array_keys($this->entityHiddenFields);
    }

    /**
     * magic setter to set values to a field
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws EntityNotFoundException
     * @throws EntityFieldValidationException
     */
    public function __set(string $name, $value)
    {
        /** field could not be found */
        if (!$this->isField($name)) {
            throw new EntityNotFoundException('field not found: ' . $name);
        }

        $field = $this->entityHiddenFields[$name];
        $field->setValue($value);
    }

    /**
     * magic getter to a field
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!$this->isField($name)) {
            return null;
        }

        $field = $this->entityHiddenFields[$name];

        return $field->getValue();
    }

    /**
     * @param array $fields
     */
    public function populate(array $fields): void
    {
        $this->setFromArray($fields);
    }

    /**
     * lets you know if there are changed files inside the entity
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        foreach ($this->entityHiddenFields as $field) {
            if ($field->isDirty()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws EntityNotFoundException
     */
    public function isFieldDirty(string $name): bool
    {
        /** field could not be found */
        if (!$this->isField($name)) {
            throw new EntityNotFoundException('field not found: ' . $name);
        }

        return $this->entityHiddenFields[$name]->isDirty();
    }

    /**
     * returns a list of changed fields
     *
     * @return array
     */
    public function getDirtyFields(): array
    {
        $dirtyFields = [];
        foreach ($this->entityHiddenFields as $field) {
            if ($field->isDirty()) {
                $dirtyFields[] = $field->getName();
            }
        }

        return $dirtyFields;
    }

    /**
     * returns a key value store of changed keys and values
     *
     * @return array
     */
    public function getDirtyFieldValues(): array
    {
        $dirtyFields = [];
        foreach ($this->entityHiddenFields as $field) {
            if ($field->isDirty()) {
                $dirtyFields[$field->getName()] = $field->getValue();
            }
        }

        return $dirtyFields;
    }

    /**
     * cleans all fields
     */
    public function resetDirty(): void
    {
        foreach ($this->entityHiddenFields as $field) {
            if ($field->isDirty()) {
                $field->resetDirty();
            }
        }
    }

    /**
     * if it is a filed inside the entity
     *
     * @param string $name
     *
     * @return bool
     */
    public function isField(string $name): bool
    {
        return array_key_exists($name, $this->entityHiddenFields);
    }

    /**
     * often data is pact into an array as key value store, to easy assign it you can use this function
     *
     * @param array $fields
     */
    public function setFromArray(array $fields): void
    {
        foreach ($fields as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param EntityStoreInterface $entityStore
     */
    public function setEntityStore(EntityStoreInterface $entityStore): void
    {
        $this->entityStore = $entityStore;
    }

    /**
     * @return EntityStoreInterface|null
     */
    public function getEntityStore(): ?EntityStoreInterface
    {
        return $this->entityStore ?? null;
    }

    /**
     * stores the entity using the store set with setEntityStore()
     *
     * @see setEntityStore
     */
    public function store(): void
    {
        if (isset($this->entityStore)) {
            $this->entityStore->store($this);
        }
    }

    /**
     * add all fields to the debug output
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    /**
     * returns the entity with all its fields as key value array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->entityHiddenFields as $name => $entityField) {
            $result[$name] = $entityField->getValue();
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return null|EntityField
     */
    public function getEntityField(string $name): ?EntityField
    {
        return $this->entityHiddenFields[$name] ?? null;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * magic function to test if a field isset()
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->isField($name) && $this->__get($name) !== null;
    }
}
