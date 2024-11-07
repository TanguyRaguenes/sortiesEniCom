<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAndTime = null;

    #[ORM\Column]
    private ?float $duration = null;

    /**
     * @var Collection<int, Place>
     */
    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'trip')]
    private Collection $place;

    #[ORM\Column]
    private ?int $seats = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textNote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDeadline = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    private ?State $state = null;

    #[ORM\ManyToOne(inversedBy: 'tripsOrganizer')]
    private ?Participant $organizer = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'tripsParticipant')]
    private Collection $participants;

    public function __construct()
    {
        $this->place = new ArrayCollection();
        $this->participants = new ArrayCollection();
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

    public function getDateAndTime(): ?\DateTimeInterface
    {
        return $this->dateAndTime;
    }

    public function setDateAndTime(\DateTimeInterface $dateAndTime): static
    {
        $this->dateAndTime = $dateAndTime;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlace(): Collection
    {
        return $this->place;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->place->contains($place)) {
            $this->place->add($place);
            $place->setTrip($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->place->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getTrip() === $this) {
                $place->setTrip(null);
            }
        }

        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): static
    {
        $this->seats = $seats;

        return $this;
    }

    public function getTextNote(): ?string
    {
        return $this->textNote;
    }

    public function setTextNote(?string $textNote): static
    {
        $this->textNote = $textNote;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
