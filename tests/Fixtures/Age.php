<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectInteger;

final class Age extends AbstractValueObjectInteger
{
    public function sanitize(int $value): int
    {
        return $value;
    }

    public function isValid(int $value): bool
    {
        return $value >= 0;
    }
}
