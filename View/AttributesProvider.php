<?php


namespace ZineInc\StorageBundle\View;

use ZineInc\Storage\Common\FileId;

interface AttributesProvider
{
    public function getAttributes(FileId $fileId);
} 