<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParticipeSalon
 *
 * @ORM\Table(name="participe_salon")
 * @ORM\Entity
 */
class ParticipeSalon
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
     * @var string
     *
     * @ORM\Column(name="stand", type="string", length=255, nullable=true)
     */
    private $stand;

    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumn(nullable=false)
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
     * Set stand.
     *
     * @param string $stand
     *
     * @return ParticipeSalon
     */
    public function setStand($stand)
    {
        $this->stand = $stand;

        return $this;
    }

    /**
     * Get stand.
     *
     * @return string
     */
    public function getStand()
    {
        return $this->stand;
    }

    /**
     * Set brand.
     *
     * @param Brand $brand
     *
     * @return ParticipeSalon
     */
    public function setBrand(\B2bBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

}
