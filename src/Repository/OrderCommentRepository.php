<?php

namespace App\Repository;

use App\Entity\OrderComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderComment[]    findAll()
 * @method OrderComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderComment::class);
    }

    // /**
    //  * @return OrderComments[] Returns an array of OrderComments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderComments
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
