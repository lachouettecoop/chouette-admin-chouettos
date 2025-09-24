<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read:task"}},
 *     denormalizationContext={"groups"={"write:task"}},
 *     graphql={
 *         "item_query",
 *         "collection_query",
 *         "create",
 *         "update"
 *     }
 * )
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:task", "read:creneauGenerique"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:task", "write:task", "read:creneauGenerique"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:task", "write:task", "read:creneauGenerique"})
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:task", "write:task", "read:creneauGenerique"})
     */
    private $link;

    /**
     * @ORM\ManyToMany(targetEntity=CreneauGenerique::class, inversedBy="tasks")
     * @Groups({"read:task", "write:task"})
     */
    private $creneauGeneriques;

    public function __construct()
    {
        $this->creneauGeneriques = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->title ?? 'Nouvelle tâche';
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return Collection<int, CreneauGenerique>
     */
    public function getCreneauGeneriques(): Collection
    {
        return $this->creneauGeneriques;
    }

    public function addCreneauGenerique(CreneauGenerique $creneau): self
    {
        if (!$this->creneauGeneriques->contains($creneau)) {
            $this->creneauGeneriques[] = $creneau;
            $creneau->addTask($this);
        }
        return $this;
    }

    public function removeCreneauGenerique(CreneauGenerique $creneau): self
    {
        if ($this->creneauGeneriques->removeElement($creneau)) {
            $creneau->removeTask($this);
        }
        return $this;
    }
}
