<?php


namespace App\EventListeners;

use App\Entity\SortieParticipant;
use App\Entity\Sortie;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SortieListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();


        if ($entity instanceof SortieParticipant) {
            $entityManager = $args->getObjectManager();
            $repo = $entityManager->getRepository('App:SortieParticipant');
            $nbInscrit = $repo->createQueryBuilder('s')
                ->select("count(s.id)")
                ->leftJoin('s.sortie', 'sortie')
                ->where("sortie.id = :sortie")
                ->setParameter('sortie', $entity->getSortie()->getId())
                ->getQuery()
                ->getSingleScalarResult();
            $repoSortie = $entityManager->getRepository('App:Sortie');
            $idSortie = $entity->getSortie()->getId();
            if($idSortie!=null) {
                $sortie = $repoSortie->find();
                $nbInscriptionsMax = $sortie->getNbInscriptionMax();
                if ($nbInscrit >= $nbInscriptionsMax) {
                    $entityManager->remove($entity);

                }
            }
            $entityManager->flush();
        }
    }
}