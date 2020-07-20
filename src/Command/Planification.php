<?php


namespace App\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Datetime;
use Symfony\Component\Console\Output\OutputInterface;


/*
 .symfony.cloud.yaml

timezone: Europe/Paris

crons:
    update_base:
        # every day at 3h45 AM */
// spec: */5 * * * *
// cmd: php bin/console app:planification



class Planification extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

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
        $now = new \DateTime('now');
        foreach ($sorties as $sortie){
            //dd ($sortie->getDateHeureDebut()->getTimestamp().'\n'.$sortie->getDuree().'\n'.date('now')->getTimestamp());

            if(($sortie->getEtat() == "Ouverte" ||$sortie->getEtat() == "Créée") && $sortie->getDateLimiteInscription()<=$now){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Clôturée']));
            }elseif ($sortie->getEtat() == "Clôturée" && $sortie->getDateHeureDebut()<=date("Y-m-d H:i:s")){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Activité en cours']));
            }elseif ($sortie->getEtat() == "Activité en cours" && $sortie->getDateHeureDebut()->getTimestamp()+$sortie->getDuree()<=$now->getTimestamp()){
                $sortie->setEtat($repoEtat->findOneBy(['libelle'=>'Passée']));
            }
        }
        return 1;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }


}