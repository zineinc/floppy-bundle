<?php


namespace ZineInc\StorageBundle\View;


use ZineInc\Storage\Common\FileId;

class ChainedPreviewRenderer implements PreviewRenderer
{
    private $renderers;

    public function __construct(array $renderers)
    {
        foreach($renderers as $renderer) {
            if(!$renderer instanceof PreviewRenderer) {
                throw new \InvalidArgumentException('$renderers should be array of PreviewRenderer objects');
            }
        }

        $this->renderers = $renderers;
    }

    public function render(FileId $fileId)
    {
        $renderer = $this->findRenderer($fileId);
        if($renderer === null) {
            throw new \LogicException(sprintf('File "%s" is not supported by "%s" renderer.', $fileId->id(), __CLASS__));
        }

        return $renderer->render($fileId);
    }

    public function supports(FileId $fileId)
    {
        return $this->findRenderer($fileId) !== null;
    }

    private function findRenderer(FileId $fileId) {
        foreach($this->renderers as $renderer) {
            if($renderer->supports($fileId)) {
                return $renderer;
            }
        }

        return null;
    }
}