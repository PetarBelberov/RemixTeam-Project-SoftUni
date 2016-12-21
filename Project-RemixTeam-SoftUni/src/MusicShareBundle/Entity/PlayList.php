<?php

namespace MusicShareBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayList
 *
 * @ORM\Table(name="play_list")
 * @ORM\Entity(repositoryClass="MusicShareBundle\Repository\PlayListRepository")
 */
class PlayList
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
     * @ORM\Column(name="ownerID", type="integer")
     */
    private $ownerID;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MusicShareBundle\Entity\User", inversedBy="playLists")
     * @ORM\JoinColumn(name="ownerID", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MusicShareBundle\Entity\Sound", mappedBy="playLists")
     */
    private $songs;

    /**
     * @var string
     *
     * @ORM\Column(name="playlist_name", type="string")
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
     * @param integer $id
     * @return $this
     */
    public function setOwnerID($id)
    {
        $this->ownerID = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerID()
    {
        return $this->ownerID;
    }

    /**
     * @param $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSongs()
    {
        return $this->songs;
    }

    public function addSong($song)
    {
        $this->songs[] = $song;

        return $this;
    }

    public function setSongs($songs)
    {
        $this->songs = $songs;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }
}

