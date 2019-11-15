<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\VideoRepository")
 */
class Video
{


  /**
   * @ORM\ManyToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Person", inversedBy="videos")
   */
   private $persons;


  public function __construct()
    {
      $this->dateCreation=new \Datetime();
      $this->persons = new ArrayCollection();
      $this->topVideo=false;
      $this->publish=true;
    }

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="youtube_id", type="string", length=255)
     */
    private $youtubeId;

    /**
     * @var string
     *
     * @ORM\Column(name="url_image", type="string", length=255)
     */
    private $urlImage;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255,nullable=true)
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePublication", type="datetime")
     */
    private $datePublication;

    /**
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_default_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private $image;


    /**
     * @var bool
     *
     * @ORM\Column(name="publish", type="boolean",nullable=true)
     */
    private $publish;


    /**
     * @var bool
     *
     * @ORM\Column(name="topvideo", type="boolean",nullable=true)
     */
    private $topVideo;


    /**
     * @Assert\Image(
     *     maxSize="3M",
     *  maxSizeMessage="Image trop volumineuse (max 3Mo)"
     * )
     */
    private $file;

    private $tempFilename;
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
     * Set publish
     *
     * @param boolean $publish
     *
     * @return Video
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return bool
     */
    public function getPublish()
    {
        return $this->publish;
    }



    /**
     * Set topVideo
     *
     * @param boolean $topVideo
     *
     * @return Event
     */
    public function setTopVideo($topVideo)
    {
        $this->topVideo = $topVideo;

        return $this;
    }

    /**
     * Get topVideo
     *
     * @return bool
     */
    public function getTopVideo()
    {
        return $this->topVideo;
    }


    /**
     * Set title
     *
     * @param string $title
     *
     * @return Video
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
     * Set type
     *
     * @param string $type
     *
     * @return Video
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }




    /**
     * Set youtubeId
     *
     * @param string $title
     *
     * @return Video
     */
    public function setYoutubeId($youtubeId)
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    /**
     * Get youtubeId
     *
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->youtubeId;
    }




    /**
     * Set urlImage
     *
     * @param string $urlImage
     *
     * @return Video
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get youtubeId
     *
     * @return string
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }




    /**
     * Set description
     *
     * @param string $description
     *
     * @return Video
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

    /**
     * Set source
     *
     * @param string $source
     *
     * @return Video
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Video
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Video
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set datePublication
     *
     * @param \DateTime $datePublication
     *
     * @return Video
     */
    public function setDatePublication($datePublication)
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    /**
     * Get datePublication
     *
     * @return \DateTime
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    public function setImage(Image $image)
    {
      $this->image= $image;
      return $this;
    }


    public function getImage()
    {
      return $this->image;
    }


    public function getFile()
    {
      return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
      $this->file = $file;
    }


    public function addPerson(Person $person)
    {
      $this->persons[] = $person;
      $person->addVideo($this);

      return $this;
    }

    public function removePerson(Person $person)
    {
      $this->persons->removeElement($person);
      $person->removeVideo($this);
    }

    public function getPersons()
    {
      return $this->persons;
    }
}
