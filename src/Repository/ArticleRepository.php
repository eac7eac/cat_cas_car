<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Article $entity, bool $flush = true): void
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
    public function remove(Article $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */

    public function findLatestPublished()
    {
        return $this->published($this->latest())
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllPublishedLastWeek(): array
    {
        return $this->published($this->latest())
            ->andWhere('a.publishedAt >= :week_ago')
            ->setParameter('week_ago', new \DateTime('-1 week'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function totalCountForPeriod(string $dateFrom, string $dateTo): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.publishedAt >= :date_from AND a.publishedAt <= :date_to')
            ->setParameter('date_from', date('Y-m-d', strtotime($dateFrom)))
            ->setParameter('date_to', date('Y-m-d', strtotime($dateTo)))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function publishedCountForPeriod(string $dateFrom, string $dateTo): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.publishedAt >= :date_from AND a.publishedAt <= :date_to AND a.publishedAt IS NOT NULL')
            ->setParameter('date_from', date('Y-m-d', strtotime($dateFrom)))
            ->setParameter('date_to', date('Y-m-d', strtotime($dateTo)))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */

    public function findLatest()
    {
        return $this->latest()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of Article objects
     */

    public function findPublished()
    {
        return $this->published()
            ->getQuery()
            ->getResult();
    }

    private function published(QueryBuilder $queryBuilder = null)
    {
        return $this->getOrCreateQueryBuilder($queryBuilder)->andWhere('a.publishedAt IS NOT NULL');
    }

    public function latest(QueryBuilder $queryBuilder = null)
    {
        return $this->getOrCreateQueryBuilder($queryBuilder)->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * @param QueryBuilder|null $queryBuilder
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('a');
    }

    public function findAllWithSearchQuery(?string $search)
    {
        $qb = $this->createQueryBuilder('a');

        if ($search) {
            $qb
                ->andWhere('a.description LIKE :search OR u.firstName LIKE :search OR a.title LIKE :search')
                ->setParameter('search', "%$search%")
            ;
        }

        return $qb
            ->innerJoin('a.author', 'u')
            ->addSelect('u')
            ->orderBy('a.publishedAt', 'DESC')
            ;
    }
}
