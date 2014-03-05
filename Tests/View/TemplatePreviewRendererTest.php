<?php


namespace View;


use ZineInc\Storage\Common\FileId;
use ZineInc\StorageBundle\View\TemplatePreviewRenderer;

class TemplatePreviewRendererTest extends \PHPUnit_Framework_TestCase
{
    const TEMPLATE = 'some-template';
    const SUPPORTED_EXTENSION = 'jpg';
    const UNSUPPORTED_EXTENSION = 'txt';
    const FILE_TYPE = 'image';
    const FILE_ID = 'some-id';
    const URL = 'some-url';
    const PREVIEW_CONTENT = 'preview content';

    private $templating;
    private $attributesProvider;
    private $renderer;

    protected function setUp()
    {
        $this->templating = $this->getMock('Symfony\Component\Templating\EngineInterface');
        $this->attributesProvider = $this->getMock('ZineInc\StorageBundle\View\AttributesProvider');

        $this->renderer = $this->createRenderer($this->templating, $this->attributesProvider);
    }

    /**
     * @test
     */
    public function renderPreviewUsingTwig()
    {
        //given

        $fileId = new FileId(self::FILE_ID.'.'.self::SUPPORTED_EXTENSION);
        $attributes = array('name' => 'value');

        $this->attributesProvider->expects($this->any())
                ->method('getAttributes')
                ->with($fileId)
                ->will($this->returnValue($attributes));

        $this->templating->expects($this->any())
                ->method('render')
                ->with(self::TEMPLATE, array('fileId' => new FileId($fileId->id(), $attributes)))
                ->will($this->returnValue(self::PREVIEW_CONTENT));

        //when

        $actualPreview = $this->renderer->render($fileId);

        //then

        $this->verifyMockObjects();
        $this->assertEquals(self::PREVIEW_CONTENT, $actualPreview);
    }

    /**
     * @test
     * @dataProvider supportsProvider
     */
    public function testSupports($extension, $expected)
    {
        //given

        $fileId = new FileId(self::FILE_ID.'.'.$extension);

        //when

        $actual = $this->renderer->supports($fileId);

        //then

        $this->assertEquals($expected, $actual);
    }

    private function createRenderer($templating, $attributesProvider)
    {
        $renderer = new TemplatePreviewRenderer($templating, self::TEMPLATE, $attributesProvider, array(self::SUPPORTED_EXTENSION));
        return $renderer;
    }

    public function supportsProvider()
    {
        return array(
            array(self::SUPPORTED_EXTENSION, true),
            array(strtoupper(self::SUPPORTED_EXTENSION), true),
            array(self::UNSUPPORTED_EXTENSION, false),
            array(strtoupper(self::UNSUPPORTED_EXTENSION), false),
        );
    }
}
 