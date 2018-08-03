<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\PrimaryCategory;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\Style;
use B2bBundle\Entity\Target;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\ShopRepository")
 */
class Shop {
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
     * @ORM\Column(name="name", type="string", length=45)
     */
    private $name;


    /**
     * @ORM\Column(type="decimal", scale=8, nullable = true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=5)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="town", type="string", length=255)
     */
    private $town;

    /**
     * @var country
     *
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pic;

    /**
     * @var customer
     *
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", inversedBy="shops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Target", cascade={"persist"})
     */
    private $targets;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SalesmanShop", cascade={"persist"}, mappedBy="shop")
     */
    private $salesmen;


    /**
     * @ORM\Column(name="is_active", type="boolean" , options={"default" : 1})
     */
    private $isActive;

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Style", cascade={"persist"})
     */
    private $styles;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\PrimaryCategory", cascade={"persist"})
     */
    private $categories;

    /**
     * @var string
     *
     * @ORM\Column(name="urlWebsite", type="string", length=255, nullable=true)
     */
    private $urlWebsite;

    /**
     * @var string
     *
     * @ORM\Column(name="urlInstagram", type="string", length=255, nullable=true)
     */
    private $urlInstagram;

    /**
     * @var string
     *
     * @ORM\Column(name="urlFacebook", type="string", length=255, nullable=true)
     */
    private $urlFacebook;

