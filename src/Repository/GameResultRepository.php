<?php

namespace App\Repository;

use App\Entity\GameResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameResult>
 *
 * @method GameResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameResult[]    findAll()
 * @method GameResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameResult::class);
    }

//    /**
//     * @return GameResult[] Returns an array of GameResult objects
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

//    public function findOneBySomeField($value): ?GameResult
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
