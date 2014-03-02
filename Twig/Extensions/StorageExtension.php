<?php


namespace ZineInc\StorageBundle\Twig\Extensions;

use ZineInc\Storage\Client\UrlGenerator;
use ZineInc\Storage\Common\FileId;

class StorageExtension extends \Twig_Extension
{
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions()
    {
        return array(
            'storage_url' => new \Twig_Function_Method($this, 'getUrl'),
        );
    }

    public function getUrl(FileId $fileId, $type)
    {
        return $this->urlGenerator->generate($fileId, $type);
    }

    public function getName()
    {
        return 'zineinc_storage';
    }
}