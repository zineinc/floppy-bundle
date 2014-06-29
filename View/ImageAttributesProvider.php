<?php


namespace Floppy\Bundle\View;


use Floppy\Bundle\UrlGenerator\FilterSetApplier;
use Floppy\Common\FileId;

class ImageAttributesProvider implements AttributesProvider
{
    private $filterSetApplier;
    private $filterSet;

    public function __construct(FilterSetApplier $filterSetApplier, $filterSet)
    {
        $this->filterSetApplier = $filterSetApplier;
        $this->filterSet = $filterSet;
    }

    public function getAttributes(FileId $fileId)
    {
        return $this->filterSetApplier->applyFilterSet($fileId, $this->filterSet)->attributes()->all();
    }
}