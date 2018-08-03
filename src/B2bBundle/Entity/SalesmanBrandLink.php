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
 * @ORM\Table(name="salesman_brand_link")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SalesmanBrandLinkRepository")
 */
class SalesmanBrandLink {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Brand", cascade={"persist"}, inversedBy="salesmen")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Salesman", cascade={"persist"}, inversedBy="brands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salesman;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Departement", cascade={"persist"})
     */
    private $departments;

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
     * @return SalesmanBrandLink
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
     * Set salesman
     *
     * @param Salesman $salesman
     * @return SalesmanBrandLink
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
     * Add department
     *
     * @param Departement $department
     *
     * @return SalesmanBrandLink
     */
    public function addDepartment(Departement $department) {
        $this->departments[] = $department;

        return $this;
    }

    /**
     * Remove department
     *
     * @param Departement $department
     */
    public function removeDepartment(Departement $department) {
        $this->departments->removeElement($department);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments() {
        return $this->departments;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->departments = new ArrayCollection();
    }
}
