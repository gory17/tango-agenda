<?php

namespace Fabien\EventsEngineBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\EventRepository")
 */
class Event
{

  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\TypeEvent", inversedBy="events")
   * @ORM\JoinColumn(nullable=false)
   */
  private $type_event;


  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Country")
   * @ORM\JoinColumn(nullable=false)
   */
  private $country;



  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\City", inversedBy="events")
   * @ORM\JoinColumn(nullable=true)
   * @ORM\OrderBy({"title" = "ASC"})
   */
  private $city;

  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Date", mappedBy="event",cascade={"persist"}, orphanRemoval=true)
   */
  private $dates;

  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image", inversedBy="events")
   * @ORM\JoinColumn(name="image_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
   */
  private $image;

  /**
   * @Assert\Image(
   *     maxSize="1M",
   *  maxSizeMessage="Image trop volumineuse (max 1Mo)"
   * )
   */
  private $file;

  private $tempFilename;
  private $alt;


  public function __construct()
    {
      $this->dateInscription=null;
      $this->tocken=md5("ceciestuncodesecret".date("y:h:m:s"));
      $this->date_creation=new \Datetime();
      $this->date_update=new \Datetime();
      $this->dates = new ArrayCollection();
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
     * @var integer
     *
     * @ORM\Column(name="day", type="integer",nullable=true)
     */
    private $day;


    /**
     * @var string
     *
     * @ORM\Column(name="organizer", type="string", length=255, nullable=true)
     */
    private $organizer;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url_web", type="string", length=255, nullable=true)
     */
    private $urlWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="url_fb", type="string", length=255, nullable=true)
     */
    private $urlFb;

    /**
     * @var string
     *
     * @ORM\Column(name="adress", type="string", length=255)
     */
    private $adress;

    /**
     * @var string
     *
     * @ORM\Column(name="city_other", type="string", length=255, nullable=true)
     */
    private $city_other;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255, nullable=false)
     */
    private $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $date_creation;

    /**
     * @var string
     *
     * @ORM\Column(name="date_update", type="datetime" , nullable=true)
     */
    private $date_update;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_creator", length=255, nullable=true)
     */
    private $mail_creator;

    /**
     * @var string
     *
     * @ORM\Column(name="password_creator", length=255, nullable=true)
     */
    private $password_creator;



    /**
     * @var string
     *
     * @ORM\Column(name="tocken", length=255, nullable=false)
     */
    private $tocken;


    /**
     * @var bool
     *
     * @ORM\Column(name="gender_balance", type="boolean")
     */
    private $genderBalance;

    /**
     * @var bool
     *
     * @ORM\Column(name="publish", type="boolean",nullable=true)
     */
    private $publish;


    /**
     * @var int
     *
     * @ORM\Column(name="valorisation", type="integer",nullable=true)
     */
    private $valorisation;



    /**
     * @var bool
     *
     * @ORM\Column(name="inscription", type="boolean",nullable=true)
     */
    private $inscription;


    /**
     * @var string
     *
     * @ORM\Column(name="date_inscription", type="datetime",nullable=true)
     */
    private $dateInscription;


    /**
     * @var string
     *
     * @ORM\Column(name="lieu_raw", type="text", nullable=true)
     */
    private $lieuRaw;


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
     * @return Event
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
     * Set lieuRaw
     *
     * @param string $lieuRaw
     *
     * @return Event
     */
    public function setLieuRaw($lieuRaw)
    {
        $this->lieuRaw = $lieuRaw;

        return $this;
    }

    /**
     * Get lieuRaw
     *
     * @return string
     */
    public function getLieuRaw()
    {
        return $this->lieuRaw;
    }


    /**
     * Set day
     *
     * @param string $day
     *
     * @return Event
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }




    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
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
     * Set urlWeb
     *
     * @param string $urlWeb
     *
     * @return Event
     */
    public function setUrlWeb($urlWeb)
    {
        $this->urlWeb = $urlWeb;

        return $this;
    }

    /**
     * Get urlWeb
     *
     * @return string
     */
    public function getUrlWeb()
    {
        return $this->urlWeb;
    }

    /**
     * Set urlFb
     *
     * @param string $urlFb
     *
     * @return Event
     */
    public function setUrlFb($urlFb)
    {
        $this->urlFb = $urlFb;

        return $this;
    }

    /**
     * Get urlFb
     *
     * @return string
     */
    public function getUrlFb()
    {
        return $this->urlFb;
    }

