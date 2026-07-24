<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\DependencyInjection\Compiler;

use LauLamanApps\DoctrineValueObject\Doctrine\AbstractPhpEnumType;
use LauLamanApps\DoctrineValueObject\Doctrine\AbstractValueObjectType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Registers every concrete {@see AbstractValueObjectType} / {@see AbstractPhpEnumType} with
 * Doctrine, keyed by its `NAME` constant — no manual `doctrine.dbal.types` config needed.
 *
 * It discovers types via get_declared_classes(), which relies on the consuming app having
 * loaded them — the standard `App\` service loader does this by reflecting over `src/`.
 */
final class DoctrineApplicationTypeRegisterCompilerPass implements CompilerPassInterface
{
    private const string CONTAINER_TYPES_PARAMETER = 'doctrine.dbal.connection_factory.types';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::CONTAINER_TYPES_PARAMETER)) {
            return;
        }

        /** @var array<string, array{class: class-string}> $typeDefinition */
        $typeDefinition = $container->getParameter(self::CONTAINER_TYPES_PARAMETER);

        foreach ($this->findCustomTypes() as $name => $class) {
            $typeDefinition[$name] ??= ['class' => $class];
        }

        $container->setParameter(self::CONTAINER_TYPES_PARAMETER, $typeDefinition);
    }

    /**
     * @return \Generator<string, class-string>
     */
    private function findCustomTypes(): \Generator
    {
        foreach (get_declared_classes() as $className) {
            if (
                !is_subclass_of($className, AbstractValueObjectType::class)
                && !is_subclass_of($className, AbstractPhpEnumType::class)
            ) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            if ($reflection->isAbstract()) {
                continue;
            }

            if (!$reflection->hasConstant('NAME')) {
                throw new \LogicException(sprintf('Doctrine type "%s" must declare a "public const string NAME".', $className));
            }

            $name = $reflection->getConstant('NAME');
            if (!is_string($name) || '' === $name) {
                throw new \LogicException(sprintf('The NAME constant of Doctrine type "%s" must be a non-empty string.', $className));
            }

            yield $name => $className;
        }
    }
}
