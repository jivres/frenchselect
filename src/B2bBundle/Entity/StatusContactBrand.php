<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StatusContactBrand
 *
 * @ORM\Table(name="status_contact_brand")
 * @ORM\Entity
 */
class StatusContactBrand {
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






}
