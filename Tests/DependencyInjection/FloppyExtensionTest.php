<?php


namespace Floppy\Bundle\Tests\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Floppy\Common\FileId;
use Floppy\Bundle\DependencyInjection\FloppyExtension;

class FloppyExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new FloppyExtension();
    }

    /**
     * @test
     */
    public function validAndCompleteConfiguration_configureAllServices()
    {
        //given

        $container = new ContainerBuilder();
        $container->set('templating', $this->getMock('Symfony\Component\Templating\EngineInterface'));

        //when

        $this->extension->load(array(array(
            'secret_key' => 'abc',
            'endpoint' => array(
                'host' => 'localhost',
            ),
        )), $container);

        //then

        $client = $container->get('floppy.client');
        $urlGenerator = $container->get('floppy.url_generator');
        $storageExtension = $container->get('floppy.twig.extension');
        $imagePreviewRenderer = $container->get('floppy.view.preview.image');

        $this->assertInstanceOf('Floppy\Client\StorageClient', $client);
        $this->assertInstanceOf('Floppy\Client\UrlGenerator', $urlGenerator);
        $this->assertInstanceOf('Floppy\Bundle\Twig\Extensions\FloppyExtension', $storageExtension);
        $this->assertInstanceOf('Floppy\Bundle\View\PreviewRenderer', $imagePreviewRenderer);

        $this->assertTrue($imagePreviewRenderer->supports(new FileId('some.jpg')));
        $this->assertFalse($imagePreviewRenderer->supports(new FileId('some.txt')));

        //all services can be created?
        foreach($container->getServiceIds() as $id) {
            $service = $container->get($id);
            $this->assertNotNull($service);
        }
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

        $this->assertEquals($config['endpoint']['host'], $container->getParameter('floppy.endpoint.host'));
        $this->assertEquals($config['endpoint']['protocol'], $container->getParameter('floppy.endpoint.protocol'));
        $this->assertEquals($config['secret_key'], $container->getParameter('floppy.secret_key'));
    }
}
 