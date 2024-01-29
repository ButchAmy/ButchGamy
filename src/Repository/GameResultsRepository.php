<?php

namespace App\Repository;

use App\Entity\GameResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameResults>
 *
 * @method GameResults|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameResults|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameResults[]    findAll()
 * @method GameResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameResultsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameResults::class);
    }

//    /**
//     * @return GameResults[] Returns an array of GameResults objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameResults
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
