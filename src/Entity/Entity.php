<?php

namespace Ortnit\Entity;

use Ortnit\Entity\Validator\Validator;

abstract class Entity
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $dirtyFields = [];

    /**
     * @var Validator
     */
    protected $validator = null;

    public function __construct(Validator $validator = null)
    {
        $localVars = get_class_vars(Entity::class);
        $entityVars = get_class_vars(static::class);
        dump($localVars, $entityVars);
        $diffVars = array_diff(array_keys($entityVars), array_keys($localVars));
        dump($diffVars);

        $this->setValidator($validator);


        foreach ($diffVars as $varName) {
            $this->addField($varName);
        }

        dump($this->fields);
    }

    public function __get($key)
    {
        $this->getFieldValue($key);
    }

    public function __set($key, $value)
    {
        $this->setFieldValue($key, $value);
    }

    public function addField(string $varName, string $type = null, $defaultValue = null)
    {
        if (property_exists(static::class, $varName)) {
            $this->fields[$varName] = [
                'defaultValue' => $this->$varName,
                'type' => gettype($this->$varName) ?? $type,
                'value' => $this->$varName ?? $defaultValue,
            ];
            unset($this->$varName);
        } else {
        }
    }


    protected function getTypeFromVar($variable): string
    {
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setFieldValue(string $key, $value): void
    {
        $this->fields[$key]['value'] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFieldValue(string $key)
    {
        return $this->fields[$key]['value'];
    }


    public function setValidator(Validator $validator = null)
    {
        if ($validator === null) {
            $this->validator = new Validator();
        } else {
            $this->validator = $validator;
        }
    }
}
