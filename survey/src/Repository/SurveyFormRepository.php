<?php

namespace App\Repository;

use App\Entity\SurveyForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SurveyForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method SurveyForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method SurveyForm[]    findAll()
 * @method SurveyForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SurveyFormRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SurveyForm::class);
    }

    // /**
    //  * @return SurveyForm[] Returns an array of SurveyForm objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SurveyForm
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
