<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\PaymentRepository")
 */
class Payment {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Invoice", cascade={"persist"}, inversedBy="payments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $invoice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payment
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
     * Set invoice
     *
     * @param Invoice $invoice
     * @return Payment
     */
    public function setInvoice($invoice) {
        $this->invoice = $invoice;
        $this->invoice->addPayment($this);

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \B2bBundle\Entity\Invoice
     */
    public function getInvoice() {
        return $this->invoice;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Payment
     */
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     * @return float
     */
    public function getAmount() {
        return $this->amount;
    }

    public function __construct() {
        $this->date = new \DateTime("now");
    }
}
