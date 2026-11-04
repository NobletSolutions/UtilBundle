<?php

namespace NS\UtilBundle\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use NS\UtilBundle\NSUtilBundle;
use NS\UtilBundle\Service\Ajax;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class CompatibilityKernel extends Kernel
{
    private ?string $testCacheDir = null;

    public function registerBundles(): iterable
    {
        return [new FrameworkBundle(), new DoctrineBundle(), new TwigBundle(), new NSUtilBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'secret' => 'compatibility-test',
                'form' => true,
                'router' => ['resource' => __DIR__.'/routes.yml', 'utf8' => true],
                'validation' => true,
                'default_locale' => 'en',
                'translator' => ['fallbacks' => ['en']],
                'http_method_override' => false,
                'handle_all_throwables' => true,
            ]);
            $container->loadFromExtension('doctrine', [
                'dbal' => ['url' => 'sqlite:///:memory:'],
                'orm' => ['auto_generate_proxy_classes' => true],
            ]);
            $container->loadFromExtension('twig', [
                'strict_variables' => true,
                'form_themes' => ['@NSUtil/Form/fields.html.twig'],
            ]);
            $container->setAlias('test.ajax', Ajax::class)->setPublic(true);
            $container->setAlias('test.form_factory', 'form.factory')->setPublic(true);
            $container->setAlias('test.twig', 'twig')->setPublic(true);
        });
    }

    public function getCacheDir(): string
    {
        return $this->testCacheDir ??= sys_get_temp_dir().'/ns-util-compatibility/'.bin2hex(random_bytes(8));
    }

    public function getLogDir(): string
    {
        return $this->getCacheDir().'/logs';
    }
}
