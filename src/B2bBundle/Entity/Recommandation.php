<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recommandation
 *
 * @ORM\Table(name="recommandation")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\RecommandationRepository")
 */
class Recommandation {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }



    public function __toString() {
        return $this->label;
    }
}
