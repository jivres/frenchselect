<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BrandUniversCalcul
 *
 * @ORM\Table(name="brand_univers_calcul", indexes={@ORM\Index(name="brand_id", columns={"brand_id"}), @ORM\Index(name="univers_id", columns={"univers_id"})})
 * @ORM\Entity
 */
class BrandUniversCalcul
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="poids", type="integer", nullable=false)
     */
    private $poids;

    /**
     * @var \Univers
     *
     * @ORM\ManyToOne(targetEntity="Univers",cascade={"persist"})
     */
    private $univers;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand", cascade={"persist"})
     */
    private $brand;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set poids.
     *
     * @param int $poids
     *
     * @return BrandUniversCalcul
     */
    public function setPoids($poids)
    {
        $this->poids = $poids;

        return $this;
    }

    /**
     * Get poids.
     *
     * @return int
     */
    public function getPoids()
    {
        return $this->poids;
    }

    /**
     * Set univers.
     *
     * @param \B2bBundle\Entity\Univers|null $univers
     *
     * @return BrandUniversCalcul
     */
    public function setUnivers(\B2bBundle\Entity\Univers $univers = null)
    {
        $this->univers = $univers;

        return $this;
    }

    /**
     * Get univers.
     *
     * @return \B2bBundle\Entity\Univers|null
     */
    public function getUnivers()
    {
        return $this->univers;
    }

    /**
     * Set brand.
     *
     * @param \B2bBundle\Entity\Brand|null $brand
     *
     * @return BrandUniversCalcul
     */
    public function setBrand(\B2bBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return \B2bBundle\Entity\Brand|null
     */
    public function getBrand()
    {
        return $this->brand;
    }
}
