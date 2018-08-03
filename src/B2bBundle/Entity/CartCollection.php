<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * CartCollection
 *
 * @ORM\Table(name="cart_collection")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CartCollectionRepository")
 */
class CartCollection implements \JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Brand", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", cascade={"persist"}, inversedBy="carts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $customer;

    /**
     * @var int
     *
     * @ORM\Column(name="nbProducts", type="integer")
     */
    private $nbProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="cartCollections")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", nullable=true)
     */
    private $cart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deliveryDate", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Collection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $collection;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\CartCategory", cascade={"persist", "remove"}, mappedBy="cartCollection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cartCategories;

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float", nullable=true)
     */
    private $priceCollection;

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
     * Set brand
     *
     * @param \B2bBundle\Entity\Brand $brand
     *
     * @return CartCollection
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \B2bBundle\Entity\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set customer
     *
     * @param \B2bBundle\Entity\Customer $customer
     *
     * @return CartCollection
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \B2bBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Add products amount to nbProducts
     *
     * @param int $nbProducts
     *
     * @return CartCollection
     */
    public function addProducts($nbProducts)
    {
        $this->nbProducts += $nbProducts;

        return $this;
    }

    /**
     * Remove products amount to nbProducts
     *
     * @param int $nbProducts
     *
     * @return CartCollection
     */
    public function removeProducts($nbProducts)
    {
        $this->nbProducts -= $nbProducts;

        return $this;
    }

    /**
     * Get nbProducts
     *
     * @return int
     */
    public function getNbProducts()
    {
        return $this->nbProducts;
    }

    /**
     * Set cart
     *
     * @param Cart $cart
     *
     * @return CartCollection
     */
    public function setCart($cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set delivery date
     *
     * @param \DateTime $deliveryDate
     *
     * @return CartCollection
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get delivery date
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * Set collection
     *
     * @param Collection $collection
     *
     * @return CartCollection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Add cart row
     *
     * @param \B2bBundle\Entity\CartCategory $cartCategory
     *
     * @return CartCollection
     */
    public function addCartCategory(\B2bBundle\Entity\CartCategory $cartCategory)
    {
        $cartCategory->setCartCollection($this);
        $this->cartCategories[] = $cartCategory;
        return $this;
    }

    /**
     * Remove cart row
     *
     * @param \B2bBundle\Entity\CartCategory $cartCategory
     */
    public function removeCartCategory(\B2bBundle\Entity\CartCategory $cartCategory)
    {
        $this->cartCategories->removeElement($cartCategory);
    }

    /**
     * Get cart rows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartCategories()
    {
        return $this->cartCategories;
    }

    public function update(ManagerRegistry $em)
    {
        $count = 0;
        foreach ($this->cartCategories as $cartCategory) {
            if ($cartCategory->update($em) == 0) {
                $this->removeCartCategory($cartCategory);
                $em->getManager()->remove($cartCategory);
            } else {
                $count += 1;
            }
        }
        return $count;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->cartCategories as $cartCategory) {
            $total += $cartCategory->getTotal();
        }
        return $total;
    }

    public function __construct()
    {
        $this->cartCategories = new ArrayCollection();
        $this->nbProducts = 0;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array(
            'name' => $this->collection,
            'nbProducts' => $this->nbProducts,
            'categories' => $this->cartCategories,
        );
    }

    /**
     * Set nbProducts.
     *
     * @param int $nbProducts
     *
     * @return CartCollection
     */
    public function setNbProducts($nbProducts)
    {
        $this->nbProducts = $nbProducts;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceCollection()
    {
        return $this->priceCollection;
    }

    /**
     * @param float $priceCollection
     */
    public function setPriceCollection($priceCollection)
    {
        $this->priceCollection = $priceCollection;
    }

    public function getProductsNb(){
        $cpt =0;
        foreach($this->getCartCategories() as $cartCategory){
            foreach($cartCategory->getCartRows() as $cartRow){
                if($cartRow->hasQuantity()){
                    $cpt++;
                }
            }
        }
        return $cpt;
    }


}
