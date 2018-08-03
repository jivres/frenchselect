<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DefectiveProduct
 *
 * @ORM\Table(name="defective_product")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\DefectiveProductRepository")
 */
class DefectiveProduct {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DefectiveProductReport", inversedBy="defectiveProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $report;


    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Product", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\ColorProduct")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Size")
     * @ORM\JoinColumn(nullable=false)
     */
    private $size;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\MyFile", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $pictures;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $comment;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get report
     *
     * @return DefectiveProductReport
     */
    public function getReport() {
        return $this->report;
    }

    /**
     * Set report
     *
     * @param DefectiveProductReport $report
     *
     * @return DefectiveProduct
     */
    public function setReport($report) {
        $this->report = $report;

        return $this;
    }

    /**
     * Set size
     *
     * @param $product
     * @return DefectiveProduct
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
     * Set color
     *
     * @param $color
     * @return DefectiveProduct
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
     * Set size
     *
     * @param Size $size
     *
     * @return DefectiveProduct
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return Size
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return DefectiveProduct
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Add picture
     *
     * @param MyFile $picture
     *
     * @return DefectiveProduct
     */
    public function addPicture(MyFile $picture) {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove defective product
     *
     * @param $picture
     * @return DefectiveProduct
     */
    public function removePicture(MyFile $picture) {
        $this->pictures->removeElement($picture);

        return $this;
    }

    /**
     * Get pictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures() {
        return $this->pictures;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return DefectiveProduct
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
     * Constructor
     */
    public function __construct() {
        $this->pictures = new ArrayCollection();
    }
}
