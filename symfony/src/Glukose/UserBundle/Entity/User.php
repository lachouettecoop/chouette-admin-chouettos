<?php

namespace Glukose\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Attribute;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\ObjectClass;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Dn;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\Sequence;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\ArrayField;
use Gorg\Bundle\LdapOrmBundle\Annotation\Ldap\DnPregMatch;

/**
 * @ORM\Entity
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
     * @var integer
     *
     * @ORM\Column(name="destination", type="smallint", nullable=true)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="portable", type="string", length=255, nullable=true)
     */
    private $portable;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="statusAssociatif", type="string", length=255, nullable=true)
     */
    private $statusAssociatif;

    /**
     * @var string
     *
     * @ORM\Column(name="dateAdhesion", type="string", length=255, nullable=true)
     */
    private $dateAdhesion;

    /**
     * @var string
     *
     * @ORM\Column(name="typeCotisation", type="string", length=255, nullable=true)
     */
    private $typeCotisation;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="string", length=255, nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="modePaiement", type="string", length=255, nullable=true)
     */
    private $modePaiement;

    /**
     * @var string
     *
     * @ORM\Column(name="presentAzendoo", type="string", length=255, nullable=true)
     */
    private $presentAzendoo;

    /**
     * @var string
     *
     * @ORM\Column(name="codeBarre", type="string", length=255, nullable=true)
     */
    private $codeBarre;

    /**
     * @var string
     *
     * @ORM\Column(name="csp", type="string", length=255, nullable=true)
     */
    private $csp;

    /**
     * @var string
     *
     * @ORM\Column(name="domaineCompetence", type="string", length=255, nullable=true)
     */
    private $domaineCompetence;

    /**
     * @var string
     *
     * @ORM\Column(name="dateAzendoo", type="string", length=255, nullable=true)
     */
    private $dateAzendoo;

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
    * @ORM\ManyToMany(targetEntity="Glukose\UserBundle\Entity\Groupe", mappedBy="membres")
    */
    private $groupes;

    /**
     * @var bool
     *
     * @ORM\Column(name="accepteMail", type="boolean", nullable=true)
     */
    private $accepteMail = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="membreActif", type="boolean", nullable=true)
     */
    private $membreActif = false;


    /**
     * @DnPregMatch("/ou=([a-zA-Z0-9\.]+)/")
     */
    private $entities = array("accounts");



    public function __toString(){
        return $this->prenom.' '.$this->nom;
    }

    public function __construct()
    {
        parent::__construct();
        $this->adhesions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adresses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function exportDateNaissance()
    {
        $output = ' ';
        if ($this->dateNaissance != ''){
            $output = $this->dateNaissance->format('d/m/Y');
        }
        return $output;
    }

    public function exportdAhesionAnnee()
    {
        $output = '';
        foreach($this->adhesions as $adhesion){
            $output.= $adhesion->getAnnee().', ';
        }

        return $output;
    }

    public function exportAdhesionDate()
    {
        $output = '';
        foreach($this->adhesions as $adhesion){
          if($adhesion->getDateAdhesion() != null && $adhesion->getDateAdhesion()!= ''){
            $output.= $adhesion->getDateAdhesion()->format('d/m/Y').', ';
            }
        }

        return $output;
    }
    public function exportAdhesionMontant()
    {
        $output = '';
        foreach($this->adhesions as $adhesion){
            $output.= $adhesion->getMontant().', ';
        }

        return $output;
    }

    public function exportAdresse()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getDestinataire();
        }

        return $output;
    }

    public function exportAdresse1()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getLigne1();
        }

        return $output;
    }

    public function exportAdresse2()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getLigne2();
        }

        return $output;
    }

    public function exportAdresse3()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getLigne3();
        }

        return $output;
    }

    public function exportAdresse4()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getCodePostal();
        }

        return $output;
    }

 public function exportAdresse5()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getVille();
        }

        return $output;
    }

 public function exportAdresse6()
    {
        $output = '';
        foreach($this->adresses as $adresse){
            $output.= $adresse->getPays();
        }

        return $output;
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
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
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
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
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
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set destination
     *
     * @param integer $destination
     * @return User
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination
     *
     * @return integer
     */
    public function getDestination()
    {
        return $this->destination;
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
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set portable
     *
     * @param string $portable
     * @return User
     */
    public function setPortable($portable)
    {
        $this->portable = $portable;

        return $this;
    }

    /**
     * Get portable
     *
     * @return string
     */
    public function getPortable()
    {
        return $this->portable;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return User
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
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
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
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
     * Set uid
     *
     * @param integer $uid
     *
     * @return User
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
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
     * Get motDePasse
     *
     * @return string
     */
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    /**
     * Set statusAssociatif
     *
     * @param string $statusAssociatif
     *
     * @return User
     */
    public function setStatusAssociatif($statusAssociatif)
    {
        $this->statusAssociatif = $statusAssociatif;

        return $this;
    }

    /**
     * Get statusAssociatif
     *
     * @return string
     */
    public function getStatusAssociatif()
    {
        return $this->statusAssociatif;
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
     * Get dateAdhesion
     *
     * @return string
     */
    public function getDateAdhesion()
    {
        return $this->dateAdhesion;
    }

    /**
     * Set typeCotisation
     *
     * @param string $typeCotisation
     *
     * @return User
     */
    public function setTypeCotisation($typeCotisation)
    {
        $this->typeCotisation = $typeCotisation;

        return $this;
    }

    /**
     * Get typeCotisation
     *
     * @return string
     */
    public function getTypeCotisation()
    {
        return $this->typeCotisation;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return User
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set modePaiement
     *
     * @param string $modePaiement
     *
     * @return User
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement
     *
     * @return string
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set presentAzendoo
     *
     * @param string $presentAzendoo
     *
     * @return User
     */
    public function setPresentAzendoo($presentAzendoo)
    {
        $this->presentAzendoo = $presentAzendoo;

        return $this;
    }

    /**
     * Get presentAzendoo
     *
     * @return string
     */
    public function getPresentAzendoo()
    {
        return $this->presentAzendoo;
    }


    /**
     * Set dateAzendoo
     *
     * @param string $dateAzendoo
     *
     * @return User
     */
    public function setDateAzendoo($dateAzendoo)
    {
        $this->dateAzendoo = $dateAzendoo;

        return $this;
    }

    /**
     * Get dateAzendoo
     *
     * @return string
     */
    public function getDateAzendoo()
    {
        return $this->dateAzendoo;
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
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
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
     * Add groupe
     *
     * @param \Glukose\UserBundle\Entity\Groupe $groupe
     *
     * @return User
     */
    public function addGroupe(\Glukose\UserBundle\Entity\Groupe $groupe)
    {
        $this->groupes[] = $groupe;

        return $this;
    }

    /**
     * Remove groupe
     *
     * @param \Glukose\UserBundle\Entity\Groupe $groupe
     */
    public function removeGroupe(\Glukose\UserBundle\Entity\Groupe $groupe)
    {
        $this->groupes->removeElement($groupe);
    }

    /**
     * Get groupes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * Set csp
     *
     * @param string $csp
     *
     * @return User
     */
    public function setCsp($csp)
    {
        $this->csp = $csp;

        return $this;
    }

    /**
     * Get csp
     *
     * @return string
     */
    public function getCsp()
    {
        return $this->csp;
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
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
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
     * Get domaineCompetence
     *
     * @return string
     */
    public function getDomaineCompetence()
    {
        return $this->domaineCompetence;
    }

    /**
     * Set accepteMail
     *
     * @param boolean $accepteMail
     *
     * @return User
     */
    public function setAccepteMail($accepteMail)
    {
        $this->accepteMail = $accepteMail;

        return $this;
    }

    /**
     * Get accepteMail
     *
     * @return boolean
     */
    public function getAccepteMail()
    {
        return $this->accepteMail;
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
     * Get codeBarre
     *
     * @return string
     */
    public function getCodeBarre()
    {
        return $this->codeBarre;
    }

    /**
     * Set membreActif
     *
     * @param boolean $membreActif
     *
     * @return User
     */
    public function setMembreActif($membreActif)
    {
        $this->membreActif = $membreActif;

        return $this;
    }

    /**
     * Get membreActif
     *
     * @return boolean
     */
    public function getMembreActif()
    {
        return $this->membreActif;
    }
}
