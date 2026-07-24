<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;

final class UsernameType extends AbstractValueObjectType
{
    public const string NAME = 'username';

    public function getClassName(): string
    {
        return Username::class;
    }
}
