<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\DepartementRepository")
 */
class Departement {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=255)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Departement
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set zip code
     *
     * @param string $zipCode
     *
     * @return Departement
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zip code
     *
     * @return string
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    public function __toString() {
        return $this->zipCode.' - '.$this->name;
    }
}
