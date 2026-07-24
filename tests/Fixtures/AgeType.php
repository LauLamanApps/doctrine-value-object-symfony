<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;

final class AgeType extends AbstractValueObjectType
{
    public const string NAME = 'age';

    public function getClassName(): string
    {
        return Age::class;
    }
}
