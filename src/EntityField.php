<?php

declare(strict_types=1);

namespace Ortnit\Entity;

use Ortnit\Entity\Exception\EntityFieldValidationException;
use Ortnit\Validator\ValidatorInterface;
use Ortnit\Validator\Exception\ValidatorException;

class EntityField
{
    /**
     * field name
     *
     * @var string
     */
    protected string $name;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $oldValue;

    /**
     * @var ValidatorInterface|null
     */
    protected ?ValidatorInterface $rule;

    /**
     * EntityField constructor.
     *
     * @param string                  $name
     * @param mixed|null              $default
     * @param ValidatorInterface|null $rule
     */
    public function __construct(string $name, $default = null, ValidatorInterface $rule = null)
    {
        $this->name = $name;
        $this->default = $default;
        $this->value = $default;
        $this->oldValue = $default;
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @throws EntityFieldValidationException
     */
    public function setValue($value): void
    {
        try {
            if ($this->rule !== null) {
                $this->rule->assert($value);
            }
            $this->value = $value;
        } catch (ValidatorException $e) {
            $message = "failed setting " . $this->name . " to " . $value;
            throw new EntityFieldValidationException($message, 0, $e);
        }
    }

    /**
     * gets the old value
     *
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->value !== $this->oldValue;
    }

    /**
     * flushes dirty field
     */
    public function resetDirty(): void
    {
        $this->oldValue = $this->value;
    }

    /**
     * @return ValidatorInterface|null
     */
    public function getRule(): ?ValidatorInterface
    {
        return $this->rule;
    }
}
