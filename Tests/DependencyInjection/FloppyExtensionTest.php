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

        $this->assertInstanceOf('Floppy\Client\FloppyClient', $client);
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

        $imageExtensions = array('jpg');
        $config = array(
            'endpoint' => array(
                'host' => 'localhost',
                'protocol' => 'xxx',
            ),
            'secret_key' => 'abcd',
            'file_type_extensions' => array(
                'image' => $imageExtensions,
            )
        );
        $container = new ContainerBuilder();

        //when

        $this->extension->load(array($config), $container);

        //then

        $this->assertEquals($config['endpoint']['host'], $container->getParameter('floppy.endpoint.host'));
        $this->assertEquals($config['endpoint']['protocol'], $container->getParameter('floppy.endpoint.protocol'));
        $this->assertEquals($config['secret_key'], $container->getParameter('floppy.secret_key'));

        $fileTypeExtensions = $container->getParameter('floppy.file_type_extensions');
        $this->assertEquals($imageExtensions, $fileTypeExtensions['image']);

        $fileTypeAliases = $container->getParameter('floppy.form.file_type_aliases');
        $this->assertEquals($imageExtensions, $fileTypeAliases['image']['extensions']);

        $this->assertEquals($imageExtensions, $container->getParameter('floppy.form.preview.image.supported_extensions'));
    }

    /**
     * @test
     */
    public function configureDefaultCredentials()
    {
        //given

        $config = $this->validConfig();

        $config['default_credentials'] = array(
            'upload' => array(
                'expiration' => 123,
            ),
            'download' => array(
                'expiration' => 321,
            ),
        );

        $container = new ContainerBuilder();

        //when

        $this->extension->load(array($config), $container);

        //then

        $this->assertEquals($config['default_credentials']['upload'], $container->getParameter('floppy.default_credentials.upload'));
        $this->assertEquals($config['default_credentials']['download'], $container->getParameter('floppy.default_credentials.download'));

        foreach(array('floppy.credentials_generator', 'floppy.url_generator.credentials_generator') as $id) {
            $credentialsGenerator = $container->get($id);
            $credentials = $credentialsGenerator->generateCredentials();

            $this->assertNotEmpty($credentials);
        }
    }

    /**
     * @test
     */
    public function configureFilterSets()
    {
        //given

        $config = $this->validConfig();

        $config['filter_sets'] = array(
            'some_filter' => array(
                'quality' => 100,
                'some' => array(
                    'name' => 'value'
                ),
            ),
        );

        $container = new ContainerBuilder();

        //when

        $this->extension->load(array($config), $container);

        //then

        $parameter = $container->getParameter('floppy.filter_sets');
        $this->assertTrue(isset($parameter['_preview']));
        unset($parameter['_preview']);

        $this->assertEquals($config['filter_sets'], $parameter);
    }

    /**
     * @return array
     */
    private function validConfig()
    {
        $config = array(
            'endpoint' => array(
                'host' => 'localhost',
                'protocol' => 'xxx',
            ),
            'secret_key' => 'abcd',
        );
        return $config;
    }
}
 