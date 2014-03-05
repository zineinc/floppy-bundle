<?php


namespace ZineInc\StorageBundle\View;

use ZineInc\Storage\Common\FileId;

class FileAttributesProvider implements AttributesProvider
{
    private $name;

    public function __construct($name = 'n-a')
    {
        $this->name = $name;
    }

    public function getAttributes(FileId $fileId)
    {
        return array('name' => $this->name);
    }
}