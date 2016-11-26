<?php

namespace MusicShareBundle\Entity;

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
     * @Assert\NotBlank(message="Please, upload the song as a MP3 file.")
     * @Assert\File(mimeTypes={ "audio/mpeg" })
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="songAuthor", type="string", length=255)
     */
    private $songAuthor;

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

    /**
     * Get songAuthor
     *
     * @return string
     */
    public function getSongAuthor()
    {
        return $this->songAuthor;
    }
}

