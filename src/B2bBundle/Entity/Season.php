<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Season
 *
 * @ORM\Table(name="season")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SeasonRepository")
 */
class Season {
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank;

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
     * @return Season
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
     * Set rank
     *
     * @param int $rank
     *
     * @return Season
     */
    public function setRank($rank) {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank() {
        return $this->rank;
    }

    public function __toString() {
        return $this->getName();
    }

}
