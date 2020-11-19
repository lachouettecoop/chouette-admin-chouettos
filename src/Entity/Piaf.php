<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PIAFRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=PIAFRepository::class)
 */
class Piaf
{
    /**
     * @ORM\Id
     * @ApiProperty(identifier=true)
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\ManyToOne(targetEntity=Role::class)
     */
    private $role;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="piafs")
     */
    private $piaffeur;

    /**
     * @ORM\ManyToOne(targetEntity=Creneau::class, inversedBy="piafs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creneau;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $visible = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $remplacement;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPiaffeur(): ?User
    {
        return $this->piaffeur;
    }

    public function setPiaffeur(?User $piaffeur): self
    {
        $this->piaffeur = $piaffeur;

        return $this;
    }

    public function getCreneau(): ?Creneau
    {
        return $this->creneau;
    }

    public function setCreneau(?Creneau $creneau): self
    {
        $this->creneau = $creneau;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getRemplacement(): ?bool
    {
        return $this->remplacement;
    }

    public function setRemplacement(?bool $remplacement): self
    {
        $this->remplacement = $remplacement;

        return $this;
    }


}
