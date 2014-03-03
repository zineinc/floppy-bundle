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
            'storage_file_preview' => new \Twig_Function_Method($this, 'renderPreview', array('needs_environment' => true, 'is_safe' => array('html'))),
            'storage_form_javascript' => new \Twig_Function_Node('Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', array('is_safe' => array('html'))),
        );
    }

    public function getUrl(FileId $fileId, $type)
    {
        return $this->urlGenerator->generate($fileId, $type);
    }

    public function renderPreview(\Twig_Environment $env, FileId $fileId)
    {
        return $env->render('ZineIncStorageBundle::preview.html.twig', array('fileId' => $fileId));
    }

    public function getName()
    {
        return 'zineinc_storage';
    }
}