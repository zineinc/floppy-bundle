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

    public function getUrl(FileId $fileId)
    {
        $argsCount = func_num_args();
        $args = func_get_args();
        array_shift($args);

        $type = null;
        $credentials = array();

        if($argsCount === 2 && is_array($args[0])) {
            $fileId = $fileId->variant($args[0]);
        } else if($argsCount === 2 && is_string($args[0])) {
            $type = $args[0];
        } else if($argsCount === 3 && is_array($args[0]) && is_array($args[1])) {
            $fileId = $fileId->variant($args[0]);
            $credentials = $args[1];
        } else if($argsCount === 3 && is_array($args[0]) && is_string($args[1])) {
            $fileId = $fileId->variant($args[0]);
            $type = $args[1];
        } else if($argsCount === 3 && is_string($args[0]) && is_array($args[1])) {
            $type = $args[0];
            $credentials = $args[1];
        } else if($argsCount === 4 && is_array($args[0]) && is_string($args[1]) && is_array($args[2])) {
            $fileId = $fileId->variant($args[0]);
            $type = $args[1];
            $credentials = $args[2];
        } else {
            $givenArgTypes = array();
            foreach($args as $arg) {
                $givenArgTypes[] = gettype($arg);
            }
            throw new \InvalidArgumentException(sprintf('floppy_url accepts those argument types: (FileId $fileId, array $fileAttrs), (FileId $fileId, string $fileType), (FileId $fileId, array $fileAttrs, array $credentials), (FileId $fileId, array $fileAttrs, string $fileType), (FIleId $fileId, string $fileType, array $credentials) or (FileId $fileId, array $fileAttrs, string $type, array $credentials), but (FileId, %s) given', implode(', ', $givenArgTypes)));
        }


        return $this->urlGenerator->generate($fileId, $type, $credentials);
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