<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Publicite
 *
 * @ORM\Table(name="publicite")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\PubliciteRepository")
 */
class Publicite
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
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image" , cascade={"persist"})
     * @ORM\JoinColumn(name="image_default_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image" , cascade={"persist"})
     * @ORM\JoinColumn(name="image_default_id2", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private $image2;

    /**
     * @Assert\Image(
     *     maxSize="3M",
     *  maxSizeMessage="Image trop volumineuse (max 3Mo)"
     * )
     */
    private $file;

    /**
     * @Assert\Image(
     *     maxSize="3M",
     *  maxSizeMessage="Image trop volumineuse (max 3Mo)"
     * )
     */
    private $file2;

    private $tempFilename;

    public function setImage(Image $image)
    {
      $this->image= $image;
      return $this;
    }


    public function getImage()
    {
      return $this->image;
    }

    public function setImage2(Image $image2)
    {
      $this->image2= $image2;
      return $this;
    }


    public function getImage2()
    {
      return $this->image2;
    }


    public function getFile()
    {
      return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
      $this->file = $file;
    }


    public function getFile2()
    {
      return $this->file2;
    }

    public function setFile2(UploadedFile $file2 = null)
    {
      $this->file2 = $file2;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="placement", type="string", length=255)
     */
    private $placement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var int
     *
     * @ORM\Column(name="click", type="integer", nullable=true)
     */
    private $click;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


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
     * Set title
     *
     * @param string $title
     *
     * @return Publicite
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Publicite
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set placement
     *
     * @param string $placement
     *
     * @return Publicite
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Publicite
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Publicite
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set click
     *
     * @param integer $click
     *
     * @return Publicite
     */
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * Get click
     *
     * @return int
     */
    public function getClick()
    {
        return $this->click;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Publicite
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
