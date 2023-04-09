<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_ORGANISATEUR = 'ROLE_ORGANISATEUR';
    const ROLE_ANIMATEUR = 'ROLE_ANIMATEUR';
    const ROLE_USER = 'ROLE_USER';

    const ROLES = [
        self::ROLE_ADMIN => ['Administrateur', 1],
        self::ROLE_ORGANISATEUR => ['Organisateur', 2],
        self::ROLE_ANIMATEUR => ['Animateur', 3],
        self::ROLE_USER => ['user', 4],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\ManyToMany(inversedBy: 'roles', targetEntity: User::class)]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRole() === $this) {
                $user->setRole(null);
            }
        }

        return $this;
    }
}
