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
     * @ORM\ManyToMany(targetEntity="MusicShareBundle\Entity\User")
     * @ORM\JoinTable(name="favorites_songs",
     *     joinColumns={@ORM\JoinColumn(name="song_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")})
     */
    private $favorites;

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

    public function getFavorites()
    {
        return $this->favorites;
    }

    public function addToFavorites($user)
    {
        $this->favorites[] = $user;

        return $this;
    }

    public function setFavorites($users)
    {
        $this->favorites = $users;

        return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId() : int
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="MusicShareBundle\Entity\Category", inversedBy="songs")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}

