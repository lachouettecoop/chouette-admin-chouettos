<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adresse
 *
 * @ORM\Table(name="adresse")
 * @ORM\Entity
 */
class Adresse
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
     * @var string
     *
     * @ORM\Column(name="ligne1", type="string", length=255, nullable=true)
     */
    private $ligne1;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne2", type="string", length=255, nullable=true)
     */
    private $ligne2;

    /**
     * @var string
     *
     * @ORM\Column(name="NPAI", type="boolean", nullable=true, options={"default" = 0})
     */
    private $nPAI;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @var integer
     *
     * @ORM\Column(name="codePostal", type="string", length=255, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get ligne1
     *
     * @return string
     */
    public function getLigne1()
    {
        return $this->ligne1;
    }

    /**
     * Set ligne1
     *
     * @param string $ligne1
     * @return Adresse
     */
    public function setLigne1($ligne1)
    {
        $this->ligne1 = $ligne1;

        return $this;
    }

    /**
     * Get ligne2
     *
     * @return string
     */
    public function getLigne2()
    {
        return $this->ligne2;
    }

    /**
     * Set ligne2
     *
     * @param string $ligne2
     * @return Adresse
     */
    public function setLigne2($ligne2)
    {
        $this->ligne2 = $ligne2;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return integer
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set codePostal
     *
     * @param integer $codePostal
     * @return Adresse
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Adresse
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get nPAI
     *
     * @return boolean
     */
    public function getNPAI()
    {
        return $this->nPAI;
    }

    /**
     * Set nPAI
     *
     * @param boolean $nPAI
     * @return Adresse
     */
    public function setNPAI($nPAI)
    {
        $this->nPAI = $nPAI;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Adresse
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    public function __toString()
    {
        return $this->ligne1;
    }
}
