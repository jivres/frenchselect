<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\AllowedColor;
use B2bBundle\Entity\AllowedSize;
use B2bBundle\Entity\Availability;
use B2bBundle\Entity\Collection;
use B2bBundle\Entity\Country;
use B2bBundle\Entity\PrimaryCategory;
use B2bBundle\Entity\SecondaryCategory;
use B2bBundle\Entity\Target;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\ProductRepository")
 */
class Product {

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
     * @ORM\Column(name="refModel", type="string", length=50, unique=true, nullable=false)
     */
    private $refModel;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="tertiaryCategory", type="string", length=255)
     */
    private $tertiaryCategory;

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float")
     */
    private $priceHT;

    /**
     * @var float
     *
     * @ORM\Column(name="recommendedPriceTTC", type="float")
     */
    private $recommendedPriceTTC;

    /**
     * @var string
     *
     * @ORM\Column(name="material", type="text", length=255, nullable=true)
     */
    private $material;

    /**
     * @var string
     *
     * @ORM\Column(name="maintenance", type="string", length=255, nullable=true)
     */
    private $maintenance;

    /**
     * @var string
     *
     * @ORM\Column(name="dimensions", type="string", length=255, nullable=true)
     */
    private $dimensions;


    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\PrimaryCategory", cascade={"persist"}, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $primaryCat;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\SecondaryCategory", cascade={"persist"}, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $secondaryCat;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Target", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $target;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Collection", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $collection;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Country", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\AllowedColor", cascade={"persist", "remove"}, mappedBy="product")
     *  @Assert\Count(
     *      min = 1,
     *      minMessage = "Vous devez saisir au moins une couleur",
     *     )
     */
    private $allowedColors;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\AllowedSize", cascade={"persist", "remove"}, mappedBy="product")
     */
    private $allowedSizes;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Availability", cascade={"persist", "remove"}, mappedBy="product")
     */
    private $availabilities;

    /**
     * @ORM\Column(name="is_active", type="boolean" , options={"default" : 1})
     */
    private $isActive = 1;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */

    private $mainpicture;

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

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set ref
     *
     * @param string $ref
     *
     * @return Product
     */
    public function setRef($ref) {
        $this->refModel = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef() {
        return $this->refModel;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set tertiaryCategory
     *
     * @param string $tertiaryCategory
     *
     * @return Product
     */
    public function setTertiaryCategory($tertiaryCategory) {
        $this->tertiaryCategory = $tertiaryCategory;

        return $this;
    }

    /**
     * Get tertiaryCategory
     *
     * @return string
     */
    public function getTertiaryCategory() {
        return $this->tertiaryCategory;
    }

    /**
     * Set deliveryStart
     *
     * @param \DateTime $deliveryStart
     *
     * @return Product
     */
    public function setDeliveryStart(\DateTime $deliveryStart) {
        $this->deliveryStart = $deliveryStart;

        return $this;
    }

    /**
     * Get deliveryStart
     *
     * @return \DateTime
     */
    public function getDeliveryStart() {
        return $this->deliveryStart;
    }

    /**
     * Set priceHT
     *
     * @param float $priceHT
     *
     * @return Product
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
     * Set recommendedPriceTTC
     *
     * @param float $recommendedPriceTTC
     *
     * @return Product
     */
    public function setRecommendedPriceTTC($recommendedPriceTTC) {
        $this->recommendedPriceTTC = $recommendedPriceTTC;

        return $this;
    }

    /**
     * Get recommendedPriceTTC
     *
     * @return float
     */
    public function getRecommendedPriceTTC() {
        return $this->recommendedPriceTTC;
    }

    /**
     * Set material
     *
     * @param string $material
     *
     * @return Product
     */
    public function setMaterial($material) {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return string
     */
    public function getMaterial() {
        return $this->material;
    }

    /**
     * Set maintenance
     *
     * @param string $maintenance
     *
     * @return Product
     */
    public function setMaintenance($maintenance) {
        $this->maintenance = $maintenance;

        return $this;
    }

    /**
     * Get maintenance
     *
     * @return string
     */
    public function getMaintenance() {
        return $this->maintenance;
    }

    /**
     * Set dimensions
     *
     * @param string $dimensions
     *
     * @return Product
     */
    public function setDimensions($dimensions) {
        $this->dimensions = $dimensions;

        return $this;
    }

    /**
     * Get dimensions
     *
     * @return string
     */
    public function getDimensions() {
        return $this->dimensions;
    }


    /**
     * Set pack
     *
     * @param string $pack
     *
     * @return Product
     */
    public function setPack($pack) {
        $this->pack = $pack;

        return $this;
    }

    /**
     * Get pack
     *
     * @return string
     */
    public function getPack() {
        return $this->pack;
    }

    /**
     * Set primaryCat
     *
     * @param PrimaryCategory $primaryCat
     *
     * @return Product
     */
    public function setPrimaryCat(PrimaryCategory $primaryCat) {
        $this->primaryCat = $primaryCat;

        return $this;
    }

    /**
     * Get primaryCat
     *
     * @return PrimaryCategory
     */
    public function getPrimaryCat() {
        return $this->primaryCat;
    }

    /**
     * Set secondaryCat
     *
     * @param SecondaryCategory $secondaryCat
     *
     * @return Product
     */
    public function setSecondaryCat(SecondaryCategory $secondaryCat) {
        $this->secondaryCat = $secondaryCat;

        return $this;
    }

    /**
     * Get secondaryCat
     *
     * @return SecondaryCategory
     */
    public function getSecondaryCat() {
        return $this->secondaryCat;
    }

    /**
     * Set target
     *
     * @param Target $target
     *
     * @return Product
     */
    public function setTarget(Target $target) {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return Target
     */
    public function getTarget() {
        return $this->target;
    }

    /**
     * Get brand
     *
     * @return \B2bBundle\Entity\Brand
     */
    public function getBrand() {
        return $this->collection->getBrand();
    }

    /**
     * Set country
     *
     * @param Country $country
     *
     * @return Product
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
     * Set collection
     *
     * @param Collection $collection
     *
     * @return Product
     */
    public function setCollection(Collection $collection) {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return Collection
     */
    public function getCollection() {
        return $this->collection;
    }


    /**
     * Add allowed color
     *
     * @param AllowedColor $allowedColor
     *
     * @return Product
     */
    public function addAllowedColor(AllowedColor $allowedColor) {
        $allowedColor->setProduct($this);
        $this->allowedColors[] = $allowedColor;

        $this->addAvailabilitiesForColor($allowedColor);

        return $this;
    }


    /**
     * Remove allowed color
     *
     * @param AllowedColor $allowedColor
     */
    public function removeAllowedColor(AllowedColor $allowedColor) {
        $this->allowedColors->removeElement($allowedColor);
        foreach ($this->availabilities as $availability) {
            if ($availability->getColor()->getId() == $allowedColor->getColor()->getId()) {
                $this->availabilities->removeElement($availability);
                break;
            }
        }
    }

    /**
     * Get allowed colors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllowedColors() {
        return $this->allowedColors;
    }

    /**
     * Add allowed size
     *
     * @param AllowedSize $allowedSize
     *
     * @return Product
     */
    public function addAllowedSize(AllowedSize $allowedSize) {
        $allowedSize->setProduct($this);
        $this->allowedSizes[] = $allowedSize;

        $this->addAvailabilitiesForSize($allowedSize->getSize());

        return $this;
    }

    public function addAllowedSizeFromSize(Size $size){
        $allowedSize = new AllowedSize();
        $allowedSize->setSize($size);
        return $this->addAllowedSize($allowedSize);
    }

    public function addAllowedColorFromColor(ColorProduct $color){
        $allowedColor = new AllowedColor();
        $allowedColor->setColor($color);
        return $this->addAllowedColor($allowedColor);
    }

    /**
     * Remove allowed size
     *
     * @param AllowedSize $allowedSize
     */
    public function removeAllowedSize(AllowedSize $allowedSize) {
        $this->allowedSizes->removeElement($allowedSize);
        foreach ($this->availabilities as $availability) {
            $availability->removeSizeQuantity($allowedSize->getSize());
        }
    }

    /**
     * Get allowed sizes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllowedSizes() {
        return $this->allowedSizes;
    }

    public function getIndexOfSize($size) {
        foreach($this->allowedSizes as $s) {
            if ($s->getSize() == $size) {
                return $this->allowedSizes->indexOf($s);
            }
        }
        return -1;
    }

    private function addAvailabilitiesForSize(Size $size) {
        foreach ($this->availabilities as $availability) {
            $availability->addQuantity($size);
        }
    }

    private function addAvailabilitiesForColor(AllowedColor $color) {
        $availability = new Availability($this, $color->getColor(), $this->allowedSizes, '');
        $this->addAvailability($availability);
    }

    /**
     * Add availability
     *
     * @param Availability $availability
     *
     * @return Product
     */
    public function addAvailability(Availability $availability) {
        $this->availabilities[] = $availability;

        return $this;
    }

    /**
     * Remove availability
     *
     * @param Availability $availability
     */
    public function removeAvailability(Availability $availability) {
        $this->availabilities->removeElement($availability);
    }

    /**
     * Get availabilities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvailabilities() {
        return $this->availabilities;
    }

    /**
     * Get first availability with positive quantity
     *
     * @return Availability | null
     */
    public function getFirstAvailability() {
        foreach ($this->availabilities as $availability) {
            if ($availability->hasQuantity()) {
                return $availability;
            }
        }
        return null;
    }

    public function hasStock(){
        foreach($this->availabilities as $availability){
            if ($availability->hasQuantity()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->allowedColors = new ArrayCollection();
        $this->allowedSizes = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * Set refModel
     *
     * @param string $refModel
     * @return Product
     */
    public function setRefModel($refModel) {
        $this->refModel = $refModel;

        return $this;
    }

    /**
     * Get refModel
     *
     * @return string
     */
    public function getRefModel() {
        return $this->refModel;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Product
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getMainPicture()
    {
        return $this->mainpicture;
    }

    /**
     * @param mixed $mainpicture
     */
    public function setMainPicture($mainpicture)
    {
        $this->mainpicture = $mainpicture;
    }


}
