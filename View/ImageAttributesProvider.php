<?php


namespace Floppy\Bundle\View;


use Floppy\Common\FileId;

class ImageAttributesProvider implements AttributesProvider
{
    private $width;
    private $height;

    public function __construct($width, $height)
    {
        $this->height = (int) $height;
        $this->width = (int) $width;
    }

    public function getAttributes(FileId $fileId)
    {
        return array('width' => $this->width, 'height' => $this->height);
    }
}