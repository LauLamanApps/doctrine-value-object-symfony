<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;

final class AlwaysInvalidIdType extends AbstractValueObjectType
{
    public const string NAME = 'always_invalid_id';

    public function getClassName(): string
    {
        return AlwaysInvalidId::class;
    }
}
