<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\ValueObject;

abstract class AbstractId extends AbstractValueObjectInteger
{
    /**
     * Doctrine type name for this id. A subclass that declares a non-empty NAME is
     * auto-registered with Doctrine via the shared {@see \LauLamanApps\DoctrineValueObject\Doctrine\IdType},
     * so no dedicated Doctrine type class is needed per id.
     */
    public const ?string NAME = null;

    public function sanitize(int $value): int
    {
        return $value;
    }

    public function isValid(int $value): bool
    {
        return true;
    }
}
