<?php


namespace Floppy\Bundle\UrlGenerator;


use Floppy\Common\FileId;

interface FilterSetApplier
{
    /**
     * @param FileId $fileId
     * @param string $filterSet
     * @param array $options
     *
     * @return FileId
     */
    public function applyFilterSet(FileId $fileId, $filterSet, array $options = array());
} 