    /**
     * Set adress
     *
     * @param string $adress
     *
     * @return Event
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string
     */
    public function getAdress()
    {
        return $this->adress;
    }


    /**
     * Set city_other
     *
     * @param string $city_other
     *
     * @return Event
     */
    public function setCityOther($city_other)
    {
        $this->city_other = $city_other;

        return $this;
    }

    /**
     * Get city_other
     *
     * @return string
     */
    public function getCityOther()
    {
        return $this->city_other;
    }




    /**
     * Set organizer
     *
     * @param string $organizer
     *
     * @return Event
     */
    public function setOrganizer($organizer)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizer
     *
     * @return string
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }



    /**
     * Set genderBalance
     *
     * @param boolean $genderBalance
     *
     * @return Event
     */
    public function setGenderBalance($genderBalance)
    {
        $this->genderBalance = $genderBalance;

        return $this;
    }

    /**
     * Get genderBalance
     *
     * @return bool
     */
    public function getGenderBalance()
    {
        return $this->genderBalance;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     *
     * @return Event
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
     * Set typeEvent
     *
     * @param \Fabien\EventsEngineBundle\Entity\TypeEvent $typeEvent
     *
     * @return Event
     */
    public function setTypeEvent(\Fabien\EventsEngineBundle\Entity\TypeEvent $typeEvent)
    {
        $this->type_event = $typeEvent;

        return $this;
    }

    /**
     * Get typeEvent
     *
     * @return \Fabien\EventsEngineBundle\Entity\TypeEvent
     */
    public function getTypeEvent()
    {
        return $this->type_event;
    }



    /**
     * Set country
     *
     * @param \Fabien\EventsEngineBundle\Entity\Country $country
     *
     * @return Event
     */
    public function setCountry(\Fabien\EventsEngineBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return \Fabien\EventsEngineBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }





    /**
     * Set city
     *
     * @param \Fabien\EventsEngineBundle\Entity\City $city
     *
     * @return Event
     */
    public function setCity(\Fabien\EventsEngineBundle\Entity\City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Fabien\EventsEngineBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }



    /**
     * Set alt
     *
     * @param string $organizer
     *
     * @return Event
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }


    public function getFile()
    {
      return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
      $this->file = $file;
    }


    /**
     * Set origin
     *
     * @param string $origin
     *
     * @return Event
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Event
     */
    public function setDateCreation($dateCreation)
    {
        $this->date_creation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }


    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return Event
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->date_update = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->date_update;
    }

    /**
     * Set mailCreator
     *
     * @param string $mailCreator
     *
     * @return Event
     */
    public function setMailCreator($mailCreator)
    {
        $this->mail_creator = $mailCreator;

        return $this;
    }

    /**
     * Get mailCreator
     *
     * @return string
     */
    public function getMailCreator()
    {
        return $this->mail_creator;
    }

    /**
     * Set tocken
     *
     * @param string $tocken
     *
     * @return Event
     */
    public function setTocken($tocken)
    {
        $this->tocken = $tocken;

        return $this;
    }

    /**
     * Get tocken
     *
     * @return string
     */
    public function getTocken()
    {
        return $this->tocken;
    }


    /**
     * Set passwordCreator
     *
     * @param string $passwordCreator
     *
     * @return Event
     */
    public function setPasswordCreator($passwordCreator)
    {
        $this->password_creator = $passwordCreator;

        return $this;
    }

    /**
     * Get passwordCreator
     *
     * @return string
     */
    public function getPasswordCreator()
    {
        return $this->password_creator;
    }

    /**
     * Set valorisation
     *
     * @param integer $valorisation
     *
     * @return Event
     */
    public function setValorisation($valorisation)
    {
        $this->valorisation = $valorisation;

        return $this;
    }

    /**
     * Get valorisation
     *
     * @return integer
     */
    public function getValorisation()
    {
        return $this->valorisation;
    }

    /**
     * Set inscription
     *
     * @param boolean $inscription
     *
     * @return Event
     */
    public function setInscription($inscription)
    {
        $this->inscription = $inscription;

        return $this;
    }

    /**
     * Get inscription
     *
     * @return boolean
     */
    public function getInscription()
    {
        return $this->inscription;
    }



    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     *
     * @return Event
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }


    public function addDate(Date $date)
    {
      $date->setEvent($this);
      $this->dates[] = $date;

      return $this;
    }

    public function removeDate(Date $date)
    {
      $this->dates->removeElement($date);
    }

    public function getDates()
    {
      return $this->dates;
    }


}
