<?php

namespace App\Repository;

use App\Data\Search;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    /**
     * SortieRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    /**
     * @param Search $search
     * @param Participant $participant
     * @return int|mixed|string
     */
    public function findSearch(Search $search, Participant $participant)
    {

        $query = $this
            ->createQueryBuilder('s')
            ->select('e','c', 's','sp','p')
            ->leftJoin('s.campus', 'c')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.sortieParticipants','sp')
            ->leftJoin('sp.participant','p');


        if ($search->organisateur) {
            $query = $query
                ->orWhere('s.organisateur = :participant')
                ->setParameter('participant', $participant->getId());
        }

        if ($search->inscrit) {
            $query = $query
                ->orWhere('sp.participant = :participant')
                ->setParameter('participant', $participant->getId());
        }
        if ($search->nonInscrit) {
            $query = $query
                ->orWhere('sp.participant != :participant')
                ->orWhere('sp.participant is null')
                ->setParameter('participant', $participant->getId());
        }
        if ($search->passees) {
            $query = $query
                ->orWhere('e.libelle = \'Passée\'');
        }

        if (!empty($search->nom)) {
            $query = $query
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%{$search->nom}%");
        }

        if (!empty($search->dateHeureDebutMin)) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateHeureDebutMin')
                ->setParameter('dateHeureDebutMin', $search->dateHeureDebutMin);
        }

        if (!empty($search->dateHeureDebutMax)) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :dateHeureDebutMax')
                ->setParameter('dateHeureDebutMax', $search->dateHeureDebutMax);
        }


        if ($search->campus) {
            $query = $query
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus', $search->campus);
        }

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
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
    public function findOneBySomeField($value): ?Sortie
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
