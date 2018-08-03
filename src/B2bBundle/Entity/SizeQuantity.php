<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quantity
 *
 * @ORM\Table(name="size_quantity")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SizeQuantityRepository")
 */
class SizeQuantity {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Size")
     * @ORM\JoinColumn(nullable=false)
     */
    private $size;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", options={"default" : 0}, nullable=true)
     */
    private $amount=0;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set size
     *
     * @param Size $size
     *
     * @return SizeQuantity
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

    /**
     * Set amount
     *
     * @param int $amount
     *
     * @return SizeQuantity
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount() {
        return $this->amount;
    }

    public function __construct() {
    }

    public function __toString() {
        return "Label";
    }
}
