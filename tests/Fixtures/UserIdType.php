<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;

final class UserIdType extends AbstractValueObjectType
{
    public const string NAME = 'user_id';

    public function getClassName(): string
    {
        return UserId::class;
    }
}
