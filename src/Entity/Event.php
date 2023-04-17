<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    // les status de l'event sont : new, en cours, terminé, annulé, reporté
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED = 'finished';
    const STATUS_CANCELED = 'canceled';
    const STATUS_POSTPONED = 'postponed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 3000)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $shortDescription = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Demande::class)]
    private Collection $demandes;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'animatedEvents')]
    private Collection $animators;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'events')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'events')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Media::class, orphanRemoval: true)]
    private Collection $medias;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Prix::class)]
    private Collection $prix;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEvent = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sponsors = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
        $this->animators = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->prix = new ArrayCollection();
        $this->status = self::STATUS_NEW;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setEvent($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getEvent() === $this) {
                $demande->setEvent(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAnimators(): Collection
    {
        return $this->animators;
    }

    public function addAnimator(User $animator): self
    {
        if (!$this->animators->contains($animator)) {
            $this->animators->add($animator);
        }

        return $this;
    }

    public function removeAnimator(User $animator): self
    {
        $this->animators->removeElement($animator);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addEvent($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeEvent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addEvent($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeEvent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setEvent($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getEvent() === $this) {
                $media->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prix>
     */
    public function getPrix(): Collection
    {
        return $this->prix;
    }

    public function addPrix(Prix $prix): self
    {
        if (!$this->prix->contains($prix)) {
            $this->prix->add($prix);
            $prix->setEvent($this);
        }

        return $this;
    }

    public function removePrix(Prix $prix): self
    {
        if ($this->prix->removeElement($prix)) {
            // set the owning side to null (unless already changed)
            if ($prix->getEvent() === $this) {
                $prix->setEvent(null);
            }
        }

        return $this;
    }

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->dateEvent;
    }

    public function setDateEvent(\DateTimeInterface $dateEvent): self
    {
        $this->dateEvent = $dateEvent;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getSponsors(): ?string
    {
        return $this->sponsors;
    }

    public function setSponsors(?string $sponsors): self
    {
        $this->sponsors = $sponsors;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
