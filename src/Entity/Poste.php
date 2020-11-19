<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=PosteRepository::class)
 */
class Poste
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="postes")
     */
    private $role;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="poste", cascade={"persist", "remove"})
     */
    private $reservationChouettos;

    /**
     * @ORM\ManyToOne(targetEntity=CreneauGenerique::class, inversedBy="postes")
     */
    private $creneauGenerique;

    public function __construct()
    {
        $this->role = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRole(): Collection
    {
        return $this->role;
    }

    public function addRole(Role $role): self
    {
        if (!$this->role->contains($role)) {
            $this->role[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->role->removeElement($role);

        return $this;
    }

    public function getReservationChouettos(): ?User
    {
        return $this->reservationChouettos;
    }

    public function setReservationChouettos(?User $reservationChouettos): self
    {
        $this->reservationChouettos = $reservationChouettos;

        return $this;
    }

    public function getCreneauGenerique(): ?CreneauGenerique
    {
        return $this->creneauGenerique;
    }

    public function setCreneauGenerique(?CreneauGenerique $creneauGenerique): self
    {
        $this->creneauGenerique = $creneauGenerique;

        return $this;
    }
}
