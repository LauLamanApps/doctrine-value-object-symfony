<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LauLamanApps\DoctrineValueObject\Doctrine\Exception\ValueObjectIsNotSafeToPersistException;
use LauLamanApps\DoctrineValueObject\Doctrine\IdType;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\AlwaysInvalidId;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\UserId;
use PHPUnit\Framework\TestCase;

final class IdTypeTest extends TestCase
{
    /**
     * The shared IdType resolves the concrete AbstractId class from the NAME it is registered
     * under, so a single type serves every id without a dedicated type class per id.
     */
    public function testConvertToPhpValueBuildsTheIdRegisteredUnderThatName(): void
    {
        $type = $this->registeredIdType();

        $result = $type->convertToPHPValue('42', $this->platform());

        self::assertInstanceOf(UserId::class, $result);
        self::assertSame(42, $result->getValue());
    }

    public function testGetClassNameResolvesFromTheRegisteredName(): void
    {
        self::assertSame(UserId::class, $this->registeredIdType()->getClassName());
    }

    public function testSqlDeclarationUsesAnIntegerColumn(): void
    {
        $platform = $this->platform();
        $platform->method('getIntegerTypeDeclarationSQL')->willReturn('INT');

        self::assertSame('INT', $this->registeredIdType()->getSQLDeclaration(['name' => 'id'], $platform));
    }

    public function testConvertToDatabaseValueUnwrapsTheId(): void
    {
        self::assertSame(42, (new IdType())->convertToDatabaseValue(new UserId(42), $this->platform()));
    }

    public function testConvertToDatabaseValueRejectsAnInvalidValueObject(): void
    {
        $this->expectException(ValueObjectIsNotSafeToPersistException::class);

        (new IdType())->convertToDatabaseValue(new AlwaysInvalidId(5), $this->platform());
    }

    private function registeredIdType(): IdType
    {
        // Force the id class to be autoloaded so IdType's NAME->class scan sees it.
        class_exists(UserId::class);

        if (!Type::hasType(UserId::NAME)) {
            Type::addType(UserId::NAME, IdType::class);
        }

        $type = Type::getType(UserId::NAME);
        self::assertInstanceOf(IdType::class, $type);

        return $type;
    }

    private function platform(): AbstractPlatform&\PHPUnit\Framework\MockObject\MockObject
    {
        return $this->createMock(AbstractPlatform::class);
    }
}
