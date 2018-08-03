<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Salon
 *
 * @ORM\Table(name="salon", indexes={@ORM\Index(name="pays", columns={"pays"})})
 * @ORM\Entity
 */
class Salon
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lieu", type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var int
     *
     * @ORM\Column(name="zipCode", type="integer", nullable=false)
     */
    private $zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */

    private $lifestyle;

    /**
     *
     * @ORM\OneToOne(targetEntity="B2bBundle\Entity\MyFile", cascade={"remove", "persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $picture;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pays", referencedColumnName="id")
     * })
     */
    private $pays;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\ParticipeSalon", cascade={"remove","persist"})
     */
    private $brands;

    /**
     * @return mixed
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @param mixed $brands
     */
    public function setBrands($brands)
    {
        $this->brands = $brands;
    }

    /**
     * Remove brand
     *
     * @param ParticipeSalon $brand
     */
    public function removeBrand(ParticipeSalon $brand)
    {
        $this->brands->removeElement($brand);
    }



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
     * Set name.
     *
     * @param string $name
     *
     * @return Salon
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateDebut.
     *
     * @param \DateTime $dateDebut
     *
     * @return Salon
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut.
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin.
     *
     * @param \DateTime|null $dateFin
     *
     * @return Salon
     */
    public function setDateFin($dateFin = null)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin.
     *
     * @return \DateTime|null
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set lieu.
     *
     * @param string|null $lieu
     *
     * @return Salon
     */
    public function setLieu($lieu = null)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu.
     *
     * @return string|null
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set adresse.
     *
     * @param string|null $adresse
     *
     * @return Salon
     */
    public function setAdresse($adresse = null)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse.
     *
     * @return string|null
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set zipcode.
     *
     * @param int $zipcode
     *
     * @return Salon
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return int
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set ville.
     *
     * @param string|null $ville
     *
     * @return Salon
     */
    public function setVille($ville = null)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville.
     *
     * @return string|null
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Salon
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lifestyle.
     *
     * @param string|null $lifestyle
     *
     * @return Salon
     */
    public function setLifestyle($lifestyle = null)
    {
        $this->lifestyle = $lifestyle;

        return $this;
    }

    /**
     * Get lifestyle.
     *
     * @return string|null
     */
    public function getLifestyle()
    {
        return $this->lifestyle;
    }

    /**
     * Set picture.
     *
     * @param string|null $lifestyle
     *
     * @return Salon
     */
    public function setPicture($picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture.
     *
     * @return string|null
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set pays.
     *
     * @param \B2bBundle\Entity\Country|null $pays
     *
     * @return Salon
     */
    public function setPays(\B2bBundle\Entity\Country $pays = null)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays.
     *
     * @return \B2bBundle\Entity\Country|null
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * @ORM\Column(name="is_active", type="boolean" , options={"default" : 1})
     */
    private $isActive;

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * Set inactive
     * @return $this
     */
    public function setInactive() {
        $this->isActive = False;
        return $this;
    }

    /**
     * Set active
     * @return $this
     */
    public function setActive() {
        $this->isActive = True;
        return $this;
    }
}
