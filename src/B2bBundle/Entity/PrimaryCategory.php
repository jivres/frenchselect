<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrimaryCategory
 *
 * @ORM\Table(name="primary_category")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\PrimaryCategoryRepository")
 */
class PrimaryCategory
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Brand", mappedBy="categories")
     */
    private $brand;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="primaryCat")
     */
    private $products;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
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
     * @return PrimaryCategory
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
     * Get brand
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrand()
    {
        return $this->brand;
    }
}
