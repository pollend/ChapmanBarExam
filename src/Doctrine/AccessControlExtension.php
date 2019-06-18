<?php
namespace App\Doctrine;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Classroom;
use App\Entity\QuizResponse;
use App\Entity\QuizSession;
use App\Entity\User;
use App\Entity\UserQuizAccess;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * Limit the access to certain resources
 * Class AccessControlExtension
 * @package App\Doctrine
 */
class AccessControlExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $user = $this->security->getUser();
        if ($this->security->isGranted(User::ROLE_ADMIN) || null === $user) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];

        switch ($resourceClass) {
            case Classroom::class:
                $queryBuilder
                    ->innerJoin(sprintf('%s.users', $rootAlias), 'extension_user', 'WITH', $queryBuilder->expr()->eq('extension_user.id', ':extension_user'))
                    ->setParameter('extension_user', $user);
                break;
            case UserQuizAccess::class:
                $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.owner',$rootAlias),":extension_owner"))
                    ->setParameter("extension_owner",$user);
                break;
            case QuizSession::class:
                $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.owner',$rootAlias),":extension_owner"))
                    ->setParameter("extension_owner",$user);
                break;
            case QuizResponse::class:
                $queryBuilder->innerJoin(sprintf('%s.session', $rootAlias), 'extension_session')
                    ->andWhere($queryBuilder->expr()->eq('extension_session.owner',':extension_owner'))
                    ->setParameter('extension_owner', $user);
                break;
        }
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $user = $this->security->getUser();
        if ($this->security->isGranted(User::ROLE_ADMIN) || null === $user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        switch ($resourceClass) {
            case Classroom::class:
                $queryBuilder->innerJoin(sprintf('%s.users', $rootAlias), 'extension_user', 'WITH', $queryBuilder->expr()->eq('extension_user.id', ':extension_user'))
                    ->setParameter('extension_user', $user);
                break;
            case UserQuizAccess::class:
                $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.owner', $rootAlias), ":extension_owner"))
                    ->setParameter("extension_owner", $user);
                break;
            case QuizSession::class:
                $queryBuilder->andWhere($queryBuilder->expr()->eq(sprintf('%s.owner', $rootAlias), ":extension_owner"))
                    ->setParameter("extension_owner", $user);
                break;
        }
    }

}