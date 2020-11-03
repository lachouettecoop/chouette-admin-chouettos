<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    //todo: change directory
    const SERVER_PATH_TO_IMAGE_FOLDER = '/var/www/symfony/web/uploads/';

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
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     * @Attribute("firstname")
     *
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
     *
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
     * @ORM\Column(name="updated", type="datetime")
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

    public function getId(): ?int
    {
        return $this->id;
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
}
