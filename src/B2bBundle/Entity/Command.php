<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\Invoice;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Command
 *
 * @ORM\Table(name="command")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CommandRepository")
 */
class Command {

    const STATUS_CREATED   = 'created';
    const STATUS_NOT_VALIDATED = 'not-validated';
    const STATUS_VALIDATED   = 'validated';
    const STATUS_CANCELED   = 'canceled';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="totalHT", type="float")
     */
    private $totalHT;

    /**
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\CartCollection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cartCollection;

    /**
     * @var string
     * @ORM\Column(name="societyName", type="string", length=255)
     */
    private $societyName;

    /**
     * @var string
     *
     * @ORM\Column(name="numTVA", type="string", length=255, nullable=true)
     */
    private $numTVA;

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
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Shop")
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryAddress", type="string", length=255, nullable=true)
     */
    private $deliveryAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryZP", type="string", length=255, nullable=true)
     */
    private $deliveryZP;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryTown", type="string", length=255, nullable=true)
     */
    private $deliveryTown;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(nullable=true)
     */
    private $paymentMethod;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\PaymentTerms")
     * @ORM\JoinColumn(nullable=true)
     */
    private $paymentTerms;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\Invoice", mappedBy="command")
     * @ORM\JoinColumn(nullable=true)
     */
    private $invoices;

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
     * @return Command
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
     * Set status
     *
     * @param string $status
     *
     * @return Command
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
     * Set totalHT
     *
     * @param float $totalHT
     *
     * @return Command
     */
    public function setTotalHT($totalHT) {
        $this->totalHT = $totalHT;

        return $this;
    }

    /**
     * Get totalHT
     *
     * @return float
     */
    public function getTotalHT() {
        return $this->totalHT;
    }

    /**
     * Update totalHT with products in cart
     *
     * @return $this
     */
    public function updateTotalHT() {
        $this->totalHT = 0.;

        foreach ($this->cartCollection->getCartCategories() as $cartCategory) {
            foreach ($cartCategory->getCartRows() as $cartRow) {
                $qt = 0;
                foreach ($cartRow->getSizeQuantities() as $sizeQuantity) {
                    $qt += $sizeQuantity->getAmount();
                }
                $this->totalHT += $qt * $cartRow->getPriceHT();
            }
        }
        return $this;
    }

    /**
     * Set Cart Collection
     *
     * @param CartCollection $cartCollection
     *
     * @return Command
     */
    public function setCartCollection($cartCollection) {
        $this->totalHT = 0.;
        $this->cartCollection = $cartCollection;
        //$cartCollection->setCommand($this);
        $this->setSocietyName($cartCollection->getCustomer()->getCompanyName());
        $this->setNumTVA($cartCollection->getCustomer()->getNumTVA());
        $this->setBillingAddress($cartCollection->getCustomer()->getBillingAddress());
        $this->setBillingZP($cartCollection->getCustomer()->getBillingZP());
        $this->setBillingTown($cartCollection->getCustomer()->getBillingTown());

        return $this;
    }

    /**
     * Get Cart Collection
     *
     * @return Cart Collection
     */
    public function getCartCollection() {
        return $this->cartCollection;
    }

    /**
     * Set societyName
     *
     * @param string $societyName
     *
     * @return Command
     */
    public function setSocietyName($societyName) {
        $this->societyName = $societyName;

        return $this;
    }

    /**
     * Get societyName
     *
     * @return string
     */
    public function getSocietyName() {
        return $this->societyName;
    }

    /**
     * Set numTVA
     *
     * @param string $numTVA
     *
     * @return Command
     */
    public function setNumTVA($numTVA) {
        $this->numTVA = $numTVA;

        return $this;
    }

    /**
     * Get numTVA
     *
     * @return string
     */
    public function getNumTVA() {
        return $this->numTVA;
    }

    /**
     * Set billingAddress
     *
     * @param string $billingAddress
     *
     * @return Command
     */
    public function setBillingAddress($billingAddress) {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return string
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * Set billingZP
     *
     * @param string $billingZP
     *
     * @return Command
     */
    public function setBillingZP($billingZP) {
        $this->billingZP = $billingZP;

        return $this;
    }

    /**
     * Get billingZP
     *
     * @return string
     */
    public function getBillingZP() {
        return $this->billingZP;
    }

    /**
     * Set billingTown
     *
     * @param string $billingTown
     *
     * @return Command
     */
    public function setBillingTown($billingTown) {
        $this->billingTown = $billingTown;

        return $this;
    }

    /**
     * Get billingTown
     *
     * @return string
     */
    public function getBillingTown() {
        return $this->billingTown;
    }


    /**
     * Set shop
     *
     * @param Shop $shop
     * @return $this
     */
    public function setShop(\B2bBundle\Entity\Shop $shop) {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     * @return \B2bBundle\Entity\Shop
     */
    public function getShop() {
        return $this->shop;
    }

    /**
     * Set deliveryAddress
     *
     * @param string $deliveryAddress
     *
     * @return Command
     */
    public function setDeliveryAddress($deliveryAddress) {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * Get deliveryAddress
     *
     * @return string
     */
    public function getDeliveryAddress() {
        return $this->deliveryAddress;
    }

    /**
     * Set deliveryZP
     *
     * @param string $deliveryZP
     *
     * @return Command
     */
    public function setDeliveryZP($deliveryZP) {
        $this->deliveryZP = $deliveryZP;

        return $this;
    }

    /**
     * Get deliveryZP
     *
     * @return string
     */
    public function getDeliveryZP() {
        return $this->deliveryZP;
    }

    /**
     * Set deliveryTown
     *
     * @param string $deliveryTown
     *
     * @return Command
     */
    public function setDeliveryTown($deliveryTown) {
        $this->deliveryTown = $deliveryTown;

        return $this;
    }

    /**
     * Get deliveryTown
     *
     * @return string
     */
    public function getDeliveryTown() {
        return $this->deliveryTown;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Command
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Command
     */
    public function setComment($comment) {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Set payment method
     *
     * @param PaymentMethod $paymentMethod
     * @return $this
     */
    public function setPaymentMethod(\B2bBundle\Entity\PaymentMethod $paymentMethod) {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get payment method
     * @return \B2bBundle\Entity\PaymentMethod
     */
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * Set payment terms
     *
     * @param PaymentTerms $paymentTerms
     * @return $this
     */
    public function setPaymentTerms(\B2bBundle\Entity\PaymentTerms $paymentTerms) {
        $this->paymentTerms = $paymentTerms;

        return $this;
    }

    /**
     * Get payment terms
     * @return \B2bBundle\Entity\PaymentTerms
     */
    public function getPaymentTerms() {
        return $this->paymentTerms;
    }

    /**
     * Add invoice
     *
     * @param Invoice $invoice
     * @return Command
     */
    public function addInvoice(Invoice $invoice) {
        $this->invoices[] = $invoice;
        return $this;
    }

    /**
     * Remove invoice
     *
     * @param Invoice $invoice
     */
    public function removeInvoice(Invoice $invoice) {
        $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoicess() {
        return $this->invoices;
    }

    public function copy(Command $copy) {
        $this->setDate($copy->getDate());
        $this->setStatus($copy->getStatus());
        $this->setSocietyName($copy->getSocietyName());
        $this->setNumTVA($copy->getNumTVA());
        $this->setBillingAddress($copy->getBillingAddress());
        $this->setBillingTown($copy->getBillingTown());
        $this->setBillingZP($copy->getBillingZP());
        $this->setShop($copy->getShop());
        $this->setDeliveryAddress($copy->getDeliveryAddress());
        $this->setDeliveryTown($copy->getDeliveryTown());
        $this->setDeliveryZP($copy->getDeliveryZP());
        $this->setPhone($copy->getPhone());
        $this->setPaymentMethod($copy->getPaymentMethod());
        $this->setPaymentTerms($copy->getPaymentTerms());
    }

    public function __construct() {
        $this->date = new \DateTime("now");
        $this->invoices = new ArrayCollection();
    }

    /**
     * Get invoices.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }
}
