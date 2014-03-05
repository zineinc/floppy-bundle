<?php


namespace ZineInc\StorageBundle\Twig\Extensions;

use ZineInc\Storage\Client\UrlGenerator;
use ZineInc\Storage\Common\FileId;
use ZineInc\StorageBundle\View\PreviewRenderer;

class StorageExtension extends \Twig_Extension
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
            'storage_url' => new \Twig_Function_Method($this, 'getUrl'),
            'storage_file_preview' => new \Twig_Function_Method($this, 'renderPreview', array('needs_environment' => true, 'is_safe' => array('html'))),
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
        return 'zineinc_storage';
    }
}