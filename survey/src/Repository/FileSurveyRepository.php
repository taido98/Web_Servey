<?php

namespace App\Repository;

use App\Entity\FileSurvey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FileSurvey|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileSurvey|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileSurvey[]    findAll()
 * @method FileSurvey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileSurveyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FileSurvey::class);
    }

    // /**
    //  * @return FileSurvey[] Returns an array of FileSurvey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FileSurvey
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
