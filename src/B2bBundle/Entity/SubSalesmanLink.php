<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Salesman
 *
 * @ORM\Table(name="sub_salesman_link")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SubSalesmanLinkRepository")
 */
class SubSalesmanLink {

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
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Salesman", cascade={"persist"}, inversedBy="subordinates")
     */
    private $superior;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Salesman", cascade={"persist"}, inversedBy="superiors")
     */
    private $subordinate;

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
     * @return SubSalesmanLink
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
     * Set superior
     *
     * @param Salesman $superior
     * @return SubSalesmanLink
     */
    public function setSuperior(Salesman $superior) {
        $this->superior = $superior;

        return $this;
    }

    /**
     * Get superior
     *
     * @return Salesman
     */
    public function getSuperior() {
        return $this->superior;
    }

    /**
     * Set subordinate
     *
     * @param Salesman $subordinate
     * @return SubSalesmanLink
     */
    public function setSubordinate(Salesman $subordinate) {
        $this->subordinate = $subordinate;

        return $this;
    }

    /**
     * Get subordinate
     *
     * @return Salesman
     */
    public function getSubordinate() {
        return $this->subordinate;
    }

    /**
     * Add department
     *
     * @param Departement $department
     * @return SubSalesmanLink
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
