<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\Brand;
use B2bBundle\Entity\ContactBrand;
use B2bBundle\Entity\Departement;
use B2bBundle\Entity\Shop;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Salesman
 *
 * @ORM\Table(name="salesman")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SalesmanRepository")
 */
class Salesman extends User {

    const STATUS_ACTIVE        = 'active';
    const STATUS_VERIFICATION  = 'verification';

    /**
     * @var enum
     *
     * @ORM\Column(name="state", type="string", columnDefinition="enum('active', 'verification')")
     */
    private $state;

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
     * @var string
     *
     * @ORM\Column(name="billingAddress", type="string", length=255)
     */
    private $billingAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="billingZP", type="string", length=255)
     */
    private $billingZP;

    /**
     * @var string
     *
     * @ORM\Column(name="billingTown", type="string", length=255)
     */
    private $billingTown;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\ContactBrand", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;


    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Country", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SalesmanShop", cascade={"persist"}, mappedBy="salesman")
     */
    private $shops;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SalesmanBrandLink", cascade={"persist"}, mappedBy="salesman")
     */
    private $brands;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SubSalesmanLink", cascade={"persist"}, mappedBy="subordinate")
     */
    private $superiors;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SubSalesmanLink", cascade={"persist"}, mappedBy="superior")
     */
    private $subordinates;

    /**
     * Check if Salesman is active
     * @return bool
     */
    public function isActive() {
        return $this->state == Salesman::STATUS_ACTIVE;
    }

    /**
     * Check if Salesman is in verification
     * @return bool
     */
    public function isInVerification() {
        return $this->state == Salesman::STATUS_VERIFICATION;
    }

    /**
     * Set active State
     * @return Salesman
     */
    public function setActiveStatus() {
        $this->state = Salesman::STATUS_ACTIVE;

        return $this;
    }

    /**
     * Set in verification State
     * @return Salesman
     */
    public function setInVerificaton() {
        $this->state = Salesman::STATUS_VERIFICATION;

        return $this;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Salesman
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return Salesman
     */
    public function setCompanyName($companyName) {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName() {
        return $this->companyName;
    }

    /**
     * Set numTVA
     *
     * @param string $numTVA
     * @return Salesman
     */
    public function setNumTVA($numTVA) {
        $this->numTVA = $numTVA;

        return $this;
    }

    /**
     * Get numTVA
     *
     * @return string
     */
    public function getNumTVA() {
        return $this->numTVA;
    }

    /**
     * Set billingAddress
     *
     * @param string $billingAddress
     * @return Salesman
     */
    public function setBillingAddress($billingAddress) {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return string
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * Set billingZP
     *
     * @param string $billingZP
     * @return Salesman
     */
    public function setBillingZP($billingZP) {
        $this->billingZP = $billingZP;

        return $this;
    }

    /**
     * Get billingZP
     *
     * @return string
     */
    public function getBillingZP() {
        return $this->billingZP;
    }

    /**
     * Set billingTown
     *
     * @param string $billingTown
     * @return Salesman
     */
    public function setBillingTown($billingTown) {
        $this->billingTown = $billingTown;

        return $this;
    }

    /**
     * Get billingTown
     *
     * @return string
     */
    public function getBillingTown() {
        return $this->billingTown;
    }
    /**
     * Add contact
     *
     * @param ContactBrand $contact
     * @return Salesman
     */
    public function addContact(ContactBrand $contact)
    {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param ContactBrand $contact
     */
    public function removeContact(ContactBrand $contact)
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
     * Set country
     *
     * @param Country $country
     *
     * @return Salesman
     */
    public function setCountry(Country $country) {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Add shop
     *
     * @param SalesmanShop $shop
     *
     * @return Salesman
     */
    public function addShop(SalesmanShop $shop) {
        $this->shops[] = $shop;
        return $this;
    }

    /**
     * Remove shop
     *
     * @param SalesmanShop $shop
     */
    public function removeShop(SalesmanShop $shop) {
        $this->shops->removeElement($shop);
    }

    /**
     * Get shops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShops() {
        return $this->shops;
    }

    /**
     * Add brand link
     *
     * @param SalesmanBrandLink $brandLink
     * @return Salesman
     */
    public function addBrand(SalesmanBrandLink $brandLink) {
        $this->brands[] = $brandLink;
        //$brand->addSalesman($this);

        return $this;
    }

    /**
     * Remove brand link
     *
     * @param SalesmanBrandLink $brandLink
     */
    public function removeBrand(SalesmanBrandLink $brandLink) {
        $this->brands->removeElement($brandLink);
    }

    /**
     * Get brands links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands() {
        return $this->brands;
    }

    /**
     * Add superior link
     *
     * @param SubSalesmanLink $salesmanLink
     * @return Salesman
     */
    public function addSuperior(SubSalesmanLink $salesmanLink) {
        $this->superiors[] = $salesmanLink;

        return $this;
    }

    /**
     * Remove superior link
     *
     * @param SubSalesmanLink $salesmanLink
     */
    public function removeSuperior(SubSalesmanLink $salesmanLink) {
        $this->superiors->removeElement($salesmanLink);
    }

    /**
     * Get superior links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuperiors() {
        return $this->superiors;
    }

    /**
     * Add subordinate link
     *
     * @param SubSalesmanLink $salesmanLink
     * @return Salesman
     */
    public function addSubordinate(SubSalesmanLink $salesmanLink) {
        $this->subordinates[] = $salesmanLink;

        return $this;
    }

    /**
     * Remove subordinate link
     *
     * @param SubSalesmanLink $salesmanLink
     */
    public function removeSubordinate(SubSalesmanLink $salesmanLink) {
        $this->subordinates->removeElement($salesmanLink);
    }

    /**
     * Get subordinate links
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubordinates() {
        return $this->subordinates;
    }

    public function __toString() {
        return $this->lastName;
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->state = Salesman::STATUS_VERIFICATION;
        $this->setRoles(array('ROLE_SALESMAN'));
        $this->contacts = new ArrayCollection();
        $this->shops = new ArrayCollection();
        $this->brands = new ArrayCollection();

        $this->superiors = new ArrayCollection();
        $this->subordinates = new ArrayCollection();
    }

    public function getUser() {
        return parent::getParent();
    }
    /**
     * Set deductibleTVA
     *
     * @param $deductibleTVA
     * @return Salesman
     */
    public function setDeductibleTVA($deductibleTVA) {
        $this->deductibleTVA = $deductibleTVA;

        return $this;
    }

    /**
     * Get deductibleTVA
     *
     * @return bool
     */
    public function getDeductibleTVA() {
        return $this->deductibleTVA;
    }



    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
