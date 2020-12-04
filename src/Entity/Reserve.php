<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ReserveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ReserveRepository::class)
 */
class Reserve
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="reserve", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=CreneauGenerique::class, inversedBy="reserves")
     */
    private $creneauGeneriques;

    public function __construct()
    {
        $this->creneauGeneriques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|CreneauGenerique[]
     */
    public function getCreneauGeneriques(): Collection
    {
        return $this->creneauGeneriques;
    }

    public function addCreneauGenerique(CreneauGenerique $creneauGenerique): self
    {
        if (!$this->creneauGeneriques->contains($creneauGenerique)) {
            $this->creneauGeneriques[] = $creneauGenerique;
        }

        return $this;
    }

    public function removeCreneauGenerique(CreneauGenerique $creneauGenerique): self
    {
        $this->creneauGeneriques->removeElement($creneauGenerique);

        return $this;
    }
}
