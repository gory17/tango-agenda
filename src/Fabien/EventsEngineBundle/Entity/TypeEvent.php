<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * type_event
 *
 * @ORM\Table(name="type_event")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\type_eventRepository")
 */
class TypeEvent
{

  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Event", mappedBy="type_event")
   */
  private $events;

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
     * @ORM\Column(name="titletrad", type="string", length=255 , nullable=true)
     */
    private $titleTrad;


    /**
     * @var string
     *
     * @ORM\Column(name="descritpion", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="descritpiontrad", type="text", nullable=true)
     */
    private $descriptionTrad;

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
     * @return type_event
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
     * Set titleTrad
     *
     * @param string $titleTrad
     *
     * @return type_event
     */
    public function setTitleTrad($titleTrad)
    {
        $this->titleTrad = $titleTrad;

        return $this;
    }

    /**
     * Get titleTrad
     *
     * @return string
     */
    public function getTitleTrad()
    {
        return $this->titleTrad;
    }





    /**
     * Set description
     *
     * @param string $description
     *
     * @return type_event
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
     * Set descriptionTrad
     *
     * @param string $descriptionTrad
     *
     * @return type_event
     */
    public function setDescriptionTrad($descriptionTrad)
    {
        $this->descriptionTrad = $descriptionTrad;

        return $this;
    }

    /**
     * Get descriptionTrad
     *
     * @return string
     */
    public function getDescriptionTrad()
    {
        return $this->descriptionTrad;
    }



    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return type_event
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
