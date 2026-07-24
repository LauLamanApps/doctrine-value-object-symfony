<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractPhpEnumType;

final class StatusType extends AbstractPhpEnumType
{
    public const string NAME = 'status';

    protected function getEnumClassName(): string
    {
        return Status::class;
    }
}
