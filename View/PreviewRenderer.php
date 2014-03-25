<?php


namespace Floppy\Bundle\View;

use Floppy\Common\FileId;

interface PreviewRenderer
{
    public function render(FileId $fileId);
    public function supports(FileId $fileId);
} 