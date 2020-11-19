<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CreneauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureFin;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\OneToMany(targetEntity=Piaf::class, mappedBy="creneau", orphanRemoval=true, cascade={"persist"})
     */
    private $piafs;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;

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

}
