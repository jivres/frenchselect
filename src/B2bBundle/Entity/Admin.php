<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Admin
 *
 * @ORM\Table(name="admin")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\AdminRepository")
 */
class Admin extends User
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Admin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getUser()
    {
        return parent::getParent();
    }

    public function __construct() {
        parent::__construct();

        $this->setRoles(array('ROLE_ADMIN'));
    }
}
