<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\Access;
use B2bBundle\Entity\Cart;
use B2bBundle\Entity\Shop;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CustomerRepository")
 */
class Customer extends User
{

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=255)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="numTVA", type="string", length=255, nullable=true)
     */
    private $numTVA;

    /**
     * @var bool
     *
     * @ORM\Column(name="deductibleTVA", type="boolean")
     */
    private $deductibleTVA;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\ContactCustomer", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $contacts;


    /**
     * @var string
     *
     * @ORM\Column(name="numSIREN", type="string", length=9, nullable=true)
     */
    private $numSIREN;

    /**
     * @var string
     *
     * @ORM\Column(name="limitCredit", type="string", nullable=true)
     * @Assert\Regex("/^[0-9]+$/")
     */
    private $limitCredit;

    /**
     * @var string
     *
     * @ORM\Column(name="billingAddress", type="string", length=255, nullable=false)
     */
    private $billingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="billingZP", type="string", length=255, nullable=false)
     */
    private $billingZP;

    /**
     * @var string
     *
     * @ORM\Column(name="billingTown", type="string", length=255, nullable=false)
     */
    private $billingTown;

    /**
     * @var country
     *
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $billingCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;


    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Shop", mappedBy="customer", cascade={"persist"})
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez saisir au moins une boutique",
     *     )
     */
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Cart", cascade={"persist"}, mappedBy="customer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carts;


    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Access", cascade={"persist", "remove"}, mappedBy="customer")
     *
     */
    private $accesses;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Brand", inversedBy="customer")
     * @ORM\JoinTable(name="brand_customer",
     *   joinColumns={
     *     @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     *   }
     * )
     */
    private $brand;


    /**
     * @ORM\Column(name="valid", type="boolean" ,options={"default" : 0})
     */
    private $valid = 0;

    public function isValid() {
        return $this->valid;
    }

    public function validate(){
        $this->valid = 1;
    }



    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }



    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return Customer
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set numTVA
     *
     * @param string $numTVA
     *
     * @return Customer
     */
    public function setNumTVA($numTVA)
    {
        $this->numTVA = $numTVA;

        return $this;
    }

    /**
     * Get numTVA
     *
     * @return string
     */
    public function getNumTVA()
    {
        return $this->numTVA;
    }

    /**
     * Set deductibleTVA
     *
     * @param $deductibleTVA
     * @return Customer
     */
    public function setDeductibleTVA($deductibleTVA)
    {
        $this->deductibleTVA = $deductibleTVA;

        return $this;
    }

    /**
     * Get deductibleTVA
     *
     * @return bool
     */
    public function getDeductibleTVA()
    {
        return $this->deductibleTVA;
    }

    /**
     * Set numSIREN
     *
     * @param string $numSIREN
     *
     * @return Customer
     */
    public function setNumSIREN($numSIREN)
    {
        $this->numSIREN = $numSIREN;

        return $this;
    }

    /**
     * Get numTVA
     *
     * @return string
     */
    public function getNumSIREN()
    {
        return $this->numSIREN;
    }

    /**
     * Set numSIREN
     *
     * @param string $numSIREN
     *
     * @return Customer
     */
    public function setLimitCredit($limitCredit)
    {
        $this->numSIREN = $limitCredit;

        return $this;
    }

    /**
     * Get numTVA
     *
     * @return string
     */
    public function getLimitCredit()
    {
        return $this->limitCredit;
    }

    /**
     * Set billingAddress
     *
     * @param string $billingAddress
     *
     * @return Customer
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return string
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set billingZP
     *
     * @param string $billingZP
     *
     * @return Customer
     */
    public function setBillingZP($billingZP)
    {
        $this->billingZP = $billingZP;

        return $this;
    }

    /**
     * Get billingZP
     *
     * @return string
     */
    public function getBillingZP()
    {
        return $this->billingZP;
    }

    /**
     * Set billingTown
     *
     * @param string $billingTown
     *
     * @return Customer
     */
    public function setBillingTown($billingTown)
    {
        $this->billingTown = $billingTown;

        return $this;
    }

    /**
     * Get billingTown
     *
     * @return string
     */
    public function getBillingTown()
    {
        return $this->billingTown;
    }

    /**
     * Set billingCountry
     *
     * @param country $billingCountry
     * @return Customer
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    /**
     * Get billingCountry
     *
     * @return country
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }


    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Add shop
     * @param Shop $shop
     * @return Customer
     */
    public function addShop(Shop $shop)
    {
        $shop->setCustomer($this);
        $this->shops[] = $shop;

        return $this;
    }


    /**
     * Add brand
     * @param Brand $brand
     * @return Customer
     */
    public function addBrand(Brand $b)
    {
        $this->brand[] = $b;

        return $this;
    }



    /**
     * Remove shop
     * @param Shop $shop
     */
    public function removeShop(Shop $shop)
    {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getShops()
    {
        return $this->shops;
    }

    /**
     * Add cart
     * @param Cart $cart
     * @return Customer
     */
    public function addCart(Cart $cart)
    {
        $this->carts[] = $cart;

        return $this;
    }

    /**
     * Remove cart
     * @param Cart $cart
     */
    public function removeCart(Cart $cart)
    {
        $this->carts->removeElement($cart);
    }

    /**
     * Get carts
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRoles(array('ROLE_CUSTOMER'));
        $this->deductibleTVA = false;

        $this->shops = new ArrayCollection();
        $this->carts = new ArrayCollection();
    }

    public function getUser()
    {
        return parent::getParent();
    }

    public function __toString()
    {
        return $this->companyName;
    }

    /**
     * Add access
     *
     * @param Access $access
     *
     * @return Customer
     */
    public function addAccess(Access $access)
    {
        $this->accesses[] = $access;
        $access->setCustomer($this);
        return $this;
    }

    /**
     * Remove access
     *
     * @param Access $access
     */
    public function removeAccess(Access $access)
    {
        $this->accesses->removeElement($access);
    }

    /**
     * Get accesses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

    /**
     * Add contact
     *
     * @param ContactCustomer $contact
     * @return Customer
     */
    public function addContact(ContactCustomer $contact)
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param ContactCustomer $contact
     */
    public function removeContact(ContactCustomer $contact)
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Remove brand
     *
     * @param \B2bBundle\Entity\Brand $brand
     */
    public function removeBrand(\B2bBundle\Entity\Brand $brand)
    {
        $this->brand->removeElement($brand);
    }


}
