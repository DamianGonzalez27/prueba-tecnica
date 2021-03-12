<?php

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerDecorator implements NormalizerInterface
{
	private $decorated;

	public function __construct(NormalizerInterface $decorated)
	{
		$this->decorated = $decorated;
	}


	public function normalize($object, $format = null, array $context = [])
	{
	    if(!$_SERVER['ENABLE_API_DOCUMENTATION']){
	        throw  new NotFoundHttpException('Serialization for the html not supported');
        }

		$docs = $this->decorated->normalize($object, $format, $context);
        //dd($docs);
	    $this->addEndpointDocumentation($docs, 'Get Gcare Questionnaire', '/gcare', 'get');
        $this->addEndpointDocumentation($docs, 'Post Filled Gcare Questionnaire', '/gcare', 'post');

        //$this->addEndpointDocumentation($docs, 'Validate my acount', '/validate_my_acount', 'post');

		return $docs;
	}

	public function supportsNormalization($data, $format = null)
	{
		return $this->decorated->supportsNormalization($data, $format);
	}

	private function addEndpointDocumentation(&$docs, $description, $route, $method, $parameters = [], $requestBody = []){
        $docs['paths'][$route][$method]['summary'] = $description;
        $docs['paths'][$route][$method]['responses'] = ['responses'=>['200'=>[]]];
        $docs['paths'][$route][$method]['tags'] = ['Gcare'];
        $docs['paths'][$route][$method]['parameters'] = $parameters;
    }
}
