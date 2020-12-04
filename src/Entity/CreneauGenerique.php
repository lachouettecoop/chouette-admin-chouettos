<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CreneauGeneriqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(normalizationContext={"groups"={"read:creneauGenerique"}},)
 * @ORM\Entity(repositoryClass=CreneauGeneriqueRepository::class)
 */
class CreneauGenerique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frequence;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jour;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureDebut;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureFin;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\OneToMany(targetEntity=Poste::class, mappedBy="creneauGenerique", cascade={"persist"})
     */
    private $postes;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\OneToMany(targetEntity=Creneau::class, mappedBy="creneauGenerique")
     */
    private $creneaux;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity=Reserve::class, mappedBy="creneauGeneriques")
     */
    private $reserves;

    public function __toString()
    {
        return $this->getFrequence();
    }


    public function __construct()
    {
        $this->postes = new ArrayCollection();
        $this->creneaux = new ArrayCollection();
        $this->reserves = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrequence(): ?string
    {
        return $this->frequence;
    }

    public function setFrequence(?string $frequence): self
    {
        $this->frequence = $frequence;

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

    /**
     * @return Collection|Poste[]
     */
    public function getPostes(): Collection
    {
        return $this->postes;
    }

    public function addPoste(Poste $poste): self
    {
        if (!$this->postes->contains($poste)) {
            $this->postes[] = $poste;
            $poste->setCreneauGenerique($this);
        }

        return $this;
    }

    public function removePoste(Poste $poste): self
    {
        if ($this->postes->removeElement($poste)) {
            // set the owning side to null (unless already changed)
            if ($poste->getCreneauGenerique() === $this) {
                $poste->setCreneauGenerique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Creneau[]
     */
    public function getCreneaux(): Collection
    {
        return $this->creneaux;
    }

    public function addCreneaux(Creneau $creneaux): self
    {
        if (!$this->creneaux->contains($creneaux)) {
            $this->creneaux[] = $creneaux;
            $creneaux->setCreneauGenerique($this);
        }

        return $this;
    }

    public function removeCreneaux(Creneau $creneaux): self
    {
        if ($this->creneaux->removeElement($creneaux)) {
            // set the owning side to null (unless already changed)
            if ($creneaux->getCreneauGenerique() === $this) {
                $creneaux->setCreneauGenerique(null);
            }
        }

        return $this;
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

    public function getJour(): ?int
    {
        return $this->jour;
    }

    public function setJour(?int $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * @return Collection|Reserve[]
     */
    public function getReserves(): Collection
    {
        return $this->reserves;
    }

    public function addReserf(Reserve $reserf): self
    {
        if (!$this->reserves->contains($reserf)) {
            $this->reserves[] = $reserf;
            $reserf->addCreneauGenerique($this);
        }

        return $this;
    }

    public function removeReserf(Reserve $reserf): self
    {
        if ($this->reserves->removeElement($reserf)) {
            $reserf->removeCreneauGenerique($this);
        }

        return $this;
    }

}
