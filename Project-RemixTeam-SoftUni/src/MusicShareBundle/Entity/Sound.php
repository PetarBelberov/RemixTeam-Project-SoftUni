<?php

namespace MusicShareBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sound
 *
 * @ORM\Table(name="sound")
 * @ORM\Entity(repositoryClass="MusicShareBundle\Repository\SoundRepository")
 */
class Sound
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
     * @ORM\Column(name="songName", type="string", length=255)
     */
    private $songName;

    /**
     * @ORM\Column(type="string")
     *
     *
     * @Assert\File(mimeTypes={ "audio/mpeg", "audio/wav", "audio/x-wav", "application/octet-stream" })
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="songAuthor", type="string", length=255)
     */
    private $songAuthor;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="MusicShareBundle\Entity\User", inversedBy="songs")
     * @ORM\JoinColumn(name="uploaderID", referencedColumnName="id")
     */
    private $uploader;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\File(mimeTypes={ "image/bmp", "image/x-windows-bmp", "image/gif", "image/jpeg", "image/pjpeg", "image/png" })
     */
    private $coverFile;

    /**
     * @ORM\Column(type="integer", name="uploaderID")
     */
    private $uploaderID;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MusicShareBundle\Entity\PlayList")
     * @ORM\JoinTable(name="playlist_songs",
     *     joinColumns={@ORM\JoinColumn(name="song_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="playListID", referencedColumnName="id")})
     */
    private $playLists;

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
     * Set songName
     *
     * @param string $songName
     *
     * @return Sound
     */
    public function setSongName($songName)
    {
        $this->songName = $songName;

        return $this;
    }

    /**
     * Get songName
     *
     * @return string
     */
    public function getSongName()
    {
        return $this->songName;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set songAuthor
     *
     * @param string $songAuthor
     *
     * @return Sound
     */
    public function setSongAuthor($songAuthor)
    {
        $this->songAuthor = $songAuthor;

        return $this;
    }

    public function setUploaderID($uploader)
    {
        $this->uploaderID = $uploader;

        return $this;
    }

    public function getUploaderID()
    {
        return $this->uploaderID;
    }

    /**
     * Get songAuthor
     *
     * @return string
     */
    public function getSongAuthor()
    {
        return $this->songAuthor;
    }

    public function setCoverFile($file)
    {
        $this->coverFile = $file;

        return $this;
    }

    public function getCoverFile()
    {
        return $this->coverFile;
    }

    public function setUploader(User $uploader = null)
    {
        $this->uploader = $uploader;

        return $this;
    }

    public function getUploader()
    {
        return $this->uploader;
    }

    public function getPlayLists()
    {
        return $this->playLists;
    }

    public function addToPlayList($playList)
    {
        $this->playLists[] = $playList;

        return $this;
    }

    public function setPlayLists($playLists)
    {
        $this->playLists = $playLists;

        return $this;
    }
}

