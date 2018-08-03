<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Access
 *
 * @ORM\Table(name="access")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\AccessRepository")
 */
class Access {
    const STATUS_CREATED   = 'created';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_HANDLED   = 'handled';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowed", type="boolean")
     */
    private $allowed;

    /**
     * @var enum
     *
     * @ORM\Column(name="state", type="string", columnDefinition="enum('created', 'submitted', 'handled')")
     */
    private $state;

    /**
     * @var string
     * Motif de la demande
     *
     * @ORM\Column(name="motive", type="text")
     */
    private $motive;

    /**
     * @var string
     * Raison de l'acceptation/refus de la demande
     *
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", inversedBy="accesses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Brand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set state as submitted
     *
     * @return $this
     */
    public function setSubmitted() {
        $this->state = Access::STATUS_SUBMITTED;

        return $this;
    }

    /**
     * Set state as handled
     *
     * @return $this
     */
    public function setHandled() {
        $this->state = Access::STATUS_HANDLED;

        return $this;
    }

    /**
     * Check if created
     *
     * @return bool
     */
    public function isCreated() {
        return $this->state == Access::STATUS_CREATED;
    }

    /**
     * Check if submitted
     *
     * @return bool
     */
    public function isSubmitted() {
        return $this->state == Access::STATUS_SUBMITTED;
    }

    /**
     * Check if handled
     *
     * @return bool
     */
    public function isHandled() {
        return $this->state == Access::STATUS_HANDLED;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Access
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
     * Set allowed
     *
     * @param boolean $allowed
     *
     * @return Access
     */
    public function setAllowed($allowed) {
        $this->allowed = $allowed;

        return $this;
    }

    /**
     * Get allowed
     *
     * @return bool
     */
    public function getAllowed() {
        return $this->allowed;
    }

    /**
     * Set motive
     *
     * @param string $motive
     * @return Access
     */
    public function setMotive($motive) {
        $this->motive = $motive;

        return $this;
    }

    /**
     * Get motive
     *
     * @return string
     */
    public function getMotive() {
        return $this->motive;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return Access
     */
    public function setReason($reason) {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason() {
        return $this->reason;
    }

    /**
     * Set brand
     *
     * @param Brand $brand
     * @return $this
     */
    public function setBrand(Brand $brand) {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Brand
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     * @return $this
     */
    public function setCustomer(Customer $customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    public function __construct(Brand $brand, Customer $customer) {
        $this->brand = $brand;
        $this->customer = $customer;
        $this->allowed = false;
        $this->state = Access::STATUS_CREATED;
        $this->reason = '';
        $this->motive = '';
    }
}
