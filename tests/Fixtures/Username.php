<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectString;

final class Username extends AbstractValueObjectString
{
    public function sanitize(string $value): string
    {
        return strtolower(trim($value));
    }

    public function isValid(string $value): bool
    {
        return '' !== $value;
    }
}
