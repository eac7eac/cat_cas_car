<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Tag $entity, bool $flush = true): void
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
    public function remove(Tag $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->createQueryBuilder('t');

        if ($search) {
            $qb
                ->andWhere('t.name LIKE :search OR t.slug LIKE :search')
                ->setParameter('search', "%$search%")
            ;
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb
            ->innerJoin('t.articles', 'a')
            ->addSelect('a')
            ->orderBy('t.createdAt', 'DESC')
            ;
    }
}
