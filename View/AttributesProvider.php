<?php


namespace Floppy\Bundle\View;

use Floppy\Common\FileId;

interface AttributesProvider
{
    public function getAttributes(FileId $fileId);
} 