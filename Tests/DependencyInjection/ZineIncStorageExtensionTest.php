<?php


namespace DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use ZineInc\StorageBundle\DependencyInjection\ZineIncStorageExtension;

class ZineIncStorageExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new ZineIncStorageExtension();
    }

    /**
     * @test
     */
    public function validAndCompleteConfiguration_configureAllServices()
    {
        //given

        $container = new ContainerBuilder();

        //when

        $this->extension->load(array(array(
            'secret_key' => 'abc',
            'endpoint' => array(
                'host' => 'localhost',
            ),
        )), $container);

        //then

        $client = $container->get('zineinc.storage.client');
        $urlGenerator = $container->get('zineinc.storage.url_generator');
        $storageExtension = $container->get('zineinc.storage.twig.extension');

        $this->assertInstanceOf('ZineInc\Storage\Client\StorageClient', $client);
        $this->assertInstanceOf('ZineInc\Storage\Client\UrlGenerator', $urlGenerator);
        $this->assertInstanceOf('ZineInc\StorageBundle\Twig\Extensions\StorageExtension', $storageExtension);
    }

    /**
     * @test
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function incompleteConfiguration_throwException()
    {
        //given

        $container = new ContainerBuilder();

        //when

        $this->extension->load(array(array()), $container);
    }

    /**
     * @test
     */
    public function completeConfiguration_setContainerParameters()
    {
        //given

        $config = array(
            'endpoint' => array(
                'host' => 'localhost',
                'protocol' => 'xxx',
            ),
            'secret_key' => 'abcd',
        );
        $container = new ContainerBuilder();

        //when

        $this->extension->load(array($config), $container);

        //then

        $this->assertEquals($config['endpoint']['host'], $container->getParameter('zineinc.storage.endpoint.host'));
        $this->assertEquals($config['endpoint']['protocol'], $container->getParameter('zineinc.storage.endpoint.protocol'));
        $this->assertEquals($config['secret_key'], $container->getParameter('zineinc.storage.secret_key'));
    }
}
 