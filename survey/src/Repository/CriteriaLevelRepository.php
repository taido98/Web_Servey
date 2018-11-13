<?php

namespace App\Repository;

use App\Entity\CriteriaLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CriteriaLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method CriteriaLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method CriteriaLevel[]    findAll()
 * @method CriteriaLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CriteriaLevelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CriteriaLevel::class);
    }

    // /**
    //  * @return CriteriaLevel[] Returns an array of CriteriaLevel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CriteriaLevel
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
