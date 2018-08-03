<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Availability
 *
 * @ORM\Table(name="availability")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\AvailabilityRepository")
 */
class Availability {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\SizeQuantity", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sizeQuantities;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Product", cascade={"persist"}, inversedBy="availabilities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\ColorProduct")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Add new quantity
     *
     * @param \B2bBundle\Entity\Size $size
     *
     * @return Availability
     */
    public function addQuantity(\B2bBundle\Entity\Size $size) {
        $sizeQuantity = new SizeQuantity();
        $sizeQuantity->setSize($size);
        return $this->addSizeQuantity($sizeQuantity);
    }

    /**
     * Remove size quantity
     *
     * @param \B2bBundle\Entity\Size $size
     */
    public function removeQuantity(\B2bBundle\Entity\Size $size) {
        foreach ($this->sizeQuantities as $sizeQuantity) {
            if ($sizeQuantity->getSize() == $size) {
                $this->removeSizeQuantity($sizeQuantity);
                break;
            }
        }
    }

    /**
     * Add size quantity
     *
     * @param \B2bBundle\Entity\SizeQuantity $quantity
     *
     * @return Availability
     */
    public function addSizeQuantity(\B2bBundle\Entity\SizeQuantity $quantity) {
        $this->sizeQuantities[] = $quantity;

        return $this;
    }

    /**
     * Remove size quantity
     *
     * @param \B2bBundle\Entity\SizeQuantity $quantity
     */
    public function removeSizeQuantity(\B2bBundle\Entity\SizeQuantity $quantity) {
        $this->sizeQuantities->removeElement($quantity);
    }

    /**
     * Get size quantities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizeQuantities() {
        return $this->sizeQuantities;
    }

    public function hasQuantity() {
        foreach ($this->sizeQuantities as $qt) {
            if ($qt->getAmount() > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Availability
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return Availability
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
     * Set color
     *
     * @param ColorProduct $color
     *
     * @return Availability
     */
    public function setColor($color) {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return ColorProduct
     */
    public function getColor() {
        return $this->color;
    }

    public function __construct(Product $product, ColorProduct $color, \Doctrine\Common\Collections\Collection $allowedSizes, $status) {
        $this->sizeQuantities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setProduct($product);
        $this->setColor($color);
        $this->setStatus($status);
        foreach ($allowedSizes as $allowedSize) {
            $this->addQuantity($allowedSize->getSize());
        }
    }
}
