<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AllowedSize
 *
 * @ORM\Table(name="allowed_size")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\AllowedSizeRepository")
 */
class AllowedSize {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Product", cascade={"persist"}, inversedBy="allowedSizes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

     /**
      * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Size", cascade={"persist"})
      * @ORM\JoinColumn(nullable=false)
      */
    private $size;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return AllowedSize
     */
    public function setProduct($product) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Set size
     *
     * @param Size $size
     *
     * @return AllowedSize
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return Size
     */
    public function getSize() {
        return $this->size;
    }

    public function __toString()
    {
        return $this->size;
    }
}
