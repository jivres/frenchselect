<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Univers
 *
 * @ORM\Table(name="univers", indexes={@ORM\Index(name="label", columns={"label"})})
 * @ORM\Entity
 */
class Univers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=30, nullable=false)
     */
    private $label;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Brand", mappedBy="univers")
     */
    private $brand;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\StyleUnivers", cascade={"remove","persist"})
     */
    private $styles;

    /**
     * @return mixed
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * @param mixed $styles
     */
    public function setStyles($styles)
    {
        $this->styles = $styles;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->brand = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->label;
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return Univers
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Add brand.
     *
     * @param \B2bBundle\Entity\Brand $brand
     *
     * @return Univers
     */
    public function addBrand(\B2bBundle\Entity\Brand $brand)
    {
        $this->brand[] = $brand;

        return $this;
    }

    /**
     * Remove brand.
     *
     * @param \B2bBundle\Entity\Brand $brand
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBrand(\B2bBundle\Entity\Brand $brand)
    {
        return $this->brand->removeElement($brand);
    }

    /**
     * Get brand.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrand()
    {
        return $this->brand;

    }






}
