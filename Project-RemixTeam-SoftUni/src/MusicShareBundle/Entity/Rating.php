<?php

namespace MusicShareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table(name="ratings")
 * @ORM\Entity(repositoryClass="MusicShareBundle\Repository\RatingRepository")
 */
class Rating
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
     * @var int
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="soundId", type="integer")
     */
    private $soundId;

    /**
     * @var int
     *
     * @ORM\Column(name="liked", type="integer")
     */
    private $liked;


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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Rating
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set soundId
     *
     * @param integer $soundId
     *
     * @return Rating
     */
    public function setSoundId($soundId)
    {
        $this->soundId = $soundId;

        return $this;
    }

    /**
     * Get soundId
     *
     * @return int
     */
    public function getSoundId()
    {
        return $this->soundId;
    }

    /**
     * Set liked
     *
     * @param integer $liked
     *
     * @return Rating
     */
    public function setLiked($liked)
    {
        $this->liked = $liked;

        return $this;
    }

    /**
     * Get liked
     *
     * @return int
     */
    public function getLiked()
    {
        return $this->liked;
    }
}

