<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.',)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?int $phone = null;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'organizer')]
    private Collection $tripsOrganizer;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\ManyToMany(targetEntity: Trip::class, mappedBy: 'participants')]
    private Collection $tripsParticipant;

    #[ORM\Column(length: 255)]
    private ?string $photoProfil = null;

    public function __construct(String $name, String $firstName, String $username, int $phone, String $photoProfil)
    {
        $this->tripsOrganizer = new ArrayCollection();
        $this->tripsParticipant = new ArrayCollection();
        $this->name = $name;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->phone = $phone;
        $this->photoProfil = $photoProfil;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTripsOrganizer(): Collection
    {
        return $this->tripsOrganizer;
    }

    public function addTripsOrganizer(Trip $tripsOrganizer): static
    {
        if (!$this->tripsOrganizer->contains($tripsOrganizer)) {
            $this->tripsOrganizer->add($tripsOrganizer);
            $tripsOrganizer->setOrganizer($this);
        }

        return $this;
    }

    public function removeTripsOrganizer(Trip $tripsOrganizer): static
    {
        if ($this->tripsOrganizer->removeElement($tripsOrganizer)) {
            // set the owning side to null (unless already changed)
            if ($tripsOrganizer->getOrganizer() === $this) {
                $tripsOrganizer->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTripsParticipant(): Collection
    {
        return $this->tripsParticipant;
    }

    public function addTripsParticipant(Trip $tripsParticipant): static
    {
        if (!$this->tripsParticipant->contains($tripsParticipant)) {
            $this->tripsParticipant->add($tripsParticipant);
            $tripsParticipant->addParticipant($this);
        }

        return $this;
    }

    public function removeTripsParticipant(Trip $tripsParticipant): static
    {
        if ($this->tripsParticipant->removeElement($tripsParticipant)) {
            $tripsParticipant->removeParticipant($this);
        }

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }
}