<?php

namespace Fabien\EventsEngineBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\CityRepository")
 */
class City
{


  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Event", mappedBy="city")
   */
  private $events;

  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\State", inversedBy="cities")
   * @ORM\JoinColumn(nullable=false)
   */
  private $state;



  public function __construct()
   {
     $this->events = new ArrayCollection();
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
     * @ORM\Column(name="urlimage", type="string", length=255, nullable=true)
     */
    private $urlImage;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;


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
     * @return City
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
     * Set urlImage
     *
     * @param string $title
     *
     * @return City
     */
    public function setUrlImage($urlImage)
    {
        $this->urlImage = $urlImage;

        return $this;
    }

    /**
     * Get urlImage
     *
     * @return string
     */
    public function getUrlImage()
    {
        return $this->urlImage;
    }



    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return City
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
     * Set state
     *
     * @param \Fabien\EventsEngineBundle\Entity\State $state
     *
     * @return City
     */
    public function setState(\Fabien\EventsEngineBundle\Entity\State $state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get State
     *
     * @return \Fabien\EventsEngineBundle\Entity\State
     */
    public function getState()
    {
        return $this->state;
    }



    public function addEvent(Event $event)
    {
      $this->events[] = $event;

      $event->setEvent($this);
      return $this;
    }

    public function removeEvent(Event $event)
    {
      $this->events->removeEvent($event);
    }

    public function getEvents()
    {
      return $this->events;
    }




}
