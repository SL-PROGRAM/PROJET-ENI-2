<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
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
     * @Assert\Type(type="\DateTime", message="Date invalide")
     * @Assert\GreaterThan("+24 hours")
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @Assert\Type(type="integer", message="Valeur durée invalide")
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @Assert\Type(type="\DateTime", message="Date invalide")
     * @Assert\GreaterThan("+2 hours")
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @Assert\Type(type="integer", message="Valeur inscription invalide")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbInscriptionMax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $infosSortie;

    /**
     * @ORM\OneToMany(targetEntity=SortieParticipant::class, mappedBy="sortie", orphanRemoval=true)
     */
    private $sortieParticipants;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sorties")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="creerSorties")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sorties")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="sorties")
     */
    private $ville;



    public function __construct()
    {
        $this->sortieParticipants = new ArrayCollection();
    }



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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(?int $nbInscriptionMax): self
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }


    /**
     * @return Collection|SortieParticipant[]
     */
    public function getSortieParticipants(): Collection
    {
        return $this->sortieParticipants;
    }

    public function addSortieParticipant(SortieParticipant $sortieParticipant): self
    {
        if (!$this->sortieParticipants->contains($sortieParticipant)) {
            $this->sortieParticipants[] = $sortieParticipant;
            $sortieParticipant->setSortie($this);
        }

        return $this;
    }

    public function removeSortieParticipant(SortieParticipant $sortieParticipant): self
    {
        if ($this->sortieParticipants->contains($sortieParticipant)) {
            $this->sortieParticipants->removeElement($sortieParticipant);
            // set the owning side to null (unless already changed)
            if ($sortieParticipant->getSortie() === $this) {
                $sortieParticipant->setSortie(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
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
}
