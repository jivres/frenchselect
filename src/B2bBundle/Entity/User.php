<?php

namespace B2bBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\UserRepository")
 * @UniqueEntity("username")
 * @ORM\InheritanceType("JOINED")
 * @ORM\MappedSuperclass
 */
class User implements AdvancedUserInterface, \Serializable {
    const NO_PASSWORD = "jemappellemichel";

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=false)
     */
    protected $username;

    /**
     * @var string $mail
     * @Assert\Email
     *
     * @ORM\Column(name="mail", type="string", length=255, unique=true, nullable=false)
     */
    protected $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = array();

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /** CONNEXION POUR UN CLIENT **/

    /**
     * @ORM\ManyToOne(targetEntity="B2bBundle\Entity\Customer", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $connectedFor;

    /**
     * @ORM\ManyToMany(targetEntity="B2bBundle\Entity\Shop", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $connectedForShops;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return User
     */
    public function setMail($mail) {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail() {
        return $this->mail;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Set default password (no password)
     * @return User
     */
    public function setDefaultPassword() {
        return $this->setPassword(User::NO_PASSWORD);
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    public function getParent() {
        return $this;
    }

    public function __construct() {
        $this->isActive = true;
        $this->salt = '';
        $this->password = User::NO_PASSWORD;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));


        $this->connectedForShops = new ArrayCollection();
    }


    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles() {
        return $this->roles;
    }

    public function setRoles($roles) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {

    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired() {
        return $this->isActive;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked() {
        return $this->isActive;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired() {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled() {
        return $this->isActive;
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

    /**
     * Check if Brand is connectedFor
     * @return bool
     */
    public function isConnectedFor() {
        return $this->connectedFor != null;
    }

    /**
     * Get connected for Customer
     *
     * @return Customer
     */
    public function getConnectedFor() {
        return $this->connectedFor;
    }

    /**
     * Set connected for Customer
     * @param Customer $customer
     * @return User
     */
    public function setConnectedFor(Customer $customer) {
        $this->connectedFor = $customer;

        return $this;
    }

    /**
     * Add connected for Shop
     *
     * @param Shop $shop
     *
     * @return User
     */
    public function addConnectedForShop(Shop $shop) {
        $this->connectedForShops[] = $shop;

        return $this;
    }

    /**
     * Remove connected for Shop
     *
     * @param Shop $shop
     */
    public function removeConnectedForShop(Shop $shop) {
        $this->connectedForShops->removeElement($shop);
    }

    public function clearConnectedFor() {
        foreach ($this->connectedForShops as $connectedForShop) {
            $this->removeConnectedForShop($connectedForShop);
        }
        $this->connectedFor = null;
    }

    /**
     * Get connected for Shops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConnectedForShops() {
        return $this->connectedForShops;
    }

    public function __toString() {
        return "peut etre";
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
