<?php

namespace App\Repository;

use App\Entity\CallReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CallReport>
 */
class CallReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallReport::class);
    }

    /**
    * @return array Returns an array of call statistics
    */
    public function getStatistics(): array
    {
        
        $sql = "
            WITH t1 AS (
                SELECT 
                    customer_id, 
                    COUNT(id) as total_calls_internal, 
                    SUM(duration) as total_duration_internal
                FROM 
                    call_report
                WHERE 
                    internal_call = true 
                GROUP BY 
                    customer_id
            ), t2 AS (
                SELECT 
                    customer_id, 
                    COUNT(id) as total_calls, 
                    SUM(duration) as total_duration
                FROM 
                    call_report 
                GROUP BY 
                    customer_id
            )
            SELECT 
                t2.customer_id, 
                IFNULL(t1.total_calls_internal, 0) as total_calls_internal, 
                IFNULL(t1.total_duration_internal, 0) as total_duration_internal,
                t2.total_calls,
                t2.total_duration
            FROM 
                t2
            LEFT JOIN 
                t1 ON t2.customer_id = t1.customer_id
        ";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAllAssociative();
    }

    //    public function findOneBySomeField($value): ?CallReport
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
