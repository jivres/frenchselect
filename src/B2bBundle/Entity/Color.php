<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Color
 *
 * @ORM\Table(name="color")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\ColorRepository")
 */
class Color
{
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
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     * @ORM\Column(name="hexTriplet", type="string", length=6, nullable=true)
     */
    private $hexTriplet;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Color
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


    public function __toString() {
        return $this->label;
    }

    /**
     * Set hexTriplet
     *
     * @param string $hexTriplet
     *
     * @return Color
     */
    public function setHexTriplet($hexTriplet)
    {
        $this->hexTriplet = substr($hexTriplet, -6); // pour enlever le '#' devant

        return $this;
    }

    /**
     * Get hexTriplet
     *
     * @return string
     */
    public function getHexTriplet()
    {
        return $this->hexTriplet;
    }
}
