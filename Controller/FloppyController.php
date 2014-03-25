<?php


namespace Floppy\Bundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Floppy\Common\FileId;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FloppyController extends Controller
{
    /**
     * @Route("/preview", name="floppyFilePreview")
     * @Template()
     */
    public function previewAction(Request $request)
    {
        return array(
            'fileId' => new FileId($request->query->get('fileId')),
        );
    }
} 