<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

use App\Repository\PIAFRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"piaffeur": "exact"})
 * @ApiFilter(BooleanFilter::class, properties={"visible": "exact"})
 * @ApiFilter(SearchFilter::class, properties={"statut": "exact"})
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
    private $pourvu;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourvu(): ?bool
    {
        return $this->pourvu;
    }

    public function setPourvu(?bool $pourvu): self
    {
        $this->pourvu = $pourvu;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
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



}
