<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Size
 *
 * @ORM\Table(name="size")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\SizeRepository")
 */
class Size {
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="val", type="string", length=255)
     */
    private $val;


    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set val
     *
     * @param string $val
     *
     * @return Size
     */
    public function setVal($val) {
        $this->val = $val;

        return $this;
    }

    /**
     * Get val
     *
     * @return string
     */
    public function getVal() {
        return $this->val;
    }

    /**
     * Set rank
     *
     * @param int $rank
     *
     * @return Size
     */
    public function setRank($rank) {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank() {
        return $this->rank;
    }

    public function __toString() {
        return $this->val;
    }
}
