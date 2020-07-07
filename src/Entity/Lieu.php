<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Cette valeur ne peut pas être vide")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Ne peut contenir un nombre"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message = "Cette valeur ne peut pas être vide")
     * @ORM\Column(type="string", length=255)
     */
    private $rue;

    /**
     * @Assert\Type(type="float", message="Valeur invalide")
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @Assert\Type(type="float", message="Valeur invalide")
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="lieux")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="lieu")
     */
    private $sorties;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param mixed $sorties
     */
    public function setSorties($sorties): void
    {
        $this->sorties = $sorties;
    }

    
}
