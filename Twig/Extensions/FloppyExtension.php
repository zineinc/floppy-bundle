<?php


namespace Floppy\Bundle\Twig\Extensions;

use Floppy\Bundle\UrlGenerator\FilterSetApplier;
use Floppy\Client\UrlGenerator;
use Floppy\Common\FileId;
use Floppy\Bundle\View\PreviewRenderer;

class FloppyExtension extends \Twig_Extension
{
    private $urlGenerator;
    private $previewRenderer;
    private $filterSetApplier;

    public function __construct(UrlGenerator $urlGenerator, PreviewRenderer $previewRenderer, FilterSetApplier $filterSetApplier)
    {
        $this->urlGenerator = $urlGenerator;
        $this->previewRenderer = $previewRenderer;
        $this->filterSetApplier = $filterSetApplier;
    }

    public function getFunctions()
    {

        return array(
            new \Twig_SimpleFunction('floppy_url', array($this, 'getUrl')),
            new \Twig_SimpleFunction('floppy_file_preview', array($this, 'renderPreview'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('floppy_filter', array($this, 'filter')),
        );
    }

    public function getUrl(FileId $fileId)
    {
        $argsCount = func_num_args();
        $args = func_get_args();
        array_shift($args);

        $type = null;
        $credentials = array();

        if ($argsCount === 2 && is_array($args[0])) {
            $credentials = $args[0];
        } else if ($argsCount === 2 && is_string($args[0])) {
            $type = $args[0];
        } else if ($argsCount === 3 && is_string($args[0]) && is_array($args[1])) {
            $type = $args[0];
            $credentials = $args[1];
        } else if($argsCount !== 1) {
            $givenArgTypes = array();
            foreach($args as $arg) {
                $givenArgTypes[] = gettype($arg);
            }
            throw new \InvalidArgumentException(sprintf('floppy_url accepts those argument types: (FileId $fileId), (FileId $fileId, array $credentials), (FileId $fileId, string $fileType), (FileId $fileId, string $fileType, array $credentials), but (FileId, %s) given', implode(', ', $givenArgTypes)));
        }


        return $this->urlGenerator->generate($fileId, $type, $credentials);
    }

    public function renderPreview(\Twig_Environment $env, FileId $fileId)
    {
        return $this->previewRenderer->render($fileId);
    }

    public function filter(FileId $fileId, $filterSet, array $options = array())
    {
        return $this->filterSetApplier->applyFilterSet($fileId, $filterSet, $options);
    }

    public function getName()
    {
        return 'floppy';
    }
}