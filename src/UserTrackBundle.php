<?php

namespace Tourze\UserTrackBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\UserTrackBundle\DependencyInjection\ListenerCompilerPass;

class UserTrackBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ListenerCompilerPass());
    }
}
