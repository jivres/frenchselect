<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\Collection;
use B2bBundle\Entity\Salesman;
use B2bBundle\Entity\Shop;
use B2bBundle\Entity\Style;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Brand
 *
 * @ORM\Table(name="brand")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\BrandRepository")
 * @ORM\Table(name="brand", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1C52F958C3E49468", columns={"cgv_id"})}, indexes={@ORM\Index(name="IDX_1C52F958F92F3E70", columns={"country_id"}), @ORM\Index(name="IDX_1C52F958E052B35C", columns={"manufacture_country_id"}), @ORM\Index(name="IDX_1C52F9584834EBA6", columns={"price_range_id"}), @ORM\Index(name="IDX_1C52F958754851E1", columns={"billing_country_id"})})
 */
class Brand extends User
{


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Feature", inversedBy="brand")
     * @ORM\JoinTable(name="brand_feature",
     *   joinColumns={
     *     @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="feature_id", referencedColumnName="id")
     *   }
     * )
     */
    private $feature;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Univers", inversedBy="brand")
     * @ORM\JoinTable(name="brand_univers",
     *   joinColumns={
     *     @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="univers_id", referencedColumnName="id")
     *   }
     * )
     */
    private $univers;
    /**
     * @var string
     *
     * @ORM\Column(name="brandName", type="string", length=255)
     */
    private $brandName;

    /**
     * @var string
     *
     * @ORM\Column(name="numTVA", type="string", length=255, nullable=true)
     *
     *
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
     * @ORM\Column(name="numSIREN", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $numSIREN;

    /**
     * @var string
     *
     * @ORM\Column(name="RCSTown", type="string", length=255)
     */
    private $RCSTown;

    /**
     * @var string
     *
     * @ORM\Column(name="APECode", type="string", length=5, nullable=true)
     * @Assert\Regex("/^[0-9]{4}[A-Za-z]$/")
     */
    private $APECode;

    /**
     * @var float
     *
     * @ORM\Column(name="capital", type="decimal")
     *
     * @Assert\Regex("/^[0-9]*,{0,1}[0-9]*$/")
     */
    private $capital;

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
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"persist", "remove"})
     * @Assert\NotBlank(message="Merci d'envoyer les CGV au format PDF")
     * //TODO //@Assert\File(mimeTypes={ "application/pdf" })
     */
    private $CGV;



    /**
     * @var float
     *
     * @ORM\Column(name="commandMin", type="float")
     *
     * @Assert\Regex("/^[0-9]*,{0,1}[0-9]*$/")
     */
    private $commandMin;

    /**
     * @var string
     *
     * @ORM\Column(name="franco", type="string", length=255, nullable=true)
     *
     * @Assert\Regex("/^[0-9]*,{0,1}[0-9]*$/")
     */
    private $franco;

    /**
     * @var float
     *
     * @ORM\Column(name="deliveryCharges", type="float", nullable=true)
     *
     * @Assert\Regex("/^[0-9]*,{0,1}[0-9]*$/")
     */
    private $deliveryCharges;

    /**
     * @var int
     *
     * @ORM\Column(name="yearCreation", type="integer", nullable = true)
     *
     * @Assert\Regex("/^[0-9]{4}$/")
     */
    private $yearCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="urlWebsite", type="string", length=255, nullable=true)
     */
    private $urlWebsite;


    /**
     * @var float
     *
     * @ORM\Column(name="marginAvg", type="float")
     */
    private $marginAvg;

    /**
     * @var string
     *
     * @ORM\Column(name="slogan", type="string", length=255, nullable=false)
     */
    private $slogan;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;



    /**
     * @var string|null
     *
     * @ORM\Column(name="url_insta", type="string", length=255, nullable=true)
     */
    private $urlInsta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url_fac", type="string", length=255, nullable=true)
     */
    private $urlFac;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictureHomme;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $lifestyleHomme;


    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictureFemme;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictureEnfant;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */

    private $lifestyleFemme;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */

    private $lifestyleEnfant;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */

    private $lifestyle2;

    /**
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $logo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_inscription", type="date", nullable=false)
     */
    private $dateInscription;

    /**
     * @var \Target
     *
     * @ORM\ManyToOne(targetEntity="Target")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primaryTarget", referencedColumnName="id", nullable=false)
     * })
     *
     */
    private $primarytarget;

    /**
     * @var \Recommandation
     *
     * @ORM\ManyToOne(targetEntity="Recommandation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recommandation_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $recommandation = '1';

    /**
     * @return \Recommandation
     */
    public function getRecommandation()
    {
        return $this->recommandation;
    }

    /**
     * @param \Recommandation $recommandation
     */
    public function setRecommandation($recommandation)
    {
        $this->recommandation = $recommandation;
    }


    /**
     * @var \PriceRange
     *
     * @ORM\ManyToOne(targetEntity="PriceRange")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="price_range_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $priceRange;


    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manufacture_country_id", referencedColumnName="id")
     * })
     */
    private $manufactureCountry;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $country;


    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\PaymentMethod", cascade={"persist"})
     */
    private $paymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Collection", cascade={"persist"}, mappedBy="brand")
     * @ORM\OrderBy({"year" = "desc", "season" = "desc"})
     */
    private $collections;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\PaymentTerms", cascade={"persist"})
     */
    private $paymentTerms;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\PrimaryCategory", cascade={"persist"},inversedBy="brand" )
     */
    private $categories;


    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Style", cascade={"persist"})
     * @Assert\Count(
     *       min = 1,
     *       minMessage = "Vous devez choisir au moins un style",
     * )
     */
    private $styles;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\ContactBrand", cascade={"persist"})
     */
    private $contacts;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Target")
     * @ORM\JoinTable(name="brand_secondary_target",
     *   joinColumns={
     *     @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     *   }
     * )
     */
    private $targets;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\SalesmanBrandLink", cascade={"persist"}, mappedBy="brand")
     */
    private $salesmen;

    /**
     * @var bool
     * @ORM\Column(name="accessRestricted", type="boolean")
     */
    private $accessRestricted;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Customer", mappedBy="brand")
     */
    private $customer;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }


    /**
     * Add customer
     * @param Customer $customer
     * @return Brand
     */
    public function addCustomer(Customer $c)
    {
        $c->addBrand($this);
        $this->customer[] = $c;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param \B2bBundle\Entity\Customer $customer
     */
    public function removeCustomer(\B2bBundle\Entity\Customer $customer)
    {
        $customer->removeBrand($this);
        $this->customer->removeElement($customer);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Brand
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set Logo
     *
     * @param UploadedFile $logo
     *
     * @return Brand
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set brandName
     *
     * @param string $brandName
     *
     * @return Brand
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;

        return $this;
    }

    /**
     * Get brandName
     *
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * Set numTVA
     *
     * @param string $numTVA
     *
     * @return Brand
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
     * Set numSIREN
     *
     * @param string $numSIREN
     *
     * @return Brand
     */
    public function setNumSIREN($numSIREN)
    {
        $this->numSIREN = $numSIREN;

        return $this;
    }

    /**
     * Get numSIREN
     *
     * @return string
     */
    public function getNumSIREN()
    {
        return $this->numSIREN;
    }

    /**
     * Set RCSTown
     *
     * @param $RCSTown
     * @return Brand
     */
    public function setRCSTown($RCSTown)
    {
        $this->RCSTown = $RCSTown;

        return $this;
    }

    /**
     * Get RCSTown
     *
     * @return string
     */
    public function getRCSTown()
    {
        return $this->RCSTown;
    }

    /**
     * Set APECode
     *
     * @param $APECode
     * @return Brand
     */
    public function setAPECode($APECode)
    {
        $this->APECode = $APECode;

        return $this;
    }

    /**
     * Get APECode
     *
     * @return string
     */
    public function getAPECode()
    {
        return $this->APECode;
    }

    /**
     * Set capital
     *
     * @param $capital
     * @return Brand
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get capital
     *
     * @return float
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * Set billingAddress
     *
     * @param string $billingAddress
     *
     * @return Brand
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
     * @return Brand
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
     * @return Brand
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
     * @param $billingCountry
     * @return Brand
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    /**
     * Get billingCountry
     *
     * @return Country
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
     * @return Brand
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
     * Set competing
     *
     * @param string $competing
     *
     * @return Brand
     */
    public function setCompeting($competing)
    {
        $this->competing = $competing;

        return $this;
    }

    /**
     * Get competing
     *
     * @return string
     */
    public function getCompeting()
    {
        return $this->competing;
    }

    /**
     * Set commandMin
     *
     * @param float $commandMin
     *
     * @return Brand
     */
    public function setCommandMin($commandMin)
    {
        $this->commandMin = $commandMin;

        return $this;
    }

    /**
     * Get commandMin
     *
     * @return float
     */
    public function getCommandMin()
    {
        return $this->commandMin;
    }

    /**
     * Set franco
     *
     * @param string $franco
     *
     * @return Brand
     */
    public function setFranco($franco)
    {
        $this->franco = $franco;

        return $this;
    }

    /**
     * Get franco
     *
     * @return string
     */
    public function getFranco()
    {
        return $this->franco;
    }

    /**
     * Set deliveryCharges
     *
     * @param float $deliveryCharges
     *
     * @return Brand
     */
    public function setDeliveryCharges($deliveryCharges)
    {
        $this->deliveryCharges = $deliveryCharges;

        return $this;
    }

    /**
     * Get deliveryCharges
     *
     * @return float
     */
    public function getDeliveryCharges()
    {
        return $this->deliveryCharges;
    }

    /**
     * Set yearCreation
     *
     * @param integer $yearCreation
     *
     * @return Brand
     */
    public function setYearCreation($yearCreation)
    {
        $this->yearCreation = $yearCreation;

        return $this;
    }

    /**
     * Get yearCreation
     *
     * @return int
     */
    public function getYearCreation()
    {
        return $this->yearCreation;
    }

    /**
     * Set urlWebsite
     *
     * @param string $urlWebsite
     * @return Brand
     */
    public function setUrlWebsite($urlWebsite)
    {
        $this->urlWebsite = $urlWebsite;

        return $this;
    }

    /**
     * Get urlWebsite
     *
     * @return string
     */
    public function getUrlWebsite()
    {
        return $this->urlWebsite;
    }


    /**
     * Set marginAvg
     *
     * @param float $marginAvg
     *
     * @return Brand
     */
    public function setMarginAvg($marginAvg)
    {
        $this->marginAvg = $marginAvg;

        return $this;
    }

    /**
     * Get marginAvg
     *
     * @return float
     */
    public function getMarginAvg()
    {
        return $this->marginAvg;
    }

    /**
     * Set CGV
     *
     * @param string $CGV
     *
     * @return Brand
     */
    public function setCGV($CGV)
    {
        $this->CGV = $CGV;
        return $this;
    }

    /**
     * Get CGV
     *
     * @return string
     */
    public function getCGV()
    {
        return $this->CGV;
    }

    /**
     * Set slogan
     *
     * @param string $slogan
     *
     * @return Brand
     */
    public function setSlogan($slogan)
    {
        $this->slogan = $slogan;

        return $this;
    }

    /**
     * Get slogan
     *
     * @return string
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Brand
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set country
     *
     * @param \B2bBundle\Entity\Country $country
     *
     * @return Brand
     */
    public function setCountry(\B2bBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \B2bBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set manufactureCountry
     *
     * @param \B2bBundle\Entity\Country $manufactureCountry
     *
     * @return Brand
     */
    public function setManufactureCountry(\B2bBundle\Entity\Country $manufactureCountry)
    {
        $this->manufactureCountry = $manufactureCountry;

        return $this;
    }

    /**
     * Get manufactureCountry
     *
     * @return \B2bBundle\Entity\Country
     */
    public function getManufactureCountry()
    {
        return $this->manufactureCountry;
    }

    /**
     * Set priceRange
     *
     * @param \B2bBundle\Entity\PriceRange $priceRange
     *
     * @return Brand
     */
    public function setPriceRange(\B2bBundle\Entity\PriceRange $priceRange)
    {
        $this->priceRange = $priceRange;

        return $this;
    }

    /**
     * Get priceRange
     *
     * @return \B2bBundle\Entity\PriceRange
     */
    public function getPriceRange()
    {
        return $this->priceRange;
    }

    /**
     * Add paymentMethod
     *
     * @param \B2bBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return Brand
     */
    public function addPaymentMethod(\B2bBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods[] = $paymentMethod;

        return $this;
    }

    /**
     * Remove paymentMethod
     *
     * @param \B2bBundle\Entity\PaymentMethod $paymentMethod
     */
    public function removePaymentMethod(\B2bBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods->removeElement($paymentMethod);
    }

    /**
     * Get paymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * Add paymentTerm
     *
     * @param \B2bBundle\Entity\PaymentTerms $paymentTerm
     *
     * @return Brand
     */
    public function addPaymentTerm(\B2bBundle\Entity\PaymentTerms $paymentTerm)
    {
        $this->paymentTerms[] = $paymentTerm;

        return $this;
    }

    /**
     * Remove paymentTerm
     *
     * @param \B2bBundle\Entity\PaymentTerms $paymentTerm
     */
    public function removePaymentTerm(\B2bBundle\Entity\PaymentTerms $paymentTerm)
    {
        $this->paymentTerms->removeElement($paymentTerm);
    }

    /**
     * Get paymentTerms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentTerms()
    {
        return $this->paymentTerms;
    }

    /**
     * Add category
     *
     * @param \B2bBundle\Entity\PrimaryCategory $category
     *
     * @return Brand
     */
    public function addCategory(\B2bBundle\Entity\PrimaryCategory $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \B2bBundle\Entity\PrimaryCategory $category
     */
    public function removeCategory(\B2bBundle\Entity\PrimaryCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add style
     *
     * @param Style $style
     * @return Brand
     */
    public function addStyle(Style $style)
    {
        $this->styles[] = $style;

        return $this;
    }

    /**
     * Remove style
     *
     * @param Style $style
     */
    public function removeStyle(Style $style)
    {
        $this->styles->removeElement($style);
    }

    /**
     * Get styles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Add contact
     *
     * @param ContactBrand $contact
     * @return Brand
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
     * Add target
     *
     * @param Target $target
     * @return Brand
     */
    public function addTarget(Target $target)
    {
        $this->targets[] = $target;

        return $this;
    }

    /**
     * Remove target
     *
     * @param Target $target
     */
    public function removeTarget(Target $target)
    {
        $this->targets->removeElement($target);
    }

    /**
     * Get targets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Add salesman link
     *
     * @param SalesmanBrandLink $salesmanBrandLink
     * @return Brand
     */
    public function addSalesman(SalesmanBrandLink $salesmanBrandLink)
    {
        $this->salesmen[] = $salesmanBrandLink;

        return $this;
    }

    /**
     * Remove salesman
     *
     * @param SalesmanBrandLink $salesmanBrandLink
     */
    public function removeSalesman(SalesmanBrandLink $salesmanBrandLink)
    {
        $this->salesmen->removeElement($salesmanBrandLink);
    }

    /**
     * Get salesmen
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalesmen()
    {
        return $this->salesmen;
    }

    /**
     * Add collection
     *
     * @param Collection $collection
     *
     * @return Brand
     */
    public function addCollection(Collection $collection)
    {
        $this->collections[] = $collection;

        return $this;
    }

    /**
     * Remove collection
     *
     * @param Collection $collection
     */
    public function removeCollection(Collection $collection)
    {
        $this->collections->removeElement($collection);
    }

    /**
     * Get collections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollections()
    {
        return $this->collections;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRoles(array('ROLE_BRAND'));
        $this->paymentMethods = new ArrayCollection();
        $this->paymentTerms = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->targets = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->univers = new ArrayCollection();
    }

    public function getUser()
    {
        return parent::getParent();
    }

    public function __toString()
    {
        return (string) $this->brandName;
    }

    /**
     * Set urlInsta.
     *
     * @param string|null $urlInsta
     *
     * @return Brand
     */
    public function setUrlInsta($urlInsta = null)
    {
        $this->urlInsta = $urlInsta;

        return $this;
    }

    /**
     * Get urlInsta.
     *
     * @return string|null
     */
    public function getUrlInsta()
    {
        return $this->urlInsta;
    }

    /**
     * Set urlFac.
     *
     * @param string|null $urlFac
     *
     * @return Brand
     */
    public function setUrlFac($urlFac = null)
    {
        $this->urlFac = $urlFac;

        return $this;
    }

    /**
     * Get urlFac.
     *
     * @return string|null
     */
    public function getUrlFac()
    {
        return $this->urlFac;
    }

    /**
     * Set pictureHomme.
     *
     * @param string|null $pictureHomme
     *
     * @return Brand
     */
    public function setPictureHomme($pictureHomme = null)
    {
        $this->pictureHomme = $pictureHomme;

        return $this;
    }

    /**
     * Get pictureHomme.
     *
     * @return string|null
     */
    public function getPictureHomme()
    {
        return $this->pictureHomme;
    }

    /**
     * Set lifestyleHomme.
     *
     * @param string|null $lifestyleHomme
     *
     * @return Brand
     */
    public function setLifestyleHomme($lifestyleHomme = null)
    {
        $this->lifestyleHomme = $lifestyleHomme;

        return $this;
    }

    /**
     * Get lifestyleHomme.
     *
     * @return string|null
     */
    public function getLifestyleHomme()
    {
        return $this->lifestyleHomme;
    }

    /**
     * Set lifestyle2.
     *
     * @param string|null $lifestyle2
     *
     * @return Brand
     */
    public function setLifestyle2($lifestyle2 = null)
    {
        $this->lifestyle2 = $lifestyle2;

        return $this;
    }

    /**
     * Get lifestyle2.
     *
     * @return string|null
     */
    public function getLifestyle2()
    {
        return $this->lifestyle2;
    }


    /**
     * Set dateInscription.
     *
     * @param \DateTime|null $dateInscription
     *
     * @return Brand
     */
    public function setDateInscription($dateInscription = null)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get dateInscription.
     *
     * @return \DateTime|null
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set pictureFemme.
     *
     * @param string $pictureFemme
     *
     * @return Brand
     */
    public function setPictureFemme($pictureFemme)
    {
        $this->pictureFemme = $pictureFemme;

        return $this;
    }

    /**
     * Get pictureFemme.
     *
     * @return string
     */
    public function getPictureFemme()
    {
        return $this->pictureFemme;
    }

    /**
     * Set pictureEnfant.
     *
     * @param string $pictureEnfant
     *
     * @return Brand
     */
    public function setPictureEnfant($pictureEnfant)
    {
        $this->pictureEnfant = $pictureEnfant;

        return $this;
    }

    /**
     * Get pictureEnfant.
     *
     * @return string
     */
    public function getPictureEnfant()
    {
        return $this->pictureEnfant;
    }

    /**
     * Set lifestyleFemme.
     *
     * @param string $lifestyleFemme
     *
     * @return Brand
     */
    public function setLifestyleFemme($lifestyleFemme)
    {
        $this->lifestyleFemme = $lifestyleFemme;

        return $this;
    }

    /**
     * Get lifestyleFemme.
     *
     * @return string
     */
    public function getLifestyleFemme()
    {
        return $this->lifestyleFemme;
    }

    /**
     * Set lifestyleEnfant.
     *
     * @param string $lifestyleEnfant
     *
     * @return Brand
     */
    public function setLifestyleEnfant($lifestyleEnfant)
    {
        $this->lifestyleEnfant = $lifestyleEnfant;

        return $this;
    }

    /**
     * Get lifestyleEnfant.
     *
     * @return string
     */
    public function getLifestyleEnfant()
    {
        return $this->lifestyleEnfant;
    }

    /**
     * Set primarytarget.
     *
     * @param int $primarytarget
     *
     * @return Brand
     */
    public function setPrimarytarget($primarytarget)
    {
        $this->primarytarget = $primarytarget;

        return $this;
    }

    /**
     * Get primarytarget.
     *
     * @return int
     */
    public function getPrimarytarget()
    {
        return $this->primarytarget;
    }





    /**
     * Get univers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUnivers()
    {
        return $this->univers;
    }

    /**
     * Get feature
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeature()
    {
        return $this->feature;
    }


    /**
     * Get accessRestricted
     *
     * @return bool
     */
    public function getAccessRestricted()
    {
        return $this->accessRestricted;
    }

    /**
     * Set accessRestricted
     *
     * @param $accessRestricted
     * @return Brand
     */
    public function setAccessRestricted($accessRestricted)
    {
        $this->accessRestricted = $accessRestricted;

        return $this;
    }

    /**
     * Add univers
     *
     * @param \B2bBundle\Entity\Univers $univers
     *
     * @return Brand
     */
    public function addUnivers(\B2bBundle\Entity\Univers $univers)
    {
        $this->univers[] = $univers;

        return $this;
    }


    /**
     * Add feature.
     *
     * @param \B2bBundle\Entity\Feature $feature
     *
     * @return Brand
     */
    public function addFeature(\B2bBundle\Entity\Feature $feature)
    {
        $this->feature[] = $feature;

        return $this;
    }

    /**
     * Remove feature.
     *
     * @param \B2bBundle\Entity\Feature $feature
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFeature(\B2bBundle\Entity\Feature $feature)
    {
        return $this->feature->removeElement($feature);
    }


    /**
     * Remove univers.
     *
     * @param \B2bBundle\Entity\Univers $univer
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUnivers(\B2bBundle\Entity\Univers $univers)
    {
        return $this->univers->removeElement($univers);
    }


    /**
     * Set deductibleTVA
     *
     * @param $deductibleTVA
     * @return Brand
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



}
