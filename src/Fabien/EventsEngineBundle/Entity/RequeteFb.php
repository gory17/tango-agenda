<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RequeteFb
 *
 * @ORM\Table(name="requete_fb")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\RequeteFbRepository")
 */
class RequeteFb
{


  /**
   * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\TypeEvent", inversedBy="events")
   * @ORM\JoinColumn(nullable=false)
   */
  private $type_event;

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
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=true)
     */
    private $count;


    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut;


    public function __construct()
    {
      $this->statut="todo";
    }


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
     * @return RequeteFb
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
     * Set statut
     *
     * @param string $statut
     *
     * @return RequeteFb
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }





    /**
     * Set count
     *
     * @param integer $count
     *
     * @return RequeteFb
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
}
