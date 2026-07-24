<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\ValueObject;

use LauLamanApps\DoctrineValueObject\Tests\Fixtures\UserId;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\Username;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    public function testIntegerIdExposesItsValue(): void
    {
        $id = new UserId(42);

        self::assertSame(42, $id->getValue());
        self::assertSame('42', (string) $id);
    }

    public function testStringValueObjectAppliesSanitizeOnConstruction(): void
    {
        $username = new Username('  Alice  ');

        self::assertSame('alice', $username->getValue());
        self::assertSame('alice', (string) $username);
    }

    public function testValidationHooksAreCallable(): void
    {
        $username = new Username('bob');

        self::assertTrue($username->isValid('bob'));
        self::assertFalse($username->isValid(''));
    }

    public function testConstructionWithCheckDisabledStillSanitizes(): void
    {
        $username = new Username('  BOB ', check: false);

        self::assertSame('bob', $username->getValue());
    }
}
