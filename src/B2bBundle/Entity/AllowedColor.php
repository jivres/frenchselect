<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * AllowedColor
 *
 * @ORM\Table(name="allowed_color")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\AllowedColorRepository")
 */
class AllowedColor {
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
     * @ORM\Column(name="refUnique", type="string", length=50, unique=true, nullable=true)
     */
    private $refUnique;

    /**
     * @var string
     *
     * @ORM\Column(name="eanCode", type="string", length=13, nullable=true)
     */
    private $eanCode;

    /**
     * @var string
     *
     * @ORM\Column(name="colorCode", type="string", length=32, nullable=true)
     */
    private $colorCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="favourite", type="boolean", nullable=true)
     */
    private $favourite;

    /**
     * @var float
     *
     * @ORM\Column(name="reduction", type="float", nullable=true)
     */
    private $reduction;

    /**
    * @var string
    *
    * @ORM\Column(name="additionalInformation", type="string", length=255, nullable=true)
    */
    private $additionalInformation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deliveryStart", type="datetime")
     */
    private $deliveryStart;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Product", cascade={"persist"}, inversedBy="allowedColors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\ColorProduct", cascade={"persist"}, inversedBy="allowedColors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;


    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\MyFile", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $figures;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set color
     *
     * @param ColorProduct $color
     * @return AllowedColor
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

    /**
     * Set product
     *
     * @param Product $product
     * @return AllowedColor
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
     * Add figure
     *
     * @param \B2bBundle\Entity\MyFile $figure
     * @return AllowedColor
     */
    public function addFigure(MyFile $figure) {
        $this->figures[] = $figure;

        return $this;
    }

    /**
     * Remove figure
     *
     * @param \B2bBundle\Entity\MyFile $figure
     */
    public function removeFigure(MyFile $figure) {
        $this->figures->removeElement($figure);
    }

    /**
     * Get figures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFigures() {
        return $this->figures;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->figures = new ArrayCollection();
    }

    /**
     * Set refUnique
     *
     * @param string $refUnique
     * @return AllowedColor
     */
    public function setRefUnique($refUnique) {
        $this->refUnique = $refUnique;

        return $this;
    }

    /**
     * Get refUnique
     *
     * @return string
     */
    public function getRefUnique() {
        return $this->refUnique;
    }

    /**
     * Set eanCode
     *
     * @param string $eanCode
     * @return AllowedColor
     */
    public function setEanCode($eanCode) {
        $this->eanCode = $eanCode;

        return $this;
    }

    /**
     * Get eanCode
     *
     * @return string
     */
    public function getEanCode() {
        return $this->eanCode;
    }

    /**
     * Set colorCode
     *
     * @param string $colorCode
     * @return AllowedColor
     */
    public function setColorCode($colorCode) {
        $this->colorCode = $colorCode;

        return $this;
    }

    /**
     * Get colorCode
     *
     * @return string
     */
    public function getColorCode() {
        return $this->colorCode;
    }

    /**
     * Set favourite
     *
     * @param boolean $favourite
     * @return AllowedColor
     */
    public function setFavourite($favourite) {
        $this->favourite = $favourite;

        return $this;
    }

    /**
     * Get favourite
     *
     * @return boolean
     */
    public function getFavourite() {
        return $this->favourite;
    }

    /**
     * Set additionalInformation
     *
     * @param string $additionalInformation
     * @return AllowedColor
     */
    public function setAdditionalInformation($additionalInformation) {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    /**
     * Get additionalInformation
     *
     * @return string
     */
    public function getAdditionalInformation() {
        return $this->additionalInformation;
    }

    /**
     * Set reduction
     *
     * @param float $reduction
     * @return AllowedColor
     */
    public function setReduction($reduction) {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * Get reduction
     *
     * @return float
     */
    public function getReduction() {
        return $this->reduction;
    }

    /**
     * Set deliveryStart
     *
     * @param \DateTime $deliveryStart
     * @return AllowedColor
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
     * Set Printed
     *
     * @param UploadedFile $printed
     *
     * @return ColorProduct
     */
    public function setPrinted($printed)
    {
        $this->printed = $printed;

        return $this;
    }

    /**
     * Get printed
     *
     * @return MyFile
     */
    public function getPrinted()
    {
        return $this->printed;
    }
}
