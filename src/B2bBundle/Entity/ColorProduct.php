<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ColorProduct
 *
 * @ORM\Table(name="color_product")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\ColorRepository")
 */
class ColorProduct
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
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $picture;


    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\AllowedColor", cascade={"persist", "remove"}, mappedBy="color")
     *
     */
    private $allowedColors;


    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Color", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $color;

    /**
     * Add allowed color
     *
     * @param AllowedColor $allowedColor
     *
     * @return ColorProduct
     */
    public function addAllowedColor(AllowedColor $allowedColor) {
        $allowedColor->setProduct($this);
        $this->allowedColors[] = $allowedColor;

        $this->addAvailabilitiesForColor($allowedColor);

        return $this;
    }


    /**
     * Remove allowed color
     *
     * @param AllowedColor $allowedColor
     */
    public function removeAllowedColor(AllowedColor $allowedColor) {
        $this->allowedColors->removeElement($allowedColor);
        foreach ($this->availabilities as $availability) {
            if ($availability->getColor() == $allowedColor) {
                $this->availabilities->remove($availability);
                break;
            }
        }
    }
    /**
     * Get allowed colors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllowedColors() {
        return $this->allowedColors;
    }

    /**
     * Set color
     *
     * @param Color $color
     * @return ColorProduct
     */
    public function setColor($color) {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return Color
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * Set Picture
     *
     * @param UploadedFile $picture
     *
     * @return ColorProduct
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }



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
     * @return ColorProduct
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

    public function __construct() {
        $this->allowedColors = new ArrayCollection();
    }
}
