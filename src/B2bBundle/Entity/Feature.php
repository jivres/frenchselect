<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feature
 *
 * @ORM\Table(name="feature")
 * @ORM\Entity
 */
class Feature
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
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Brand", mappedBy="feature")
     */
    private $brand;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->brand = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Feature
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
     * @return Feature
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

    public function __toString() {
        return $this->label;
    }
}
