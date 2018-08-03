<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Collection
 *
 * @ORM\Table(name="collection")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CollectionRepository")
 */
class Collection {
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Season")
     * @ORM\JoinColumn(nullable=false)
     */
    private $season;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"persist"})
     */
    private $lifestyle;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove","persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $lookbook;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;


    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Brand", inversedBy="collections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Product", mappedBy="collection")
     */
    private $products;

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
     * Set name
     *
     * @param string $name
     *
     * @return Collection
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
     * Set year
     *
     * @param integer $year
     *
     * @return Collection
     */
    public function setYear($year) {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear() {
        return $this->year;
    }

    /**
     * Set season
     *
     * @param Season $season
     *
     * @return Collection
     */
    public function setSeason($season) {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return Season
     */
    public function getSeason() {
        return $this->season;
    }

    /**
     * Set lifestyle
     *
     * @param string $lifestyle
     * @return Collection
     */
    public function setLifestyle($lifestyle) {
        $this->lifestyle = $lifestyle;

        return $this;
    }

    /**
     * Get lifestyle
     *
     * @return string
     */
    public function getLifestyle() {
        return $this->lifestyle;
    }


    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Collection
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * Set lookbook
     *
     * @param string $lookbook
     *
     * @return Collection
     */
    public function setLookbook($lookbook) {
        $this->lookbook = $lookbook;

        return $this;
    }

    /**
     * Get lookbook
     *
     * @return string
     */
    public function getLookbook() {
        return $this->lookbook;
    }

    /**
     * Set brand
     *
     * @param Brand $brand
     *
     * @return Collection
     */
    public function setBrand($brand) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get lookbook
     *
     * @return string
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Add product
     *
     * @param \B2bBundle\Entity\Product $product
     *
     * @return Collection
     */
    public function addProduct(\B2bBundle\Entity\Product $product) {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \B2bBundle\Entity\Product $product
     */
    public function removeProduct(\B2bBundle\Entity\Product $product) {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
    }
}