    /**
     * @var bool
     *
     * @ORM\Column(name="deliverySameAddress", type="boolean")
     */
    private $deliverySameAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryAddress", type="string", length=255, nullable=true)
     */
    private $deliveryAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryZP", type="string", length=255, nullable=true)
     */
    private $deliveryZP;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryTown", type="string", length=255, nullable=true)
     */
    private $deliveryTown;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryComment", type="text", nullable=true)
     */
    private $deliveryComment;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\ContactCustomer", cascade={"persist"})
     */
    private $contacts;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Shop
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set latitude
     *
     * @param $latitude
     * @return Shop
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return decimal
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param $longitude
     * @return Shop
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return decimal
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Shop
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Shop
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Shop
     */
    public function setTown($town) {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown() {
        return $this->town;
    }

    public function getFullAddress() {
        return $this->address.', '.$this->zipCode.' '.$this->town;
    }

    /**
     * Set country
     *
     * @param Country $country
     * @return Shop
     */
    public function setCountry($country) {
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
     * Set phone
     *
     * @param string $phone
     * @return Shop
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set pic
     *
     * @param string $pic
     * @return Shop
     */
    public function setPic($pic) {
        $this->pic = $pic;

        return $this;
    }

    /**
     * Get pic
     *
     * @return string
     */
    public function getPic() {
        return $this->pic;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Set customer
     * @param Customer $customer
     * @return Shop
     */
    public function setCustomer(Customer $customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Add target
     *
     * @param Target $target
     * @return Shop
     */
    public function addTarget(Target $target) {
        $this->targets[] = $target;

        return $this;
    }

    /**
     * Remove target
     *
     * @param Target $target
     */
    public function removeTarget(Target $target) {
        $this->targets->removeElement($target);
    }

    /**
     * Get targets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTargets() {
        return $this->targets;
    }

    /**
     * Add salesman
     *
     * @param SalesmanShop $salesman
     * @return Shop
     */
    public function addSalesman(SalesmanShop $salesman) {
        $this->salesmen[] = $salesman;
        return $this;
    }

    /**
     * Remove salesman
     *
     * @param SalesmanShop $salesman
     */
    public function removeSalesman(SalesmanShop $salesman) {
        $this->salesmen->removeElement($salesman);
    }

    /**
     * Get salesmen
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalesmen() {
        return $this->salesmen;
    }

    /**
     * Add category
     *
     * @param PrimaryCategory $category
     *
     * @return Shop
     */
    public function addCategory(PrimaryCategory $category) {
        $this->categories[] = $category;
        return $this;
    }

    /**
     * Remove category
     *
     * @param PrimaryCategory $category
     */
    public function removeCategory(PrimaryCategory $category) {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Add style
     *
     * @param Style $style
     *
     * @return Shop
     */
    public function addStyle(Style $style) {
        $this->styles[] = $style;

        return $this;
    }

    /**
     * Remove style
     *
     * @param Style $style
     */
    public function removeStyle(Style $style) {
        $this->styles->removeElement($style);
    }

    /**
     * Get styles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStyles() {
        return $this->styles;
    }

    /**
     * Set urlWebsite
     *
     * @param string $urlWebsite
     * @return Shop
     */
    public function setUrlWebsite($urlWebsite) {
        $this->urlWebsite = $urlWebsite;

        return $this;
    }

    /**
     * Get urlWebsite
     *
     * @return string
     */
    public function getUrlWebsite() {
        return $this->urlWebsite;
    }

    /**
     * Set urlInstagram
     *
     * @param string $urlInstagram
     * @return Shop
     */
    public function setUrlInstagram($urlInstagram) {
        $this->urlInstagram = $urlInstagram;

        return $this;
    }

    /**
     * Get urlInstagram
     *
     * @return string
     */
    public function getUrlInstagram() {
        return $this->urlInstagram;
    }

    /**
     * Set urlFacebook
     *
     * @param string $urlFacebook
     * @return Shop
     */
    public function setUrlFacebook($urlFacebook) {
        $this->urlFacebook = $urlFacebook;

        return $this;
    }

    /**
     * Get urlFacebook
     *
     * @return string
     */
    public function getUrlFacebook() {
        return $this->urlFacebook;
    }

    /**
     * Set deliverySameAddress
     * @param $deliverySameAddress
     * @return $this
     */
    public function setDeliverySameAddress($deliverySameAddress) {
        $this->deliverySameAddress = $deliverySameAddress;

        return $this;
    }

    /**
     * Get deliverySameAddress
     *
     * @return bool
     */
    public function getDeliverySameAddress() {
        return $this->deliverySameAddress;
    }

    /**
     * Set deliveryAddress
     *
     * @param string $deliveryAddress
     * @return Shop
     */
    public function setDeliveryAddress($deliveryAddress) {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * Get deliveryAddress
     *
     * @return string
     */
    public function getDeliveryAddress() {
        return $this->deliveryAddress;
    }

    /**
     * Set deliveryZP
     *
     * @param string $deliveryZP
     * @return Shop
     */
    public function setDeliveryZP($deliveryZP) {
        $this->deliveryZP = $deliveryZP;

        return $this;
    }

    /**
     * Get deliveryZP
     *
     * @return string
     */
    public function getDeliveryZP() {
        return $this->deliveryZP;
    }

    /**
     * Set deliveryTown
     *
     * @param string $deliveryTown
     * @return Shop
     */
    public function setDeliveryTown($deliveryTown) {
        $this->deliveryTown = $deliveryTown;

        return $this;
    }

    /**
     * Get deliveryTown
     *
     * @return string
     */
    public function getDeliveryTown() {
        return $this->deliveryTown;
    }

    /**
     * Set delivery comment
     *
     * @param $deliveryComment
     * @return Shop
     */
    public function setDeliveryComment($deliveryComment) {
        $this->deliveryComment = $deliveryComment;

        return $this;
    }

    /**
     * Get delivery comment
     *
     * @return string
     */
    public function getDeliveryComment() {
        return $this->deliveryComment;
    }

    /**
     * Add contact
     *
     * @param ContactCustomer $contact
     * @return Shop
     */
    public function addContact(ContactCustomer $contact) {
        $this->contacts[] = $contact;

        return $this;
    }

    /**
     * Remove contact
     *
     * @param ContactCustomer $contact
     */
    public function removeContact(ContactCustomer $contact) {
        $this->contacts->removeElement($contact);
    }

    /**
     * Get contacts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContacts() {
        return $this->contacts;
    }

    /**
     * Shop constructor.
     */
    public function __construct() {
        $this->targets = new ArrayCollection();
        $this->salesmen = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
    }
    /**
     * Set inactive
     * @return $this
     */
    public function setInactive() {
        $this->isActive = False;
        return $this;
    }

    /**
     * Set active
     * @return $this
     */
    public function setActive() {
        $this->isActive = True;
        return $this;
    }
}
