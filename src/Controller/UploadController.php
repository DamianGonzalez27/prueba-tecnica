<?php

namespace App\Controller;

use App\Entity\AppFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

class UploadController extends AbstractController {

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var DownloadHandler
     */
    private $downloadHandler;

    public function __construct(EntityManagerInterface $em, DownloadHandler $downloadHandler)
    {
        $this->downloadHandler = $downloadHandler;
        $this->em = $em;
    }

    /**
     * @Route("api/file/{id}", methods={"GET"})
     */
    public function getFile(Request $request, $id){

        $appFile = $this->em->find(AppFile::class, $id);
        if(!$appFile)
            throw new NotFoundHttpException('File not found');
        try{
            $response =  $this->downloadHandler->downloadObject($appFile, $fileField = 'file', $objectClass = null, $appFile->getOriginalName());
            $response->headers->set('Content-Disposition', 'inline');
            $response->headers->set('Content-Type', $appFile->getMimeType());
            $response->headers->set('x-file-name',$appFile->getName());
            return $response;
        }catch (\Exception $exception){
            throw new NotFoundHttpException('File not found in filesystem');
        }
    }

    /**
     * @Route("api/file/download/{id}", methods={"GET"}))
     */
    public function downloadFile(Request $request, $id){

        $appFile = $this->em->find(AppFile::class, $id);
        if(!$appFile)
            throw new NotFoundHttpException('File not found');
        try{
            $response =  $this->downloadHandler->downloadObject($appFile, $fileField = 'file', $objectClass = null, $appFile->getOriginalName());
            $response->headers->set('x-file-name',$appFile->getName());
            return $response;
        }catch (\Exception $exception){
            throw new NotFoundHttpException('File not found in filesystem');
        }
    }


    /**
     * @Route("file/{id}", methods={"GET"})
     */
    public function getFile2(Request $request, $id){

        $appFile = $this->em->find(AppFile::class, $id);
        if(!$appFile)
            throw new NotFoundHttpException('File not found');
        try{
            $response =  $this->downloadHandler->downloadObject($appFile, $fileField = 'file', $objectClass = null, $appFile->getOriginalName());
            $response->headers->set('Content-Disposition', 'inline');
            $response->headers->set('Content-Type', $appFile->getMimeType());
            $response->headers->set('x-file-name',$appFile->getName());
            return $response;
        }catch (\Exception $exception){
            throw new NotFoundHttpException('File not found in filesystem');
        }
    }

    /**
     * @Route("file/download/{id}", methods={"GET"})
     */
    public function downloadFile2(Request $request, $id){

        //REVISAR SI EL USUARIO ACTUAL PUEDE VER EL ARCHIVO
        $appFile = $this->em->find(AppFile::class, $id);
        if(!$appFile)
            throw new NotFoundHttpException('File not found');
        try{
            $response =  $this->downloadHandler->downloadObject($appFile, $fileField = 'file', $objectClass = null, $appFile->getOriginalName());
            $response->headers->set('x-file-name',$appFile->getName());
            return $response;
        }catch (\Exception $exception){
            throw new NotFoundHttpException('File not found in filesystem');
        }
    }


}