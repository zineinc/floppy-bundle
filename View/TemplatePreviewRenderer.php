<?php


namespace ZineInc\StorageBundle\View;


use Symfony\Component\Templating\EngineInterface;
use ZineInc\Storage\Common\FileId;

class TemplatePreviewRenderer implements PreviewRenderer
{
    private $templating;
    private $template;
    private $attributesProvider;
    private $supportedExtensions;

    public function __construct(EngineInterface $templating, $template, AttributesProvider $attributesProvider, array $supportedExtensions = array())
    {
        $this->attributesProvider = $attributesProvider;
        $this->supportedExtensions = $supportedExtensions;
        $this->template = $template;
        $this->templating = $templating;
    }

    public function render(FileId $fileId)
    {
        if(!$this->supports($fileId)) {
            throw new \LogicException(sprintf('This renderer doesn\'t support file "%s", supported file extensions: %s', $fileId->id(), implode(',', $this->supportedExtensions)));
        }

        return $this->templating->render($this->template, array(
            'fileId' => $fileId->variant($this->attributesProvider->getAttributes($fileId))
        ));
    }

    public function supports(FileId $fileId)
    {
        if(count($this->supportedExtensions) === 0) {
            return true;
        }

        $extension = strtolower(\pathinfo($fileId->id(), PATHINFO_EXTENSION));
        return in_array($extension, $this->supportedExtensions);
    }
}