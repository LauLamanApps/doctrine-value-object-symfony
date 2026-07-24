<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LauLamanApps\DoctrineValueObject\Doctrine\Exception\ValueObjectIsNotSafeToPersistException;
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObject;
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectInteger;
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractValueObjectString;

/**
 * Maps a value object (extending {@see AbstractValueObjectInteger} or
 * {@see AbstractValueObjectString}) to/from a database column. Subclasses declare a
 * `public const string NAME` and the concrete value-object class via {@see getClassName()};
 * registration by NAME is handled automatically by the DoctrineApplicationTypeRegisterCompilerPass.
 */
abstract class AbstractValueObjectType extends Type
{
    /** @return class-string<AbstractValueObject> */
    abstract public function getClassName(): string;

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $className = $this->getClassName();

        if (is_subclass_of($className, AbstractValueObjectString::class)) {
            return $platform->getStringTypeDeclarationSQL($column);
        }

        if (is_subclass_of($className, AbstractValueObjectInteger::class)) {
            return $platform->getIntegerTypeDeclarationSQL($column);
        }

        throw new \LogicException(sprintf(
            'Value object "%s" must extend a string or integer value object to be persistable.',
            $className,
        ));
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?AbstractValueObject
    {
        if (null === $value || 'NULL' === $value) {
            return null;
        }

        if (is_numeric($value)) {
            $value = (int) $value;
        }

        $className = $this->getClassName();

        return new $className($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_string($value) || is_int($value)) {
            return $value;
        }

        if ($value instanceof AbstractValueObject) {
            if (!$value->isValid($value->getValue())) {
                throw new ValueObjectIsNotSafeToPersistException();
            }

            return $value->getValue();
        }

        throw new \LogicException(sprintf(
            'Cannot convert a value of type "%s" to a database value.',
            get_debug_type($value),
        ));
    }
}
