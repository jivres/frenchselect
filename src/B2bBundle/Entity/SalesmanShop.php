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
 * @ORM\Table(name="salesman_shop")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SalesmanShopRepository")
 */
class SalesmanShop {

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
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Shop", cascade={"persist"}, inversedBy="salesmen")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shop;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Salesman", cascade={"persist"}, inversedBy="shops")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salesman;

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
     * @return SalesmanShop
     */
    public function setBrand(Brand $brand) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return Brand
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Set shop
     *
     * @param Shop $shop
     * @return SalesmanShop
     */
    public function setShop(Shop $shop) {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return Customer
     */
    public function getShop() {
        return $this->shop;
    }

    /**
     * Set salesman
     *
     * @param Salesman $salesman
     * @return SalesmanShop
     */
    public function setSalesman(Salesman $salesman) {
        $this->salesman = $salesman;

        return $this;
    }

    /**
     * Get salesman
     *
     * @return Salesman
     */
    public function getSalesman() {
        return $this->salesman;
    }

    /**
     * Constructor
     */
    public function __construct() {
    }
}
