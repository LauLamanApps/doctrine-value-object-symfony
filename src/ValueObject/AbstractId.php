<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\ValueObject;

abstract class AbstractId extends AbstractValueObjectInteger
{
    public function sanitize(int $value): int
    {
        return $value;
    }

    public function isValid(int $value): bool
    {
        return true;
    }
}
