<?php

namespace Akrzem\EasyAdminPlusBundle\Acl\Compiler;

use Akrzem\EasyAdminPlusBundle\AkrzemEasyAdminPlusBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigPathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $twigLoaderFilesystemId = $container->getAlias('twig.loader')->__toString();
        $twigLoaderFilesystemDefinition = $container->getDefinition($twigLoaderFilesystemId);

        // Replaces native EasyAdmin templates
        $easyAdminExtensionBundleRefl = new \ReflectionClass(AkrzemEasyAdminPlusBundle::class);
        $easyAdminExtensionBundlePath = dirname($easyAdminExtensionBundleRefl->getFilename());
        $easyAdminExtensionTwigPath = $easyAdminExtensionBundlePath.'/Resources/views';
        $twigLoaderFilesystemDefinition->addMethodCall('prependPath', array($easyAdminExtensionTwigPath, 'EasyAdmin'));

        $nativeEasyAdminBundleRefl = new \ReflectionClass(EasyAdminBundle::class);
        $nativeEasyAdminBundlePath = dirname($nativeEasyAdminBundleRefl->getFilename());
        $nativeEasyAdminTwigPath = $nativeEasyAdminBundlePath.'/Resources/views';
        // Defines a namespace from native EasyAdmin templates
        $twigLoaderFilesystemDefinition->addMethodCall('addPath', array($nativeEasyAdminTwigPath, 'BaseEasyAdmin'));
    }
}
