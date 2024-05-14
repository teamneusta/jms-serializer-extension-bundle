<?php declare(strict_types=1);

namespace Neusta\JmsSerializerExtensionBundle\DependencyInjection;

use Neusta\JmsSerializerExtensionBundle\Serializer\FileLocator as SerializerFileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class NeustaJmsSerializerExtensionExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container): void
    {
        $container->register('jms_serializer.metadata.file_locator_decorated', SerializerFileLocator::class)
            ->setDecoratedService('jms_serializer.metadata.file_locator')
            ->addArgument(new Reference('jms_serializer.metadata.file_locator_decorated.inner'))
            ->addArgument($container->getParameter('kernel.project_dir'))
            ->addArgument($config['non_prefixed_namespaces']);
    }
}
