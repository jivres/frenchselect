<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatusContactCustomer
 *
 * @ORM\Table(name="status_contact_customer")
 * @ORM\Entity
 */
class StatusContactCustomer {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    public function __toString() {
        return $this->label;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return StatusContactCustomer
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }






}
