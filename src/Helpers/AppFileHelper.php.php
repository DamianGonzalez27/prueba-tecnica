<?php
namespace App\Helpers;

use App\Entity\AppFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFileHelper{

	public function createFromUploadedFile(UploadedFile $uploadedFile, $type=AppFile::GENERAL_FILE){
		$appFile = new AppFile();
		$appFile->setType($type);
		$appFile->setFile($uploadedFile);
		return $appFile;
	}
}