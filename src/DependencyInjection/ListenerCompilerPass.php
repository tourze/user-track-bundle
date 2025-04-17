<?php

namespace Tourze\UserTrackBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\UserEventBundle\DependencyInjection\CommonTrait;
use Tourze\UserTrackBundle\EventSubscriber\UserTrackListener;

class ListenerCompilerPass implements CompilerPassInterface
{
    use CommonTrait;

    public function process(ContainerBuilder $container): void
    {
        $listener = $container->findDefinition(UserTrackListener::class);
        foreach ($this->fetchUserInteractionEvents($container) as $eventClass) {
            $listener->addTag('kernel.event_listener', [
                'event' => $eventClass,
                'priority' => -10,
            ]);
        }
    }
}
