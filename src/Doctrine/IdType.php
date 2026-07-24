<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Doctrine;

use Doctrine\DBAL\Types\Type;
use LauLamanApps\DoctrineValueObject\ValueObject\AbstractId;

/**
 * The single Doctrine type shared by every {@see AbstractId} subclass. A dedicated type
 * class per id is no longer needed: each id declares a `public const string NAME`, and the
 * DoctrineApplicationTypeRegisterCompilerPass registers that NAME against this one type.
 *
 * At runtime a distinct instance of this type exists per registered NAME, so it recovers the
 * concrete id class by asking the Doctrine {@see \Doctrine\DBAL\Types\TypeRegistry} for the
 * name it is registered under and mapping that back to the matching {@see AbstractId} subclass.
 */
final class IdType extends AbstractValueObjectType
{
    /** @var array<string, class-string<AbstractId>>|null */
    private static ?array $classByName = null;

    /** @return class-string<AbstractId> */
    public function getClassName(): string
    {
        return $this->resolveClassName(Type::getTypeRegistry()->lookupName($this));
    }

    /** @return class-string<AbstractId> */
    private function resolveClassName(string $name): string
    {
        // Rebuild on a miss so ids whose class loaded after the first lookup are still found.
        if (null === self::$classByName || !isset(self::$classByName[$name])) {
            self::$classByName = self::buildMap();
        }

        if (!isset(self::$classByName[$name])) {
            throw new \LogicException(sprintf('No AbstractId subclass declares a NAME of "%s".', $name));
        }

        return self::$classByName[$name];
    }

    /** @return array<string, class-string<AbstractId>> */
    private static function buildMap(): array
    {
        $map = [];

        foreach (get_declared_classes() as $className) {
            if (!is_subclass_of($className, AbstractId::class)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            if ($reflection->isAbstract()) {
                continue;
            }

            $name = $reflection->getConstant('NAME');
            if (is_string($name) && '' !== $name) {
                $map[$name] = $className;
            }
        }

        return $map;
    }
}
