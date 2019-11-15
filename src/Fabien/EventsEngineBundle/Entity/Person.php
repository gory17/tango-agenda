<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Person
 *
 * @ORM\Table(name="person")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\PersonRepository")
 */
class Person
{

  /**
   * @ORM\ManyToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Video", mappedBy="persons")
   */
   private $videos;


   /**
    * @ORM\ManyToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Event", mappedBy="persons")
    */
    private $events;


   public function __construct()
     {
       $this->active=true;
       $this->videos = new ArrayCollection();
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
     * @ORM\OneToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Person", cascade={"persist"})
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=false)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, unique=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=false)
     */
    private $prenom;


    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_default_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private $image;

    /**
     * @Assert\Image(
     *     maxSize="3M",
     *  maxSizeMessage="Image trop volumineuse (max 3Mo)"
     * )
     */
    private $file;

    private $tempFilename;
    /**
     * @var string
     *
     * @ORM\Column(name="surnom", type="string", length=255, nullable=true)
     */
    private $surnom;

    /**
     * @var string
     *
     * @ORM\Column(name="siteweb", type="string", length=255, nullable=true)
     */
    private $siteweb;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="wikipedia", type="string", length=255, nullable=true)
     */
    private $wikipedia;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="credit_photo", type="string", length=255, nullable=true)
     */
    private $creditPhoto;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;


    /**
     * @var bool
     *
     * @ORM\Column(name="homonyme", type="boolean")
     */
    private $homonyme;

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
     * Set slug
     *
     * @param string $slug
     *
     * @return Person
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Person
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Person
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }




    /**
     * Set type
     *
     * @param string $type
     *
     * @return Person
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
     * Set creditPhoto
     *
     * @param string $creditPhoto
     *
     * @return Person
     */
    public function setCreditPhoto($creditPhoto)
    {
        $this->creditPhoto = $creditPhoto;

        return $this;
    }

    /**
     * Get creditPhoto
     *
     * @return string
     */
    public function getCreditPhoto()
    {
        return $this->creditPhoto;
    }





    /**
     * Set surnom
     *
     * @param string $surnom
     *
     * @return Person
     */
    public function setSurnom($surnom)
    {
        $this->surnom = $surnom;

        return $this;
    }

    /**
     * Get surnom
     *
     * @return string
     */
    public function getSurnom()
    {
        return $this->surnom;
    }

    /**
     * Set siteweb
     *
     * @param string $siteweb
     *
     * @return Person
     */
    public function setSiteweb($siteweb)
    {
        $this->siteweb = $siteweb;

        return $this;
    }

    /**
     * Get siteweb
     *
     * @return string
     */
    public function getSiteweb()
    {
        return $this->siteweb;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     *
     * @return Person
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set wikipedia
     *
     * @param string $wikipedia
     *
     * @return Person
     */
    public function setWikipedia($wikipedia)
    {
        $this->wikipedia = $wikipedia;

        return $this;
    }

    /**
     * Get wikipedia
     *
     * @return string
     */
    public function getWikipedia()
    {
        return $this->wikipedia;
    }




    /**
     * Set role
     *
     * @param string $role
     *
     * @return Person
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }




    /**
     * Set homonyme
     *
     * @param boolean $homonyme
     *
     * @return Person
     */
    public function setHomonyme($homonyme)
    {
        $this->homonyme = $homonyme;

        return $this;
    }

    /**
     * Get homonyme
     *
     * @return bool
     */
    public function getHomonyme()
    {
        return $this->homonyme;
    }


    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Person
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Person
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



    public function getPartner()
    {
      return $this->partner;
    }

    public function setPartner(Person $partner = null)
    {
      $this->partner = $partner;
    }



    public function __toString(){
        return $this->prenom.' '.$this->nom;
    }


    public function addVideo(Video $video)
    {
      $this->videos[] = $video;

      return $this;
    }

    public function removeVideo(Video $video)
    {
      $this->videos->removeElement($video);
    }

    public function getVideos()
    {
      return $this->videos;
    }





    public function addEvent(Event $event)
    {
      $this->events[] = $event;

      return $this;
    }

    public function removeEvent(Event $event)
    {
      $this->events->removeElement($event);
    }

    public function getEvents()
    {
      return $this->events;
    }
}
