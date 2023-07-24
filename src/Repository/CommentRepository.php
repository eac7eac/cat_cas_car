<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comment $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param string|null $search
     * @param bool $withSoftDeletes
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->createQueryBuilder('c');

        if ($search) {
            $qb
                ->andWhere('c.content LIKE :search OR c.authorName LIKE :search OR a.title LIKE :search')
                ->setParameter('search', "%$search%")
            ;
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb
            ->innerJoin('c.article', 'a')
            ->addSelect('a')
            ->orderBy('c.createdAt', 'DESC')
            ;
    }

    /**
     * @param int|null $number
     * @return array
     */
    public function numberLastComments(?int $number): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->innerJoin('c.article', 'a')
            ->addSelect('a')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults("$number")
            ->getQuery()
            ->getResult();
    }
}
