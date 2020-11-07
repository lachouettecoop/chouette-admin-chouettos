<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * Personne
 *
 * @ORM\Table(name="personne")
 * @ORM\Entity
 * @ApiResource()
 */
class Personne
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
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePremiereReunion", type="date", nullable=true)
     */
    private $datePremiereReunion;


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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Personne
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Personne
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
     * Set email
     *
     * @param string $email
     *
     * @return Personne
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set datePremiereReunion
     *
     * @param \DateTime $datePremiereReunion
     *
     * @return Personne
     */
    public function setDatePremiereReunion($datePremiereReunion)
    {
        $this->datePremiereReunion = $datePremiereReunion;

        return $this;
    }

    /**
     * Get datePremiereReunion
     *
     * @return \DateTime
     */
    public function getDatePremiereReunion()
    {
        return $this->datePremiereReunion;
    }

    public function getNomAffichage()
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }

    public function exportDatePremiereReunion()
    {
        $output = '';
        if ($this->getDatePremiereReunion() != '') {
            $output = $this->getDatePremiereReunion()->format('d/m/Y');
        }
        return $output;
    }
}

