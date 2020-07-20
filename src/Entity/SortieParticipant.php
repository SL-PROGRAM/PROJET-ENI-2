<?php

namespace App\Entity;

use App\Repository\SortieParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SortieParticipantRepository::class)
 * @UniqueEntity(
 *     fields={"sortie", "participant"},
 *     message="Le participant est déjà inscrit"
 * )
 */
class SortieParticipant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sortie::class, inversedBy="sortieParticipants")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $sortie;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="sortieParticipants")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $participant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortie(): ?Sortie
    {
        return $this->sortie;
    }

    public function setSortie(?Sortie $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }


}
