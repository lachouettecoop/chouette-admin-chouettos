<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;


use App\Repository\PIAFRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Resolver\PiafMutationResolver;

/**
 * @ApiResource(
 *     collectionOperations={"get"}
 * )
 * @ApiFilter(DateFilter::class, properties={"creneau.debut"})
 * @ApiFilter(BooleanFilter::class, properties={"visible": "exact", "pourvu": "exact"})
 * @ApiFilter(SearchFilter::class, properties={"piaffeur": "exact", "statut": "exact", "role.role_unique_id": "iexact"})
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
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $pourvu = false;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $nonPourvu = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $comptabilise;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBeginner;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateReservation;

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

    public function getComptabilise(): ?bool
    {
        return $this->comptabilise;
    }

    public function setIsBeginner(?bool $isBeginner): self
    {
        $this->isBeginner = $isBeginner;

        return $this;
    }

    public function getIsBeginner(): ?bool
    {
        return $this->isBeginner;
    }

    public function setComptabilise(?bool $comptabilise): self
    {
        $this->comptabilise = $comptabilise;

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

    public function getNonPourvu(): ?bool
    {
        return $this->nonPourvu;
    }

    public function setNonPourvu(bool $nonPourvu): self
    {
        $this->nonPourvu = $nonPourvu;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(?\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }



}
