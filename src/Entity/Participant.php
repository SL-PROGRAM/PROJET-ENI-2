<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message = "Cette valeur ne peut pas être vide")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom ne doit pas contenir de nombre"
     * )
     *  @Assert\Length(max="100", min="3",
     *      minMessage="Le nom de la sortie doit au mininmum contenir 3 caractères ")
     *      maxMessage="Le nom de la sortie doit au maximum contenir 100 caractères ")
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message = "Cette valeur ne peut pas être vide")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre prénom ne doit pas contenir de nombre"
     * )
     *  @Assert\Length(max="50", min="3",
     *      minMessage="Le nom de la sortie doit au mininmum contenir 3 caractères ")
     *      maxMessage="Le nom de la sortie doit au maximum contenir 50 caractères ")
     * @ORM\Column(type="string", length=50)
     *
     */
    private $prenom;

    /**
     * @Assert\Regex(
     *     pattern="/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/",
     *     match=true,
     *     message="Le format du numéro de téléphone est incorrect"
     * )
     *  @Assert\Length(max="10", min="15",
     *      minMessage="Un numero de téléphone doit au mininmum contenir 10 caractères  ")
     *      maxMessage="Un numero de téléphone doit au mininmum contenir 15 caractères ")
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;


    /**
     * @ORM\OneToMany(targetEntity=SortieParticipant::class, mappedBy="participant", orphanRemoval=true)
     */
    private $sortieParticipants;

    /**
     * @Assert\NotNull(message="Merci de renseigner le Campus")
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="Participants")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organisateur")
     */
    private $creerSorties;

    /**
     *  @Assert\Length(max="3", min="50",
     *      minMessage="Un numero de téléphone doit au mininmum contenir 3 caractères  ")
     *      maxMessage="Un numero de téléphone doit au mininmum contenir 50 caractères ")
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @Assert\Image(maxSize="1M", maxHeight="1920", maxWidth="1080")
     */
    private $imageFile;

    /**
     * @return mixed
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     */
    public function setImageFile($imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function __construct()
    {
        $this->sortieParticipants = new ArrayCollection();
        $this->creerSorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

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
            $sortieParticipant->setParticipant($this);
        }

        return $this;
    }

    public function removeSortieParticipant(SortieParticipant $sortieParticipant): self
    {
        if ($this->sortieParticipants->contains($sortieParticipant)) {
            $this->sortieParticipants->removeElement($sortieParticipant);
            // set the owning side to null (unless already changed)
            if ($sortieParticipant->getParticipant() === $this) {
                $sortieParticipant->setParticipant(null);
            }
        }

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

    /**
     * @return Collection|Sortie[]
     */
    public function getCreerSorties(): Collection
    {
        return $this->creerSorties;
    }

    public function addCreerSorty(Sortie $creerSorty): self
    {
        if (!$this->creerSorties->contains($creerSorty)) {
            $this->creerSorties[] = $creerSorty;
            $creerSorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeCreerSorty(Sortie $creerSorty): self
    {
        if ($this->creerSorties->contains($creerSorty)) {
            $this->creerSorties->removeElement($creerSorty);
            // set the owning side to null (unless already changed)
            if ($creerSorty->getOrganisateur() === $this) {
                $creerSorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom(). " ".$this->getPrenom();
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }


}
