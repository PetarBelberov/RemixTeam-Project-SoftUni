<?php

namespace MusicShareBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="MusicShareBundle\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;


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
     * Set name
     *
     * @param string $name
     *
     * @return Category
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



    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MusicShareBundle\Entity\Sound", mappedBy="category")
     */
    private  $songs;

    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @param ArrayCollection $songs
     */
    public function setSongs (ArrayCollection $songs)
    {
        $this->songs=$songs;
    }

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    function __toString()
    {
        return $this->getName();
    }

}

