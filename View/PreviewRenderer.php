<?php


namespace ZineInc\StorageBundle\View;

use ZineInc\Storage\Common\FileId;

interface PreviewRenderer
{
    public function render(FileId $fileId);
    public function supports(FileId $fileId);
} 