<?php


namespace Floppy\Bundle\View;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Floppy\Common\FileId;

/**
 * Lazy loaded preview renderer
 *
 * Thanks to this class, circural reference "templating -> twig -> floppy.twig.extension" is avioded
 */
class LazyLoadedPreviewRenderer implements PreviewRenderer
{
    private $container;
    private $template;
    private $attributesProvider;
    private $supportedExtensions;

    private $renderer;

    public function __construct(ContainerInterface $container, $template, AttributesProvider $attributesProvider, array $supportedExtensions = array())
    {
        $this->attributesProvider = $attributesProvider;
        $this->supportedExtensions = $supportedExtensions;
        $this->template = $template;
        $this->container = $container;
    }

    public function render(FileId $fileId)
    {
        return $this->getRenderer()->render($fileId);
    }

    private function getRenderer()
    {
        if($this->renderer === null) {
            $this->renderer = new TemplatePreviewRenderer($this->container->get('templating'), $this->template, $this->attributesProvider, $this->supportedExtensions);
        }

        return $this->renderer;
    }

    public function supports(FileId $fileId)
    {
        return $this->getRenderer()->supports($fileId);
    }
}