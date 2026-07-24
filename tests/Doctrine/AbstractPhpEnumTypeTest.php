<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\Status;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\StatusType;
use PHPUnit\Framework\TestCase;

final class AbstractPhpEnumTypeTest extends TestCase
{
    public function testConvertToPhpValueResolvesTheEnumCase(): void
    {
        $type = new StatusType();
        $platform = $this->createMock(AbstractPlatform::class);

        self::assertSame(Status::Active, $type->convertToPHPValue('active', $platform));
        self::assertNull($type->convertToPHPValue('does-not-exist', $platform));
        self::assertNull($type->convertToPHPValue(null, $platform));
        self::assertNull($type->convertToPHPValue(['array'], $platform));
    }

    public function testConvertToDatabaseValueUnwrapsTheEnum(): void
    {
        $type = new StatusType();
        $platform = $this->createMock(AbstractPlatform::class);

        self::assertSame('active', $type->convertToDatabaseValue(Status::Active, $platform));
        self::assertNull($type->convertToDatabaseValue(null, $platform));
    }

    public function testSqlDeclarationForGenericPlatform(): void
    {
        $type = new StatusType();

        $sql = $type->getSQLDeclaration(['name' => 'status'], $this->createMock(AbstractPlatform::class));

        self::assertSame("ENUM('active','inactive') COMMENT '(DC2Type:status)'", $sql);
    }

    public function testSqlDeclarationForSqlite(): void
    {
        $type = new StatusType();

        $sql = $type->getSQLDeclaration(['name' => 'status'], $this->createMock(SQLitePlatform::class));

        self::assertSame("TEXT CHECK( status IN ('active', 'inactive') )", $sql);
    }
}
