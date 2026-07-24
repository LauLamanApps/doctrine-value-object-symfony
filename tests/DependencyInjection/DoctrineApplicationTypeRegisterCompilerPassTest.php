<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject\Tests\DependencyInjection;

use LauLamanApps\DoctrineValueObject\DependencyInjection\Compiler\DoctrineApplicationTypeRegisterCompilerPass;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\StatusType;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\UserIdType;
use LauLamanApps\DoctrineValueObject\Tests\Fixtures\UsernameType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DoctrineApplicationTypeRegisterCompilerPassTest extends TestCase
{
    private const string PARAM = 'doctrine.dbal.connection_factory.types';

    public function testItRegistersEveryDeclaredCustomType(): void
    {
        // Force the fixture types to be autoloaded so get_declared_classes() sees them.
        class_exists(UserIdType::class);
        class_exists(UsernameType::class);
        class_exists(StatusType::class);

        $container = new ContainerBuilder();
        $container->setParameter(self::PARAM, ['pre_existing' => ['class' => 'App\\SomeType']]);

        (new DoctrineApplicationTypeRegisterCompilerPass())->process($container);

        /** @var array<string, array{class: class-string}> $types */
        $types = $container->getParameter(self::PARAM);

        self::assertSame(['class' => UserIdType::class], $types['user_id']);
        self::assertSame(['class' => UsernameType::class], $types['username']);
        self::assertSame(['class' => StatusType::class], $types['status']);
        // existing entries are preserved, never overwritten
        self::assertSame(['class' => 'App\\SomeType'], $types['pre_existing']);
    }

    public function testItDoesNothingWhenDoctrineParameterIsAbsent(): void
    {
        $container = new ContainerBuilder();

        (new DoctrineApplicationTypeRegisterCompilerPass())->process($container);

        self::assertFalse($container->hasParameter(self::PARAM));
    }
}
