<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SecondaryCategory
 *
 * @ORM\Table(name="secondary_category")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SecondaryCategoryRepository")
 */
class SecondaryCategory {

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
     * @ORM\OneToMany(targetEntity="Product", mappedBy="secondaryCat")
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
    public function getId() {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return SecondaryCategory
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    public function __toString() {
        return $this->getLabel();
    }
}
