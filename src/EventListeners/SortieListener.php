<?php


namespace App\EventListeners;

use App\Entity\SortieParticipant;
use App\Entity\Sortie;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Class SortieListener
 * Listener d'ajout de participant sur une sortie,
 * vérifie si le nombre max de participants n'est pas dépassé
 * @package App\EventListeners
 */
class SortieListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();


        if ($entity instanceof SortieParticipant) {
            $entityManager = $args->getObjectManager();
            $repo = $entityManager->getRepository('App:SortieParticipant');
            $nbInscrit = intval($repo->createQueryBuilder('s')
                ->select("count(s.id)")
                ->leftJoin('s.sortie', 'sortie')
                ->where("sortie.id = :sortie")
                ->setParameter('sortie', $entity->getSortie()->getId())
                ->getQuery()
                ->getSingleScalarResult());
            $repoSortie = $entityManager->getRepository('App:Sortie');

            $idSortie = $entity->getSortie()->getId();
            if (isset($idSortie)) {
                $sortie = $repoSortie->find($idSortie);
                $nbInscriptionsMax = $sortie->getNbInscriptionMax();
                if ($nbInscrit >= $nbInscriptionsMax) {
                    $entityManager->remove($entity);
                }
            }
            
        }
    }
}