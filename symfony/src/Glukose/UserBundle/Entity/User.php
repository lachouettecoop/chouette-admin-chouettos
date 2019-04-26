<?php

namespace Glukose\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Attribute;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\DnPregMatch;

/**
 * @ORM\Entity(repositoryClass="Glukose\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @ORM\Column(name="dateAdhesion", type="string", length=255, nullable=true)
     */
    private $dateAdhesion;

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
     * @ORM\ManyToMany(targetEntity="Glukose\ContactBundle\Entity\Adresse", cascade={"persist"}, orphanRemoval=true)
     */
    private $adresses;

    /**
     * @ORM\OneToMany(targetEntity="Glukose\UserBundle\Entity\Adhesion", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $adhesions;

    /**
     * @var bool
     *
     * @ORM\Column(name="carteImprimee", type="boolean", nullable=true, options={"default" : 0})
     */
    private $carteImprimee = false;


    /**
     * @DnPregMatch("/ou=([a-zA-Z0-9\.]+)/")
     */
    private $entities = array("accounts");

    public function __construct()
    {
        parent::__construct();
        $this->adhesions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adresses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->prenom . ' ' . $this->nom;
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

    public function exportAdresse()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getDestinataire();
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

    public function exportAdresse3()
    {
        $output = '';
        foreach ($this->adresses as $adresse) {
            $output .= $adresse->getLigne3();
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

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set civilite
     *
     * @param string $civilite
     * @return User
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Add adresses
     *
     * @param \Glukose\ContactBundle\Entity\Adresse $adresses
     * @return User
     */
    public function addAdress(\Glukose\ContactBundle\Entity\Adresse $adresses)
    {
        $this->adresses[] = $adresses;

        return $this;
    }

    /**
     * Remove adresses
     *
     * @param \Glukose\ContactBundle\Entity\Adresse $adresses
     */
    public function removeAdress(\Glukose\ContactBundle\Entity\Adresse $adresses)
    {
        $this->adresses->removeElement($adresses);
    }

    /**
     * Get adresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdresses()
    {
        return $this->adresses;
    }

    /**
     * Get motDePasse
     *
     * @return string
     */
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    /**
     * Set motDePasse
     *
     * @param string $motDePasse
     *
     * @return User
     */
    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    /**
     * Get dateAdhesion
     *
     * @return string
     */
    public function getDateAdhesion()
    {
        return $this->dateAdhesion;
    }

    /**
     * Set dateAdhesion
     *
     * @param string $dateAdhesion
     *
     * @return User
     */
    public function setDateAdhesion($dateAdhesion)
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return User
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Add adhesion
     *
     * @param \Glukose\UserBundle\Entity\Adhesion $adhesion
     *
     * @return User
     */
    public function addAdhesion(\Glukose\UserBundle\Entity\Adhesion $adhesion)
    {
        $this->adhesions[] = $adhesion;

        return $this;
    }

    /**
     * Remove adhesion
     *
     * @param \Glukose\UserBundle\Entity\Adhesion $adhesion
     */
    public function removeAdhesion(\Glukose\UserBundle\Entity\Adhesion $adhesion)
    {
        $this->adhesions->removeElement($adhesion);
    }

    /**
     * Get adhesions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdhesions()
    {
        return $this->adhesions;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return User
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get domaineCompetence
     *
     * @return string
     */
    public function getDomaineCompetence()
    {
        return $this->domaineCompetence;
    }

    /**
     * Set domaineCompetence
     *
     * @param string $domaineCompetence
     *
     * @return User
     */
    public function setDomaineCompetence($domaineCompetence)
    {
        $this->domaineCompetence = $domaineCompetence;

        return $this;
    }

    /**
     * Get codeBarre
     *
     * @return string
     */
    public function getCodeBarre()
    {
        return $this->codeBarre;
    }

    /**
     * Set codeBarre
     *
     * @param string $codeBarre
     *
     * @return User
     */
    public function setCodeBarre($codeBarre)
    {
        $this->codeBarre = $codeBarre;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get carteImprimee
     *
     * @return boolean
     */
    public function getCarteImprimee()
    {
        return $this->carteImprimee;
    }

    /**
     * Set carteImprimee
     *
     * @param boolean $carteImprimee
     *
     * @return User
     */
    public function setCarteImprimee($carteImprimee)
    {
        $this->carteImprimee = $carteImprimee;

        return $this;
    }
}
