<?php

namespace App\Repository;

use App\Entity\Act;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Act|null find($id, $lockMode = null, $lockVersion = null)
 * @method Act|null findOneBy(array $criteria, array $orderBy = null)
 * @method Act[]    findAll()
 * @method Act[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Act::class);
    }

    public function findByCompanyID($companyId,$id)
    {
        return $this->createQueryBuilder('act')
            ->andWhere('act.company = :company')
            ->andWhere('act.id = :id')
            ->setParameters(array(':company'=> $companyId, ':id'=>$id))
            ->orderBy('act.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Act[] Returns an array of Act objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Act
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
