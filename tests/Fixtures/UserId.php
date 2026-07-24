<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Fixtures;

use LauLamanApps\DoctrineValueObject\ValueObject\AbstractId;

final class UserId extends AbstractId
{
    public const string NAME = 'user_id';
}
