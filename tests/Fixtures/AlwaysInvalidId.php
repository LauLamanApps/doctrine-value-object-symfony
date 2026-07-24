<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectInteger;

/** A value object whose isValid() always fails — used to assert the persist guard. */
final class AlwaysInvalidId extends AbstractValueObjectInteger
{
    public function sanitize(int $value): int
    {
        return $value;
    }

    public function isValid(int $value): bool
    {
        return false;
    }
}
