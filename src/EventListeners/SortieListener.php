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
            $sortie = $repoSortie->find($entity->getSortie()->getId());
            $nbInscriptionsMax = $sortie->getNbInscriptionMax();
            if ($nbInscrit >= $nbInscriptionsMax) {
                $entityManager->clear($entity);
                $this->addFlash('danger', 'Vous ne pouvez pas vous inscrire car il n\'y a pas de place disponible');
            }
            $entityManager->flush();
        }
    }
}