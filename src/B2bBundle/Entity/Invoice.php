<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\InvoiceRepository")
 */
class Invoice {

    const STATUS_PAID         = 'paid';
    const STATUS_WAITING      = 'waiting';
    const STATUS_VERIFICATION = 'verification';
    const STATUS_REMINDER_1   = 'reminder_1';
    const STATUS_REMINDER_2   = 'reminder_2';
    const STATUS_REMINDER_3   = 'reminder_3';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="num", type="integer")
     */
    private $num;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime")
     */
    private $dueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="remindDate", type="datetime")
     * @ORM\JoinColumn(nullable=true)
     */
    private $remindDate;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Command", cascade={"persist"}, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=true)
     */
    private $command;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\PaymentMethod", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $paymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Payment", cascade={"persist"}, mappedBy="invoice")
     * @ORM\JoinColumn(nullable=true)
     */
    private $payments;

    /**
     * @var float
     *
     * @ORM\Column(name="reduction", type="float")
     */
    private $reduction;

    /**
     * @var float
     *
     * @ORM\Column(name="shippingCosts", type="float")
     */
    private $shippingCosts;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set num
     *
     * @param int $num
     * @return Invoice
     */
    public function setNum($num) {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return int
     */
    public function getNum() {
        return $this->num;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Invoice
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set due date
     *
     * @param \DateTime $dueDate
     *
     * @return Invoice
     */
    public function setDueDate($dueDate) {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get due date
     *
     * @return \DateTime
     */
    public function getDueDate() {
        return $this->dueDate;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Invoice
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set remind date
     *
     * @param \DateTime $remindDate
     *
     * @return Invoice
     */
    public function setRemindDate($remindDate) {
        $this->remindDate = $remindDate;

        return $this;
    }

    /**
     * Get remind date
     *
     * @return \DateTime
     */
    public function getRemindDate() {
        return $this->remindDate;
    }

    /**
     * Set customer
     *
     * @param \B2bBundle\Entity\Customer $customer
     *
     * @return Invoice
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \B2bBundle\Entity\Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Set command
     *
     * @param \B2bBundle\Entity\Command $command
     *
     * @return Invoice
     */
    public function setCommand($command) {
        $this->command = $command;
        $this->command->addInvoice($this);

        return $this;
    }

    /**
     * Get command
     *
     * @return \B2bBundle\Entity\Command
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * Add payment method
     * @param PaymentMethod $paymentMethod
     *
     * @return Invoice
     */
    public function addPaymentMethod(PaymentMethod $paymentMethod) {
        $this->paymentMethods[] = $paymentMethod;
        return $this;
    }

    /**
     * Remove payment method
     *
     * @param PaymentMethod $paymentMethod
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod) {
        $this->paymentMethods->removeElement($paymentMethod);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentMethods() {
        return $this->paymentMethods;
    }

    /**
     * Add payment
     * @param Payment $payment
     *
     * @return Invoice
     */
    public function addPayment(Payment $payment) {
        $this->payments[] = $payment;
        return $this;
    }

    /**
     * Remove payment
     *
     * @param Payment $payment
     */
    public function removePayment(Payment $payment) {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments() {
        return $this->payments;
    }

    /**
     * Set reduction
     *
     * @param float $reduction
     * @return Invoice
     */
    public function setReduction($reduction) {
        $this->reduction = $reduction;
        return $this;
    }

    /**
     * Get reduction
     * @return float
     */
    public function getReduction() {
        return $this->reduction;
    }

    /**
     * Set shipping costs
     *
     * @param float $shippingCosts
     * @return Invoice
     */
    public function setShippingCosts($shippingCosts) {
        $this->shippingCosts = $shippingCosts;
        return $this;
    }

    /**
     * Get shipping costs
     * @return float
     */
    public function getShippingCosts() {
        return $this->shippingCosts;
    }

    public function __construct() {
        $this->date           = new \DateTime("now");
        $this->remindDate     = new \DateTime("now");
        $this->num            = $this->id;
        $this->paymentMethods = new ArrayCollection();
        $this->payments       = new ArrayCollection();
        $this->reduction      = 0;
        $this->num            = $this->id;
        $this->shippingCosts  = 0;
    }
}
