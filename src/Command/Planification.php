<?php


namespace App\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Planification extends Command
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure () {

        $this->setName('app:planification');
        $this->setDescription("Mettre à jour les états des sorties");

    }

    public function execute (InputInterface $input, OutputInterface $output) {
        $em= $this->getContainer()->get('doctrine')->getManager();
        $repoSortie =$em->getRepository('App:Sortie');
        $sorties = $repoSortie->findAll();
        $repoEtat = $em->getRepository('App:Etat');
        foreach ($sorties as $sortie){
            if(($sortie->getEtat() == "Ouverte" ||$sortie->getEtat() == "Créée") && $sortie->getDateLimiteInscription<=date("Y-m-d H:i:s")){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Clôturée']));
            }elseif ($sortie->getEtat() == "Clôturée" && $sortie->getDateHeureDebut<=date("Y-m-d H:i:s")){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Activité en cours']));
            }elseif ($sortie->getEtat() == "Activité en cours" && date_add($sortie->getDateLimiteInsciption,$sortie->getDuree())<=date("Y-m-d H:i:s")){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Passée']));
            }
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }


}