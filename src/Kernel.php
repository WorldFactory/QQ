<?php

namespace WorldFactory\QQ;

use WorldFactory\QQ\DependencyInjection\RunnerCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /** @var Application */
    private $application;

    /**
     * @param Application $application
     */
    public function setApplication(Application $application) : self
    {
        $this->application = $application;

        return $this;
    }

    public function getCacheDir()
    {
        return getcwd().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return getcwd().'/var/log';
    }

    public function registerBundles()
    {
        $projectFileSrc = $this->getProjectDir().'/config/bundles.php';
        $localFileSrc = getcwd().'/config/bundles.php';

        $contents = array_merge(
            file_exists($projectFileSrc) ? require $projectFileSrc : [],
            file_exists($localFileSrc) ? require $localFileSrc : []
        );

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->addResource(new FileResource(getcwd().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $confDir = getcwd().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');

        $container->addCompilerPass(new RunnerCompilerPass());
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }
}
