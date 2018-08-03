<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * CartCategory
 *
 * @ORM\Table(name="cart_category")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\CartCategoryRepository")
 */
class CartCategory implements \JsonSerializable
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrimaryCategory")
     * @ORM\JoinColumn(name="primary_category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="CartCollection", inversedBy="cartCategories")
     * @ORM\JoinColumn(name="cart_collection_id", referencedColumnName="id")
     */
    private $cartCollection;

    /**
     * @ORM\OneToMany(targetEntity="B2bBundle\Entity\CartRow", cascade={"persist", "remove"}, mappedBy="cartCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cartRows;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Size", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"rank" = "ASC"})
     */
    private $sizes;

    /**
     * @var float
     *
     * @ORM\Column(name="priceHT", type="float", nullable=true)
     */
    private $priceCategory;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param PrimaryCategory $category
     *
     * @return CartCategory
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return PrimaryCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set cart collection
     *
     * @param Cartcollection $cartCollection
     *
     * @return CartCategory
     */
    public function setCartCollection($cartCollection)
    {
        $this->cartCollection = $cartCollection;

        return $this;
    }

    /**
     * Get cart collection
     *
     * @return CartCollection
     */
    public function getCartCollection()
    {
        return $this->cartCollection;
    }

    /**
     * Add cart row
     *
     * @param \B2bBundle\Entity\CartRow $cartRow
     *
     * @return CartCategory
     */
    public function addCartRow(\B2bBundle\Entity\CartRow $cartRow)
    {
        $cartRow->setCartCategory($this);
        $this->cartRows[] = $cartRow;
        $this->cartCollection->addProducts(1);

        foreach ($cartRow->getSizeQuantities() as $sizeQuantity) {
            if (!$this->sizes->contains($sizeQuantity->getSize())) {
                $this->sizes[] = $sizeQuantity->getSize();
            }
        }
        return $this;
    }

    /**
     * Remove cart row
     *
     * @param \B2bBundle\Entity\CartRow $cartRow
     */
    public function removeCartRow(\B2bBundle\Entity\CartRow $cartRow)
    {
        $this->cartRows->removeElement($cartRow);
        $this->cartCollection->removeProducts(1);

        // TODO : remove sizes from sizes
    }

    /**
     * Get cart rows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartRows()
    {
        return $this->cartRows;
    }

    /**
     * Get cart rows
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    public function update(ManagerRegistry $em)
    {
        $count = 0;
        foreach ($this->cartRows as $cartRow) {
            if (!$cartRow->hasQuantity()) {
                $this->removeCartRow($cartRow);
                $em->getManager()->remove($cartRow);
            } else {
                $count += 1;
            }
        }
        return $count;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->cartRows as $cartRow) {
            $total += $cartRow->getTotal();
        }
        return $total;
    }

    public function __construct()
    {
        $this->cartRows = new ArrayCollection();
        $this->sizes = new ArrayCollection();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return array(
            'name' => $this->category,
            'rows' => $this->cartRows,
            'sizes' => $this->sizes
        );
    }

    /**
     * Add size.
     *
     * @param \B2bBundle\Entity\Size $size
     *
     * @return CartCategory
     */
    public function addSize(\B2bBundle\Entity\Size $size)
    {
        $this->sizes[] = $size;

        return $this;
    }

    /**
     * Remove size.
     *
     * @param \B2bBundle\Entity\Size $size
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSize(\B2bBundle\Entity\Size $size)
    {
        return $this->sizes->removeElement($size);
    }

    /**
     * @return float
     */
    public function getPriceCategory()
    {
        return $this->priceCategory;
    }

    /**
     * @param float $priceCategory
     */
    public function setPriceCategory($priceCategory)
    {
        $this->priceCategory = $priceCategory;
    }


}
