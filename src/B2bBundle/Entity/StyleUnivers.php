<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StyleUniversCalcul
 *
 * @ORM\Table(name="style_univers")
 * @ORM\Entity
 */
class StyleUnivers
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
     * @var Style
     *
     * @ORM\ManyToOne(targetEntity="Style")
     * @ORM\JoinColumn(nullable=false)
     */
    private $style;


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
     * @return StyleUnivers
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
     * Set style.
     *
     * @param \B2bBundle\Entity\Style|null $style
     *
     * @return StyleUnivers
     */
    public function setStyle(\B2bBundle\Entity\Style $style = null)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get style.
     *
     * @return \B2bBundle\Entity\Style|null
     */
    public function getStyle()
    {
        return $this->style;
    }

}
