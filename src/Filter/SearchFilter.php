<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\QueryBuilder;

final class SearchFilter extends AbstractFilter
{
    //https://github.com/api-platform/core/issues/398

    /**
     * @param string $property
     * @param $value
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param string|null $operationName
     *
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \HttpInvalidParamException
     * @throws \ReflectionException
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property === 'search') {
            $this->logger->info('Search for: ' . $value);
        } else {
            return;
        }

        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass(new $resourceClass), 'App\\Filter\\SearchAnnotation');

        if (!$annotation) {
            throw new \HttpInvalidParamException('No Search implemented.');
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $searchItems = explode(' ', str_replace('-', ' ', $value));

        if (is_array($searchItems)) {
            $andx = $queryBuilder->expr()->andx();

            foreach ($searchItems as $index => $searchItem) {
                $orx = $queryBuilder->expr()->orx();

                foreach ($annotation->fields as $field) {
                    $orx->add($queryBuilder->expr()->like('o.' . $field, ':' . $parameterName . '_' . $index));
                }

                if ($orx->count()) {
                    $queryBuilder->setParameter($parameterName . '_' . $index, '%' . $searchItem . '%');
                    $andx->add($orx);
                }
            }

            if ($andx->count()) {
                $queryBuilder->andWhere($andx);
            }
        }
    }


    /**
     * @param string $resourceClass
     * @return array
     */
    public function getDescription(string $resourceClass): array
    {
        $reader = new AnnotationReader();
        $annotation = $reader->getClassAnnotation(new \ReflectionClass(new $resourceClass), \App\Filter\SearchAnnotation::class);

        $description['search'] = [
            'property' => 'search',
            'type' => 'string',
            'required' => false,
            'swagger' => ['description' => 'FullTextFilter on ' . implode(', ', $annotation->fields)],
        ];

        return $description;
    }
}