<?php

declare(strict_types=1);

namespace LauLamanApps\DoctrineValueObject;

use LauLamanApps\DoctrineValueObject\DependencyInjection\Compiler\DoctrineApplicationTypeRegisterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class DoctrineValueObjectBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Registering the pass here means the app no longer wires it up in Kernel::build().
        $container->addCompilerPass(new DoctrineApplicationTypeRegisterCompilerPass());
    }
}
