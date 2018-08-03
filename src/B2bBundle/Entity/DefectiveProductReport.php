<?php

namespace B2bBundle\Entity;

use B2bBundle\Entity\DefectiveProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DefectiveProductReport
 *
 * @ORM\Table(name="defective_product_report")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\DefectiveProductReportRepository")
 */
class DefectiveProductReport {

    const STATUS_WAITING   = 'waiting';
    const STATUS_ONGOING   = 'ongoing';
    const STATUS_RETURNING = 'returning';
    const STATUS_HANDLED   = 'handled';
    const STATUS_REFUSED   = 'refused';

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
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\Command", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $command;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\DefectiveProduct", cascade={"persist"}, mappedBy="report")
     * @ORM\JoinColumn(nullable=false)
     */
    private $defectiveProducts;

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
     * @return DefectiveProductReport
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
     * @return DefectiveProductReport
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
     * Set command
     *
     * @param \B2bBundle\Entity\Command $command
     *
     * @return DefectiveProductReport
     */
    public function setCommand($command) {
        $this->command = $command;

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
     * Create a defective product if not already exists for this report
     * @param Product $product
     * @param Size $size
     * @param ColorProduct $color
     */
    public function addDefectiveProductIfNotExists(Product $product, Size $size, ColorProduct $color) {
        foreach ($this->defectiveProducts as $defectiveProduct) {
            if ($defectiveProduct->getProduct() == $product &&
                $defectiveProduct->getSize()    == $size &&
                $defectiveProduct->getColor()   == $color)
            return;
        }
        $defectiveProduct = new DefectiveProduct();
        $defectiveProduct->setSize($size);
        $defectiveProduct->setColor($color);
        $defectiveProduct->setProduct($product);
        $defectiveProduct->setQuantity(0);
        $this->addDefectiveProduct($defectiveProduct);
    }

    /**
     * Add defective product
     *
     * @param \B2bBundle\Entity\DefectiveProduct $defectiveProduct
     * @return DefectiveProductReport
     */
    public function addDefectiveProduct(DefectiveProduct $defectiveProduct) {
        $defectiveProduct->setReport($this);
        $this->defectiveProducts[] = $defectiveProduct;

        return $this;
    }

    /**
     * Remove defective product
     *
     * @param \B2bBundle\Entity\DefectiveProduct $defectiveProduct
     */
    public function removeDefectiveProduct(DefectiveProduct $defectiveProduct) {
        $this->defectiveProducts->removeElement($defectiveProduct);
    }

    /**
     * Get defective products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefectiveProducts() {
        return $this->defectiveProducts;
    }

    public function __construct() {
        $this->date = new \DateTime("now");
        $this->setStatus(DefectiveProductReport::STATUS_WAITING);
        $this->defectiveProducts = new ArrayCollection();
    }
}
