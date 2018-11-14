<?php

namespace App\Repository;

use App\Entity\ClassSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ClassSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassSubject[]    findAll()
 * @method ClassSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassSubjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClassSubject::class);
    }

    // /**
    //  * @return ClassSubject[] Returns an array of ClassSubject objects
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
    public function findOneBySomeField($value): ?ClassSubject
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
