<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\ValueObject;

abstract class AbstractValueObjectInteger extends AbstractValueObject
{
    /** @var int */
    protected $value;

    public function __construct(int $value, bool $check = true)
    {
        parent::__construct($value, $check);
    }

    abstract public function sanitize(int $value): int;

    abstract public function isValid(int $value): bool;

    public function getValue(): int
    {
        return $this->value;
    }
}
