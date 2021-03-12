<?php

namespace App\Validators;

use App\Entity\AppFile;
use App\Entity\Schedule;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class ValidAppFileValidator extends ConstraintValidator
{


    public function validate($appFile, Constraint $constraint)
    {
        /** @var AppFile $appFile */
        if(!$file = $appFile->getFile())
            $this->context->buildViolation('No se encontró el archivo');
        else{
            $mimeType = $file->getMimeType();
            switch ($appFile->getType()){
                case AppFile::PROFILE_IMAGE_TYPE:
                    if(!in_array($mimeType,AppFile::IMAGE_MIME_TYPES))
                        $this->context->buildViolation('Tipo de imágen ('.$mimeType.') no aceptado')->addViolation();
                    break;
                case AppFile::INCIDENCE_JUSTIFICATION_TYPE:
                    if(!in_array($mimeType,AppFile::JUSTIFICATION_MIME_TYPES))
                        $this->context->buildViolation('Tipo de archivo ('.$mimeType.') no aceptado')->addViolation();
                    break;
                default:
                    $this->context->buildViolation('Tipo de archivo inválido')->addViolation();
                    break;
            }
        }

    }
}
