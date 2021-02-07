<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"read:creneauGenerique"})
     * @ORM\ManyToOne(targetEntity=Role::class, inversedBy="postes")
     */
    private $role;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="poste")
     */
    private $reservationChouettos;

    /**
     * @ORM\ManyToOne(targetEntity=CreneauGenerique::class, inversedBy="postes")
     */
    private $creneauGenerique;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}
