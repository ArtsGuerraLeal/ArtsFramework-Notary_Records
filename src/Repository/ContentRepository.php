<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    /**
     * @param $companyId
     * @return Content[] Returns an array of Content objects
     */

    public function findByCompany($companyId)
    {
        return $this->createQueryBuilder('content')
            ->andWhere('content.act = :val')
            ->setParameter('val', $companyId)
            ->orderBy('content.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findMaxNumber($act_id)
    {
        try {
            return $this->createQueryBuilder('content')
                ->select("max(content.number)")
                ->where('content.act = :val')
                ->setParameter('val', $act_id)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    public function findMaxConsecutive($act_id)
    {
        try {
            return $this->createQueryBuilder('content')
                ->select("max(content.consecutive)")
                ->where('content.act = :val')
                ->setParameter('val', $act_id)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }



    /**
     * @param $companyId
     * @param $id
     * @return Content
     * @throws NonUniqueResultException
     */
    public function findOneByCompanyID($act_id,$id)
    {
        return $this->createQueryBuilder('content')
            ->andWhere('content.act = :act')
            ->andWhere('content.id = :id')
            ->setParameter('act', $act_id)
            ->setParameter('id', $id)
            ->orderBy('content.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


    public function updateContent($act_id,$content_id,$value){

        return $this->createQueryBuilder('content')
            ->update()
            ->Where('content.id = :val')
            ->andWhere('content.act = :act')
            ->setParameter('val', $content_id)
            ->setParameter('act', $act_id)
            ->set('content.data',$value)
            ->getQuery()
            ;

    }

    public function countElements($act_id)
    {
        try {
            return $this->createQueryBuilder('content')
                ->select("count(content.id)")
                ->where('content.act = :val')
                ->setParameter('val', $act_id)
                ->orderBy('content.id', 'ASC')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }



    /**
     * @param $start
     * @param $length
     * @param $search
     * @param $orders
     * @param $columns
     * @param $act_id
     * @return Content[] Returns an array of Content objects
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findDataTable($start, $length,$search,$orders,$columns,$act_id)
    {

        $query = $this->createQueryBuilder('content');
        $countQuery = $this->createQueryBuilder('content');

        $countQuery->select('COUNT(content)');

        $query->select('content.id')
            ->addSelect('content.number')
            ->addSelect('content.date')
            ->addSelect('content.data')

            ->addSelect('content.date_created')
            ->addSelect('content.consecutive')
            ->where('content.act = :val');

        $searchQuery = null;

        if ($search['value'] != '') {


            if(is_numeric($search['value'])){
                $searchItem = $search['value'];

                $searchQuery = 'content.number LIKE \'%' . $searchItem . '%\'';

            }elseif(!is_numeric($search['value'])){

                $searchItem = $search['value'];

                $searchQuery = 'content.content LIKE \'%' . $searchItem . '%\'';

            }
        }


        if ($searchQuery !== null) {
            $query->andWhere($searchQuery);
            $countQuery->andWhere($searchQuery);
        }

        /*
                foreach ($columns as $key => $column)
                {
                    if ($search['value'] != '') {

                        // $searchItem is what we are looking for
                        $searchItem = $search['value'];
                        $searchQuery = null;

                        // $column['name'] is the name of the column as sent by the JS
                        switch ($column['data']) {
                            case 'id':
                            {
                                $searchQuery = 'patient.id LIKE \'%' . $searchItem . '%\'';
                                break;
                            }
                            case 'firstName':
                            {
                                $searchQuery = 'patient.firstName LIKE \'%' . $searchItem . '%\'';
                                break;
                            }
                            case 'lastName':
                            {
                                $searchQuery = 'patient.lastName LIKE \'%' . $searchItem . '%\'';
                                break;
                            }
                        }

                        if ($searchQuery !== null) {
                            $query->andWhere($searchQuery);
                            $countQuery->andWhere($searchQuery);
                        }
                    }
                }

               */



        $query
            ->setParameter('val', $act_id)
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->orderBy('content.id', 'DESC');

        $results = $query->getQuery()->getArrayResult();
        $countResult = $countQuery->getQuery()->getSingleScalarResult();

        return array(
            "results" 		=> $results,
            "countResult"	=> $countResult
        );


    }

}
