<?php


namespace App\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use \Datetime;
use Symfony\Component\Console\Output\OutputInterface;


/**
 .symfony.cloud.yaml

timezone: Europe/Paris

crons:
    update_base:
        #Toutes les 5 minutes
// spec: *\/5 * * * *
// cmd: php bin/console app:planification

 *
 * Cette classe crée une commande symfony, elle peut être mise en fonctionnement via les Cron Jobs SymfonyCloud
*/

class Planification extends Command
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Planification constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    /**
     * Configuration de la commande
     */
    protected function configure () {

        $this->setName('app:planification');
        $this->setDescription("Mettre à jour les états des sorties");

    }

    /**
     * Exécution de la commande
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
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