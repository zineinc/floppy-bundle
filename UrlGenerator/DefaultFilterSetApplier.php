<?php


namespace Floppy\Bundle\UrlGenerator;


use Floppy\Client\Exception\InvalidArgumentException;
use Floppy\Common\FileId;

class DefaultFilterSetApplier implements FilterSetApplier
{
    private $filterSets;

    public function __construct(array $filterSets)
    {
        $this->filterSets = $filterSets;
    }

    public function applyFilterSet(FileId $fileId, $filterSet, array $options = array())
    {
        if(!isset($this->filterSets[$filterSet])) {
            throw new InvalidArgumentException(sprintf('Filter set "%s" does not exist', $filterSet));
        }

        $filterSet = $this->filterSets[$filterSet];

        foreach($options as $filterName => $filterOptions) {
            if(isset($filterSet[$filterName]) && is_array($filterSet[$filterName])) {
                $filterSet[$filterName] = array_merge($filterSet[$filterName], $filterOptions);
            } else {
                $filterSet[$filterName] = $filterOptions;
            }
        }

        return $fileId->variant($filterSet);
    }
}