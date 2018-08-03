<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CartRepository")
 */
class Cart implements JSonSerializable {

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
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", cascade={"persist"}, inversedBy="carts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\CartCollection", cascade={"persist", "remove"}, mappedBy="cart")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cartCollections;

    /**
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\Command")
     * @ORM\Column(name="command_id", nullable=true)
     */

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float", nullable=true)
     */
    private $priceCart;


    //private $command;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set brand
     *
     * @param \B2bBundle\Entity\Brand $brand
     *
     * @return Cart
     */
    public function setBrand($brand) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \B2bBundle\Entity\Brand
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Set customer
     *
     * @param \B2bBundle\Entity\Customer $customer
     *
     * @return Cart
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \B2bBundle\Entity\Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Add cart collection
     *
     * @param \B2bBundle\Entity\CartCollection $cartCollection
     *
     * @return Cart
     */
    public function addCartCollection(\B2bBundle\Entity\CartCollection $cartCollection) {
        $cartCollection->setCart($this);
        $cartCollection->setBrand($this->brand);
        $cartCollection->setCustomer($this->customer);
        $this->cartCollections[] = $cartCollection;
        return $this;
    }

    /**
     * Remove cart collection
     *
     * @param \B2bBundle\Entity\CartCollection $cartCollection
     */
    public function removeCartCollection(\B2bBundle\Entity\CartCollection $cartCollection) {
        $this->cartCollections->removeElement($cartCollection);
        $cartCollection->setCart(Null);
    }

    /**
     * Get cart rows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartCollections() {
        return $this->cartCollections;
    }

    /**
     * @return float
     */
    public function getPriceCart()
    {
        return $this->priceCart;
    }

    /**
     * @param float $priceCart
     */
    public function setPriceCart($priceCart)
    {
        $this->priceCart = $priceCart;
    }


    public function update(ManagerRegistry $em) {
        $count = 0;
        foreach ($this->cartCollections as $cartCollection) {
            if ($cartCollection->update($em) == 0) {
                $this->removeCartCollection($cartCollection);
                $em->getManager()->remove($cartCollection);
            } else {
                $count += 1;
            }
        }
        return $count;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->cartCollections as $cartCollection) {
            $total += $cartCollection->getTotal();
        }
        return $total;
    }

    public function __construct(Brand $brand, Customer $customer) {
        $this->cartCollections = new ArrayCollection();
        $this->brand = $brand;
        $this->customer = $customer;
    }

    public function isEmpty(){
        foreach($this->getCartCollections() as $cartCollection){
            foreach ($cartCollection->getCartCategories() as $cartCategory){
                foreach ($cartCategory->getCartRows() as $cartRow){
                    if($cartRow->hasQuantity()){
                        return false;
                    }
                }
            }
        }
        return true;
    }


    public function find(Product $product){
        foreach($this->getCartCollections() as $cartCollection){
            foreach ($cartCollection->getCartCategories() as $cartCategory){
                foreach ($cartCategory->getCartRows() as $cartRow){
                    if($cartRow->getProduct()->getId() ==  $product->getId() and  $cartRow->hasQuantity()){
                        return true;
                    }
                }
            }
        }
        return false;
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
            'brand' => $this->brand->getBrandName(),
            'collections' => $this->cartCollections,
        );
    }
}
