<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adhesion
 *
 * @ORM\Table(name="paiement")
 * @ORM\Entity
 */
class Paiement
{
    const MONTANT_NOMINAL = 100;

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
     * @ORM\Column(name="$dateEcheance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="integer", length=255, nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="modePaiement", type="string", length=255, nullable=true)
     */
    private $modePaiement;

    /**
     * @var boolean
     *
     * @ORM\Column(name="effectif", type="boolean", nullable=true)
     */
    private $effectif = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="adhesions")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Paiement
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return date
     */
    public function getDateEcheance()
    {
        return $this->dateEcheance;
    }

    /**
     * @param date $dateEcheance
     * @return Paiement
     */
    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;
        return $this;
    }

    /**
     * @return string
     */
    public function getMontant()
    {
        if (is_null($this->montant)) {
            return static::MONTANT_NOMINAL;
        }
        return $this->montant;
    }

    /**
     * @param string $montant
     * @return Paiement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
        return $this;
    }

    /**
     * @return string
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * @param string $modePaiement
     * @return Paiement
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Paiement
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEffectif()
    {
        return $this->effectif;
    }

    /**
     * @param bool $effectif
     */
    public function setEffectif($effectif)
    {
        $this->effectif = $effectif;
        return $this;
    }
}
