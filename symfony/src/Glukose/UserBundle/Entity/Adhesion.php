<?php

namespace Glukose\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adhesion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Adhesion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var date
     *
     * @ORM\Column(name="dateAdhesion", type="date", nullable=true)
     */
    private $dateAdhesion;

    /**
     * @var integer
     *
     * @ORM\Column(name="annee", type="integer", nullable=true)
     */
    private $annee;

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
    * @ORM\ManyToOne(targetEntity="Glukose\UserBundle\Entity\User", inversedBy="adhesions")
    */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;

    }

    public function __toString()
    {
        return $this->annee;

    }


    /**
     * Set dateAdhesion
     *
     * @param \DateTime $dateAdhesion
     *
     * @return Adhesion
     */
    public function setDateAdhesion($dateAdhesion)
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }

    /**
     * Get dateAdhesion
     *
     * @return \DateTime
     */
    public function getDateAdhesion()
    {
        return $this->dateAdhesion;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Adhesion
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
     * @return Adhesion
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
     * Set annee
     *
     * @param integer $annee
     *
     * @return Adhesion
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set user
     *
     * @param \Glukose\UserBundle\Entity\User $user
     *
     * @return Adhesion
     */
    public function setUser(\Glukose\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Glukose\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
