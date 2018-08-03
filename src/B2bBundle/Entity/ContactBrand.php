<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContactBrand
 *
 * @ORM\Table(name="contact_brand")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\ContactRepository")
 */
class ContactBrand
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
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\StatusContactBrand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $function;

    /**
     * Get function
     *
     * @return \B2bBundle\Entity\StatusContactBrand
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set function
     *
     * @param \B2bBundle\Entity\StatusContactBrand $function
     *
     * @return ContactBrand
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

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
     * Set firstname
     *
     * @param string $firstname
     * @return ContactBrand
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return ContactBrand
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ContactBrand
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function __toString()
    {
        return $this->lastname . ', ' . $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return ContactBrand
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}
