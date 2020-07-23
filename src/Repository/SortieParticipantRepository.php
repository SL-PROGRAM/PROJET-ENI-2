<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\SortieParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SortieParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method SortieParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method SortieParticipant[]    findAll()
 * @method SortieParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieParticipantRepository extends ServiceEntityRepository
{
    /**
     * SortieParticipantRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SortieParticipant::class);
    }
    public function getnbInscrit(Sortie $sortie){
        try {
            return intval($this->createQueryBuilder('s')
                ->select("count(s.id)")
                ->leftJoin('s.sortie', 'sortie')
                ->where("sortie.id = :sortie")
                ->setParameter('sortie', $sortie->getId())
                ->getQuery()
                ->getSingleScalarResult());
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    // /**
    //  * @return SortieParticipant[] Returns an array of SortieParticipant objects
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
    public function findOneBySomeField($value): ?SortieParticipant
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
