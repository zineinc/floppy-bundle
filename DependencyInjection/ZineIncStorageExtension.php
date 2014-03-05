<?php


namespace ZineInc\StorageBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ZineIncStorageExtension extends Extension
{
    private $configFiles = array(
        'client.xml',
        'url.xml',
        'twig.xml',
        'form.xml',
        'view.xml',
    );

    public function load(array $config, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach($this->configFiles as $file)
        {
            $loader->load($file);
        }

        $this->setContainerParameters($container, $config, 'zineinc.storage');
    }

    private function setContainerParameters(ContainerBuilder $container, array $config, $rootPath)
    {
        foreach($config as $name => $value) {
            if(is_array($value) && $this->isAssociativeArray($value)) {
                $this->setContainerParameters($container, $value, $rootPath.'.'.$name);
            } else {
                $container->setParameter($rootPath.'.'.$name, $value);
            }
        }
    }

    private function isAssociativeArray(array $array)
    {
        if(count($array) === 0) {
            return false;
        }

        foreach($array as $key => $value) {
            if(!is_int($key)) {
                return true;
            }
        }

        return false;
    }

    public function getAlias()
    {
        return 'zine_inc_storage';
    }
}