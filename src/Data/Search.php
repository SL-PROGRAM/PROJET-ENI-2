<?php


namespace App\Data;


use App\Entity\Campus;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Search, Gestion des éléments filtres de la recherche
 * @package App\Data
 */
class Search
{
    public $nom ="";

    public $dateHeureDebutMin;

    public $dateHeureDebutMax;

    public $organisateur = false;

    public $inscrit= false;

    public $nonInscrit = false;

    public $passees = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus")
     */
    public $campus;

}