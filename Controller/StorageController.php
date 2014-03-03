<?php


namespace ZineInc\StorageBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ZineInc\Storage\Common\FileId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StorageController extends Controller
{
    /**
     * @Route("/preview", name="storageFilePreview")
     * @Template()
     */
    public function previewAction(Request $request)
    {
        return array(
            'fileId' => new FileId($request->query->get('fileId')),
        );
    }
} 