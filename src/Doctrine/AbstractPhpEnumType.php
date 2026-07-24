<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Maps a native PHP backed enum to/from a database column. Subclasses declare a
 * `public const string NAME` and the enum class via {@see getEnumClassName()}.
 */
abstract class AbstractPhpEnumType extends Type
{
    public const ?string NAME = null;

    /** @return class-string<\BackedEnum> */
    abstract protected function getEnumClassName(): string;

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $enum = $this->getEnumClassName();
        $values = array_map(static fn (\BackedEnum $case): string => "'".$case->value."'", $enum::cases());

        if ($platform instanceof SQLitePlatform) {
            /** @var string $columnName */
            $columnName = $column['name'];

            return sprintf('TEXT CHECK( %s IN (%s) )', $columnName, implode(', ', $values));
        }

        return sprintf("ENUM(%s) COMMENT '(DC2Type:%s)'", implode(',', $values), (string) static::NAME);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\BackedEnum
    {
        if (!is_int($value) && !is_string($value)) {
            return null;
        }

        $enum = $this->getEnumClassName();

        return $enum::tryFrom($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
