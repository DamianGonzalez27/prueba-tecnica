<?php
namespace App\Validators;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidAppFile extends Constraint
{
    public function validatedBy()
	{
		return ValidAppFileValidator::class;
	}

	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

}