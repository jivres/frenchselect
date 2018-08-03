<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\Size;
use B2bBundle\Entity\SizeQuantity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CartRow
 *
 * @ORM\Table(name="cart_row")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CartRowRepository")
 */
class CartRow implements \JsonSerializable {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CartCategory", inversedBy="cartRows")
     * @ORM\JoinColumn(name="cart_category_id", referencedColumnName="id")
     */
    private $cartCategory;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    //private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float")
     */
    private $priceHT;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\ColorProduct")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\SizeQuantity", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sizeQuantities;


    private $allowedColor;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set cart category
     *
     * @param CartCategory $cartCategory
     *
     * @return CartRow
     */
    public function setCartCategory($cartCategory) {
        $this->cartCategory = $cartCategory;

        return $this;
    }

    /**
     * Get cart category
     *
     * @return CartCategory
     */
    public function getCartCategory() {
        return $this->cartCategory;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartRow
     */
    /*public function setQuantity($quantity) {
        $this->quantity = $quantity;

        return $this;
    }*/

    /**
     * Get quantity
     *
     * @param Size $size
     * @return void
     */
    /*public function getQuantity() {
        return $this->quantity;
    }*/

    public function addQuantity(Size $size) {
        $sizeQuantity = new SizeQuantity();
        $sizeQuantity->setSize($size);
        $this->addSizeQuantity($sizeQuantity);
    }

    /**
     * Add size quantity
     *
     * @param SizeQuantity $quantity
     *
     * @return CartRow
     */
    public function addSizeQuantity(SizeQuantity $quantity) {
        $this->sizeQuantities[] = $quantity;

        return $this;
    }

    /**
     * Remove size quantity
     *
     * @param SizeQuantity $quantity
     */
    public function removeSizeQuantity(SizeQuantity $quantity) {
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
     * Set priceHT
     *
     * @param float $priceHT
     *
     * @return CartRow
     */
    public function setPriceHT($priceHT) {
        $this->priceHT = $priceHT;

        return $this;
    }

    /**
     * Get priceHT
     *
     * @return float
     */
    public function getPriceHT() {
        return $this->priceHT;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @param $availability
     *
     * @return CartRow
     */
    public function setProduct($product, $availability) {
        if ($product != null) {
            $this->product = $product;
            $this->priceHT = $product->getPriceHT();
            foreach ($availability->getSizeQuantities() as $sizeQuantity) {
                $this->addQuantity($sizeQuantity->getSize());
            }
            $this->color = $availability->getColor();
        }
        //$this->quantity = 0;

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
     * @return CartRow
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

    public function getTotal() {
        $total = 0;
        foreach ($this->sizeQuantities as $sizeQuantity) {
            $total += $sizeQuantity->getAmount();
        }
        return $total * $this->priceHT;
    }

    public function __construct() {
        $this->sizeQuantities = new ArrayCollection();
        $this->allowedColor = null;
        //$this->quantity = 1;

    }

    public function __toString() {
        return $this->product;
    }

    public function getAllowedColor(){
        if ($this->allowedColor == null) {
            foreach ($this->getProduct()->getAllowedColors() as $allowedColor) {
                if ($allowedColor->getColor() == $this->getColor()) {
                    $this->allowedColor = $allowedColor;
                    break;
                }
            }
        }
        return $this->allowedColor;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        return array(
            'product' => array(
                'id' => $this->product->getId(),
                'name' => $this->product->getName(),
            ),
            //'quantity' => $this->quantity,
            'priceHT' => $this->priceHT,
        );
    }
}
