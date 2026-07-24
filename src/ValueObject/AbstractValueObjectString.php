<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\ValueObject;

abstract class AbstractValueObjectString extends AbstractValueObject
{
    /** @var string */
    protected $value;

    public function __construct(string $value, bool $check = true)
    {
        parent::__construct($value, $check);
    }

    abstract public function sanitize(string $value): string;

    abstract public function isValid(string $value): bool;

    public function getValue(): string
    {
        return $this->value;
    }
}
