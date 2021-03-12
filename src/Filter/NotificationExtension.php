<?php
namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class NotificationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    private $tokenStorage;
    private $em;

    public function __construct( TokenStorageInterface $tokenStorage,  EntityManagerInterface $em ) {
        $this->tokenStorage = $tokenStorage;
        $this->em           = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function applyToCollection( QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null ) {
        $this->addWhere( $queryBuilder, $resourceClass );
    }

    /**
     * {@inheritdoc}
     */
    public function applyToItem( QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [] ) {
        $this->addWhere( $queryBuilder, $resourceClass );
    }

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     */
    private function addWhere( QueryBuilder $queryBuilder, string $resourceClass ) {
        if ( $resourceClass !== Notification::class )
            return;

        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.user = :user', $rootAlias));
        $queryBuilder->setParameter('user', $user);
    }
}