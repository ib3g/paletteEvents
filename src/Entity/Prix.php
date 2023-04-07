<?php

namespace App\Entity;

use App\Repository\PrixRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrixRepository::class)]
class Prix
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?float $somme = null;

    #[ORM\ManyToOne(inversedBy: 'prix')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\OneToMany(mappedBy: 'prix', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\Column]
    private ?int $placeMax = null;

    #[ORM\Column(nullable: true)]
    private ?int $placeRestantes = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSomme(): ?float
    {
        return $this->somme;
    }

    public function setSomme(float $somme): self
    {
        $this->somme = $somme;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setPrix($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getPrix() === $this) {
                $ticket->setPrix(null);
            }
        }

        return $this;
    }

    public function getPlaceMax(): ?int
    {
        return $this->placeMax;
    }

    public function setPlaceMax(int $placeMax): self
    {
        $this->placeMax = $placeMax;

        return $this;
    }

    public function getPlaceRestantes(): ?int
    {
        return $this->placeRestantes;
    }

    public function setPlaceRestantes(?int $placeRestantes): self
    {
        $this->placeRestantes = $placeRestantes;

        return $this;
    }
}
