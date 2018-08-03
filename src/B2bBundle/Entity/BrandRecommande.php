<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BrandRecommande
 *
 * @ORM\Table(name="brand_recommande", indexes={@ORM\Index(name="recommande_homme", columns={"recommande_homme"}), @ORM\Index(name="recommande_femme", columns={"recommande_femme"}), @ORM\Index(name="recommande_enfant", columns={"recommande_enfant"}), @ORM\Index(name="brand_id", columns={"brand_id"})})
 * @ORM\Entity
 */
class BrandRecommande
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
     * @var int|null
     *
     * @ORM\Column(name="score_homme", type="integer", nullable=true)
     */
    private $scoreHomme;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score_femme", type="integer", nullable=true)
     */
    private $scoreFemme;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score_enfant", type="integer", nullable=true)
     */
    private $scoreEnfant;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     * })
     */
    private $brand;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recommande_homme", referencedColumnName="id")
     * })
     */
    private $recommandeHomme;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recommande_femme", referencedColumnName="id")
     * })
     */
    private $recommandeFemme;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recommande_enfant", referencedColumnName="id")
     * })
     */
    private $recommandeEnfant;



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
     * Set scoreHomme.
     *
     * @param int|null $scoreHomme
     *
     * @return BrandRecommande
     */
    public function setScoreHomme($scoreHomme = null)
    {
        $this->scoreHomme = $scoreHomme;

        return $this;
    }

    /**
     * Get scoreHomme.
     *
     * @return int|null
     */
    public function getScoreHomme()
    {
        return $this->scoreHomme;
    }

    /**
     * Set scoreFemme.
     *
     * @param int|null $scoreFemme
     *
     * @return BrandRecommande
     */
    public function setScoreFemme($scoreFemme = null)
    {
        $this->scoreFemme = $scoreFemme;

        return $this;
    }

    /**
     * Get scoreFemme.
     *
     * @return int|null
     */
    public function getScoreFemme()
    {
        return $this->scoreFemme;
    }

    /**
     * Set scoreEnfant.
     *
     * @param int|null $scoreEnfant
     *
     * @return BrandRecommande
     */
    public function setScoreEnfant($scoreEnfant = null)
    {
        $this->scoreEnfant = $scoreEnfant;

        return $this;
    }

    /**
     * Get scoreEnfant.
     *
     * @return int|null
     */
    public function getScoreEnfant()
    {
        return $this->scoreEnfant;
    }

    /**
     * Set brand.
     *
     * @param \B2bBundle\Entity\Brand|null $brand
     *
     * @return BrandRecommande
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

    /**
     * Set recommandeHomme.
     *
     * @param \B2bBundle\Entity\Brand|null $recommandeHomme
     *
     * @return BrandRecommande
     */
    public function setRecommandeHomme(\B2bBundle\Entity\Brand $recommandeHomme = null)
    {
        $this->recommandeHomme = $recommandeHomme;

        return $this;
    }

    /**
     * Get recommandeHomme.
     *
     * @return \B2bBundle\Entity\Brand|null
     */
    public function getRecommandeHomme()
    {
        return $this->recommandeHomme;
    }

    /**
     * Set recommandeFemme.
     *
     * @param \B2bBundle\Entity\Brand|null $recommandeFemme
     *
     * @return BrandRecommande
     */
    public function setRecommandeFemme(\B2bBundle\Entity\Brand $recommandeFemme = null)
    {
        $this->recommandeFemme = $recommandeFemme;

        return $this;
    }

    /**
     * Get recommandeFemme.
     *
     * @return \B2bBundle\Entity\Brand|null
     */
    public function getRecommandeFemme()
    {
        return $this->recommandeFemme;
    }

    /**
     * Set recommandeEnfant.
     *
     * @param \B2bBundle\Entity\Brand|null $recommandeEnfant
     *
     * @return BrandRecommande
     */
    public function setRecommandeEnfant(\B2bBundle\Entity\Brand $recommandeEnfant = null)
    {
        $this->recommandeEnfant = $recommandeEnfant;

        return $this;
    }

    /**
     * Get recommandeEnfant.
     *
     * @return \B2bBundle\Entity\Brand|null
     */
    public function getRecommandeEnfant()
    {
        return $this->recommandeEnfant;
    }
}
