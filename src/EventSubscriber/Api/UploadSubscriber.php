<?php

namespace App\EventSubscriber\Api;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Annotation\AppFileAnnotation;
use App\Entity\AppFile;
use App\Entity\User;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Util\ClassUtils;

final class UploadSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var User
     */
    private $user;

    /**
     * @var PropertyInfoExtractorInterface
     */
    private $propertyInfoExtractor;

    /**
     * @var AnnotationReader $annotationReader
     */
    private $annotationReader;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token, PropertyInfoExtractorInterface $propertyInfoExtractor, Reader $annotationReader)
    {
        $this->em = $em;
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->annotationReader = $annotationReader;
        if ($token->getToken()) $this->user = $token->getToken()->getUser();
    }


    public static function getSubscribedEvents()
    {
        return [KernelEvents::VIEW => ['preWrite', EventPriorities::PRE_WRITE]];
    }

    /**
     * @param ViewEvent $event
     * @throws \Exception
     */
    public function preWrite(ViewEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($method === Request::METHOD_DELETE) return;

        //Check if request have any file
        $files = $event->getRequest()->files->getIterator();
        if (count($files) === 0) return;


        foreach ($files as $propName => $upload) {
            if (is_array($upload)) {
                foreach ($upload as $file) {
                    $this->validateFile($file);
                    $this->addFileToEntity($entity, $propName, $file);
                }
            }
            else {
                $this->validateFile($upload);
                $this->addFileToEntity($entity, $propName, $upload);
            }
        }
    }

    /**
     * @param $entity
     * @param $propName
     * @param UploadedFile $file
     * @throws \Exception
     */
    private function addFileToEntity($entity, $propName, UploadedFile $file)
    {



        $appFileProperty = $this->getAppFileProperty($entity, $propName);

        // Si no alguna propiedad de archivo en la entidad, se ignora este lstener para que el archivo pueda ser manejado en un controlador
        if (!$appFileProperty)
            return;

        //Buscar alguna propiedad de tipo appFile
        $method = $this->getAddOrSetAppFileMethod($entity, $appFileProperty);

        if (!$method) throw new \Exception('Can not create appFile for class ' . get_class($entity) . ', creation method not found');

        $realClass = ClassUtils::getClass($entity);
        $refProperty = new \ReflectionProperty($realClass, $appFileProperty);

        $fileType = AppFile::GENERAL_FILE;
        if ($annotation = $this->annotationReader->getPropertyAnnotation($refProperty, AppFileAnnotation::class)) {
            if ($annotation->fileType) $fileType = $annotation->fileType;
        }

        $appFile = $this->createFile($file, $fileType);
        $entity->$method($appFile);
    }


    //If prop name does not exists, the property to fill is the first AppFile property found
    private function getAppFileProperty($entity, $propName)
    {

        $columns = $this->em->getClassMetadata(get_class($entity))->getAssociationNames();

        if (in_array($propName, $columns)) return $propName;


        $appFileProps = [];
        foreach ($this->propertyInfoExtractor->getProperties(get_class($entity)) as $propName) {

            $propertyTypes = $this->propertyInfoExtractor->getTypes(get_class($entity), $propName);
            if (is_array($propertyTypes)) {
                foreach ($propertyTypes as $type) {
                    if ($type->getClassName() === AppFile::class || ($type->getCollectionValueType() && $type->getCollectionValueType()->getClassName() === AppFile::class)) {
                        $appFileProps[] = $propName;
                    }
                }
            }
        }

        if(count($appFileProps)>1)
            throw new \Exception('Dangerous appFile convention usage, property name not found and more than one AppFile props in entity '.get_class($entity));

        if(count($appFileProps)===1)
            return $appFileProps[0];

        return null;
    }

    public function getAddOrSetAppFileMethod($entity, $propertyName)
    {
        $arrayMethodName = 'add' . ucfirst(Inflector::singularize($propertyName));

        if (method_exists($entity, $arrayMethodName) && is_callable([$entity, $arrayMethodName])) {
            return $arrayMethodName;
        }

        $singularMethodName = 'set' . ucfirst(Inflector::singularize($propertyName));
        if (method_exists($entity, $singularMethodName) && is_callable([$entity, $singularMethodName])) {
            return $singularMethodName;
        }
        return null;
    }

    private function createFile(UploadedFile $uploadedFile, $fileType)
    {
        $appFile = new AppFile();
        $appFile->setType($fileType);
        $appFile->setFile($uploadedFile);
        return $appFile;
    }

    private function validateFile($file)
    {
        if ($file->getError()) {
            throw new HttpException(400, $file->getErrorMessage());
        }
    }

}
