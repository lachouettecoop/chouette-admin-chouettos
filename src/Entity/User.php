<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"prenom": "exact", "nom": "exact", "codeBarre": "iexact", "email": "iexact", "rolesChouette.role_unique_id": "iexact"})
 */
class User implements UserInterface
{
    const SERVER_PATH_TO_IMAGE_FOLDER = __DIR__.'/../../public/uploads/documents/';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="new_roles", type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"read:creneauGenerique"})
     * @var boolean
     * @ORM\Column(name="enabled", type="boolean" )
     */
    protected $enabled;

    /**
     * The salt to use for hashing
     * @ORM\Column(name="salt", type="string", nullable=true)
     * @var string
     */
    protected $salt;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     * @ORM\Column(name="confirmationToken", type="string", nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=20, nullable=true)
     */
    private $civilite;

    /**
     * @var string
     * @Attribute("nom")
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     * @Attribute("firstname")
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @Groups({"read:creneauGenerique"})
     * @var string
     *
     * @ORM\Column(name="codeBarre", type="string", length=255, nullable=true)
     */
    private $codeBarre;

    /**
     * @var string
     *
     * @ORM\Column(name="domaineCompetence", type="string", length=255, nullable=true)
     */
    private $domaineCompetence;

    /**
     * @var string
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var date
     *
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $dateNaissance;

    /**
     * @var text
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @var string
     *
     * @ORM\Column(name="motDePasse", type="string", length=255, nullable=true)
     */
    private $motDePasse;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Adresse", cascade={"persist"}, orphanRemoval=true)
     */
    private $adresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Adhesion", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $adhesions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paiement", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $paiements;

    /**
     * @var bool
     *
     * @ORM\Column(name="carteImprimee", type="boolean", nullable=true, options={"default" : 0})
     */
    private $carteImprimee = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="gh", type="boolean", nullable=true, options={"default" : 0})
     */
    private $gh = false;


    /**
     * DnPregMatch("/ou=([a-zA-Z0-9\.]+)/")
     */
    private $entities = array("accounts");



    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity=PersonneRattachee::class, mappedBy="user",cascade={"persist"})
     */
    private $personneRattachee;


    /**
     * @ORM\OneToOne(targetEntity=Poste::class, mappedBy="reservationChouettos", cascade={"persist", "remove"})
     */
    private $poste;

    /**
     * @ORM\OneToMany(targetEntity=Piaf::class, mappedBy="piaffeur")
     */
    private $piafs;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="users")
     */
    private $rolesChouette;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apiToken;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut = 'tres chouette';

    /**
     * @ORM\OneToMany(targetEntity=Statut::class, mappedBy="user")
     */
    private $statuts;

    /**
     * @ORM\OneToOne(targetEntity=Reserve::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $reserve;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPiafEffectuees;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPiafAttendues;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDebutPiaf;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $absenceLongueDureeSansCourses;

    /**
     * @Groups({"read:creneauGenerique"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $absenceLongueDureeCourses;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->adhesions = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->personneRattachee = new ArrayCollection();
        $this->piafs = new ArrayCollection();
        $this->rolesChouette = new ArrayCollection();
        $this->statuts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->nom.' '.$this->prenom;
    }



    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        $filename =uniqid().'-'.$this->getFile()->getClientOriginalName();
        // move takes the target directory and target filename as params
        $this->getFile()->move(
            User::SERVER_PATH_TO_IMAGE_FOLDER,
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->photo = $filename;

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload() {
        $this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated() {
        $this->setUpdated(new \DateTime("now"));
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCodeBarre(): ?string
    {
        return $this->codeBarre;
    }

    public function setCodeBarre(?string $codeBarre): self
    {
        $this->codeBarre = $codeBarre;

        return $this;
    }

    public function getDomaineCompetence(): ?string
    {
        return $this->domaineCompetence;
    }

    public function setDomaineCompetence(?string $domaineCompetence): self
    {
        $this->domaineCompetence = $domaineCompetence;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(?string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getCarteImprimee(): ?bool
    {
        return $this->carteImprimee;
    }

    public function setCarteImprimee(?bool $carteImprimee): self
    {
        $this->carteImprimee = $carteImprimee;

        return $this;
    }

    public function getGh(): ?bool
    {
        return $this->gh;
    }

    public function setGh(?bool $gh): self
    {
        $this->gh = $gh;

        return $this;
    }

    /**
     * @return Collection|Adresse[]
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses[] = $adress;
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): self
    {
        $this->adresses->removeElement($adress);

        return $this;
    }

    /**
     * @return Collection|Adhesion[]
     */
    public function getAdhesions(): Collection
    {
        return $this->adhesions;
    }

    public function addAdhesion(Adhesion $adhesion): self
    {
        if (!$this->adhesions->contains($adhesion)) {
            $this->adhesions[] = $adhesion;
            $adhesion->setUser($this);
        }

        return $this;
    }

    public function removeAdhesion(Adhesion $adhesion): self
    {
        if ($this->adhesions->removeElement($adhesion)) {
            // set the owning side to null (unless already changed)
            if ($adhesion->getUser() === $this) {
                $adhesion->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setUser($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getUser() === $this) {
                $paiement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PersonneRattachee[]
     */
    public function getPersonneRattachee(): Collection
    {
        return $this->personneRattachee;
    }

    public function addPersonneRattachee(PersonneRattachee $personneRattachee): self
    {
        if (!$this->personneRattachee->contains($personneRattachee)) {
            $this->personneRattachee[] = $personneRattachee;
            $personneRattachee->setUser($this);
        }

        return $this;
    }

    public function removePersonneRattachee(PersonneRattachee $personneRattachee): self
    {
        if ($this->personneRattachee->removeElement($personneRattachee)) {
            // set the owning side to null (unless already changed)
            if ($personneRattachee->getUser() === $this) {
                $personneRattachee->setUser(null);
            }
        }

        return $this;
    }

    public function exportDateNaissance()
    {
        $output = ' ';
        if ($this->dateNaissance != '') {
            $output = $this->dateNaissance->format('d/m/Y');
        }
        return $output;
    }

    public function exportdAhesionAnnee()
    {
        $output = '';
        foreach ($this->adhesions as $adhesion) {
            $output .= $adhesion->getAnnee() . ', ';
        }

        return $output;
    }

    public function exportAdhesionDate()
    {
        $output = '';
        foreach ($this->adhesions as $adhesion) {
            if ($adhesion->getDateAdhesion() != null && $adhesion->getDateAdhesion() != '') {
                $output .= $adhesion->getDateAdhesion()->format('d/m/Y') . ', ';
            }
        }

        return $output;
    }

    public function exportAdhesionMontant()
    {
        $output = '';
        foreach ($this->adhesions as $adhesion) {
            $output .= $adhesion->getMontant() . ', ';
        }

        return $output;
    }

    public function exportSouscriptionDate()
    {
        $output = '';

        $echeances = array_map(function(Paiement $paiement) {
            return $paiement->getDateEcheance();
        }, $this->getPaiements()->toArray());
        if (!empty($echeances)) {
            $output = min($echeances)->format('d/m/Y');
        }

        return $output;
    }

    public function exportAdresse1()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getLigne1();
        }

        return $output;
    }

    public function exportAdresse2()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getLigne2();
        }

        return $output;
    }

    public function exportAdresse4()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getCodePostal();
        }

        return $output;
    }

    public function exportAdresse5()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getVille();
        }

        return $output;
    }

    public function exportAdresse6()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getPays();
        }

        return $output;
    }


    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): self
    {
        $this->poste = $poste;

        // set (or unset) the owning side of the relation if necessary
        $newReservationChouettos = null === $poste ? null : $this;
        if ($poste->getReservationChouettos() !== $newReservationChouettos) {
            $poste->setReservationChouettos($newReservationChouettos);
        }

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
            $piaf->setPiaffeur($this);
        }

        return $this;
    }

    public function removePiaf(Piaf $piaf): self
    {
        if ($this->piafs->removeElement($piaf)) {
            // set the owning side to null (unless already changed)
            if ($piaf->getPiaffeur() === $this) {
                $piaf->setPiaffeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRolesChouette(): Collection
    {
        return $this->rolesChouette;
    }

    public function addRolesChouette(Role $rolesChouette): self
    {
        if (!$this->rolesChouette->contains($rolesChouette)) {
            $this->rolesChouette[] = $rolesChouette;
        }

        return $this;
    }

    public function removeRolesChouette(Role $rolesChouette): self
    {
        $this->rolesChouette->removeElement($rolesChouette);

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return Collection|Statut[]
     */
    public function getStatuts(): Collection
    {
        return $this->statuts;
    }

    public function addStatut(Statut $statut): self
    {
        if (!$this->statuts->contains($statut)) {
            $this->statuts[] = $statut;
            $statut->setUser($this);
        }

        return $this;
    }

    public function removeStatut(Statut $statut): self
    {
        if ($this->statuts->removeElement($statut)) {
            // set the owning side to null (unless already changed)
            if ($statut->getUser() === $this) {
                $statut->setUser(null);
            }
        }

        return $this;
    }

    public function getReserve(): ?Reserve
    {
        return $this->reserve;
    }

    public function setReserve(?Reserve $reserve): self
    {
        $this->reserve = $reserve;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $reserve ? null : $this;
        if ($reserve->getUser() !== $newUser) {
            $reserve->setUser($newUser);
        }

        return $this;
    }

    public function getNbPiafEffectuees(): ?int
    {
        return $this->nbPiafEffectuees;
    }

    public function setNbPiafEffectuees(?int $nbPiafEffectuees): self
    {
        $this->nbPiafEffectuees = $nbPiafEffectuees;

        return $this;
    }

    public function getNbPiafAttendues(): ?int
    {
        return $this->nbPiafAttendues;
    }

    public function setNbPiafAttendues(?int $nbPiafAttendues): self
    {
        $this->nbPiafAttendues = $nbPiafAttendues;

        return $this;
    }

    public function getDateDebutPiaf(): ?\DateTimeInterface
    {
        return $this->dateDebutPiaf;
    }

    public function setDateDebutPiaf(?\DateTimeInterface $dateDebutPiaf): self
    {
        $this->dateDebutPiaf = $dateDebutPiaf;

        return $this;
    }

    public function getAbsenceLongueDureeSansCourses(): ?bool
    {
        return $this->absenceLongueDureeSansCourses;
    }

    public function setAbsenceLongueDureeSansCourses(?bool $absenceLongueDureeSansCourses): self
    {
        $this->absenceLongueDureeSansCourses = $absenceLongueDureeSansCourses;

        return $this;
    }

    public function getAbsenceLongueDureeCourses(): ?bool
    {
        return $this->absenceLongueDureeCourses;
    }

    public function setAbsenceLongueDureeCourses(?bool $absenceLongueDureeCourses): self
    {
        $this->absenceLongueDureeCourses = $absenceLongueDureeCourses;

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
}
