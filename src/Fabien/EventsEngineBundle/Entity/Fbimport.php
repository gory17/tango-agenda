<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fbimport
 *
 * @ORM\Table(name="fbimport")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\FbimportRepository")
 */
class Fbimport
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
     * @ORM\Column(name="fbid", type="string", length=255)
     */
    private $fbid;

    /**
     * @var int
     *
     * @ORM\Column(name="tentative", type="integer", nullable=true)
     */
    private $tentative;


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
     * Set fbid
     *
     * @param string $fbid
     *
     * @return Fbimport
     */
    public function setFbid($fbid)
    {
        $this->fbid = $fbid;

        return $this;
    }

    /**
     * Get fbid
     *
     * @return string
     */
    public function getFbid()
    {
        return $this->fbid;
    }

    /**
     * Set tentative
     *
     * @param integer $tentative
     *
     * @return Fbimport
     */
    public function setTentative($tentative)
    {
        $this->tentative = $tentative;

        return $this;
    }

    /**
     * Get tentative
     *
     * @return int
     */
    public function getTentative()
    {
        return $this->tentative;
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
