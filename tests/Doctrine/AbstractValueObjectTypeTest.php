<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use LauLamanApps\DoctrineValueObject\Doctrine\Exception\ValueObjectIsNotSafeToPersistException;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\Age;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\AgeType;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\AlwaysInvalidId;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\AlwaysInvalidIdType;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\Username;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\UsernameType;
use PHPUnit\Framework\TestCase;

final class AbstractValueObjectTypeTest extends TestCase
{
    public function testConvertToPhpValueBuildsTheValueObject(): void
    {
        $result = (new AgeType())->convertToPHPValue('42', $this->platform());

        self::assertInstanceOf(Age::class, $result);
        self::assertSame(42, $result->getValue());
    }

    public function testConvertToPhpValueReturnsNullForNull(): void
    {
        $type = new AgeType();

        self::assertNull($type->convertToPHPValue(null, $this->platform()));
        self::assertNull($type->convertToPHPValue('NULL', $this->platform()));
    }

    public function testConvertToDatabaseValueUnwrapsTheValueObject(): void
    {
        self::assertSame(42, (new AgeType())->convertToDatabaseValue(new Age(42), $this->platform()));
        self::assertSame('alice', (new UsernameType())->convertToDatabaseValue(new Username('alice'), $this->platform()));
    }

    public function testConvertToDatabaseValuePassesScalarsThrough(): void
    {
        $type = new AgeType();

        self::assertSame(42, $type->convertToDatabaseValue(42, $this->platform()));
        self::assertSame('x', $type->convertToDatabaseValue('x', $this->platform()));
        self::assertNull($type->convertToDatabaseValue(null, $this->platform()));
        self::assertNull($type->convertToDatabaseValue('', $this->platform()));
    }

    public function testConvertToDatabaseValueRejectsAnInvalidValueObject(): void
    {
        $this->expectException(ValueObjectIsNotSafeToPersistException::class);

        (new AlwaysInvalidIdType())->convertToDatabaseValue(new AlwaysInvalidId(5), $this->platform());
    }

    public function testConvertToDatabaseValueRejectsUnsupportedTypes(): void
    {
        $this->expectException(\LogicException::class);

        (new AgeType())->convertToDatabaseValue(new \stdClass(), $this->platform());
    }

    public function testSqlDeclarationUsesIntegerColumnForIntegerValueObject(): void
    {
        $platform = $this->platform();
        $platform->method('getIntegerTypeDeclarationSQL')->willReturn('INT');

        self::assertSame('INT', (new AgeType())->getSQLDeclaration(['name' => 'id'], $platform));
    }

    public function testSqlDeclarationUsesStringColumnForStringValueObject(): void
    {
        $platform = $this->platform();
        $platform->method('getStringTypeDeclarationSQL')->willReturn('VARCHAR(255)');

        self::assertSame('VARCHAR(255)', (new UsernameType())->getSQLDeclaration(['name' => 'username'], $platform));
    }

    private function platform(): AbstractPlatform&\PHPUnit\Framework\MockObject\MockObject
    {
        return $this->createMock(AbstractPlatform::class);
    }
}
