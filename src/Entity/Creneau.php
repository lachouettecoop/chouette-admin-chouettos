<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CreneauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ApiFilter(DateFilter::class, properties={"debut"})
 * @ApiFilter(SearchFilter::class, properties={"piafs.statut": "exact", "piafs.piaffeur.email": "exact", "piafs.role.role_unique_id": "iexact"})
 * @ORM\Entity(repositoryClass=CreneauRepository::class)
 */
class Creneau
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CreneauGenerique::class, inversedBy="creneaux")
     */
    private $creneauGenerique;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $debut;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fin;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $titre;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\OneToMany(targetEntity=Piaf::class, mappedBy="creneau", orphanRemoval=true, cascade={"persist"})
     */
    private $piafs;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $horsMag = true;

    public function __toString()
    {
        return (string)$this->debut->format('d/m/Y');
    }

    public function __construct()
    {
        $this->piafs = new ArrayCollection();
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

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    /**
     * @return Collection|Piaf[]
     */
    public function getPiafs(): Collection
    {
        return $this->piafs;
    }

    public function addPiaf(Piaf $piaf): self
    {
        if (!$this->piafs->contains($piaf)) {
            $this->piafs[] = $piaf;
            $piaf->setCreneau($this);
        }

        return $this;
    }

    public function removePiaf(Piaf $piaf): self
    {
        if ($this->piafs->removeElement($piaf)) {
            // set the owning side to null (unless already changed)
            if ($piaf->getCreneau() === $this) {
                $piaf->setCreneau(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(?\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getHorsMag(): ?bool
    {
        return $this->horsMag;
    }

    public function setHorsMag(?bool $horsMag): self
    {
        $this->horsMag = $horsMag;

        return $this;
    }

}
