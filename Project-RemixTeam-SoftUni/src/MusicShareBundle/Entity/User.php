<?php

namespace MusicShareBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="MusicShareBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MusicShareBundle\Entity\Sound", mappedBy="uploader")
     */
    private $songs;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MusicShareBundle\Entity\Sound", mappedBy="favorites")
     */
    private $favoriteSongs;

    /**
     * @var ArrayCollection
     *
     * @ManyToMany(targetEntity="MusicShareBundle\Entity\Role")
     * @JoinTable(name="users_roles",
     *       joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *       inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *       )
     */
    private $roles;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @param \MusicShareBundle\Entity\Sound $songs
     *
     * @return User
     */
    public function addSong(Sound $songs)
    {
        $this->songs[] = $songs;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFavoriteSongs()
    {
        return $this->favoriteSongs;
    }

    /**
     * @param \MusicShareBundle\Entity\Sound $song
     *
     * @return User
     */
    public function addSongToFavorites(Sound $song)
    {
        $this->favoriteSongs[] = $song;

        return $this;
    }

    /**
     * @param $songs
     * @return $this
     */
    public function setFavoriteSongs($songs)
    {
        $this->favoriteSongs = $songs;

        return $this;
    }

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
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role)
        {
            /** @var $role Role */
            $stringRoles[] = is_string($role) ? $role : $role->getRole();
        }
        return $stringRoles;
    }

    /**
     * @param \MusicShareBundle\Entity\Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param Sound $song
     * @return bool
     */
    public function isAuthor(Sound $sound)
    {
        return $sound->getUploaderID() == $this->getId();
    }

    /**
     * @return bool
     */
    public  function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString() {
        return $this->username;
    }

    //Constructor
    public function __construct()
    {
        $this->songs = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }
}

