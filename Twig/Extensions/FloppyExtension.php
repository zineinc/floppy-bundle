<?php


namespace Floppy\Bundle\Twig\Extensions;

use Floppy\Client\UrlGenerator;
use Floppy\Common\FileId;
use Floppy\Bundle\View\PreviewRenderer;

class FloppyExtension extends \Twig_Extension
{
    private $urlGenerator;
    private $previewRenderer;

    public function __construct(UrlGenerator $urlGenerator, PreviewRenderer $previewRenderer)
    {
        $this->urlGenerator = $urlGenerator;
        $this->previewRenderer = $previewRenderer;
    }

    public function getFunctions()
    {
        return array(
            'floppy_url' => new \Twig_Function_Method($this, 'getUrl'),
            'floppy_file_preview' => new \Twig_Function_Method($this, 'renderPreview', array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    public function getUrl(FileId $fileId, $type)
    {
        return $this->urlGenerator->generate($fileId, $type);
    }

    public function renderPreview(\Twig_Environment $env, FileId $fileId)
    {
        return $this->previewRenderer->render($fileId);
    }

    public function getName()
    {
        return 'floppy';
    }
}