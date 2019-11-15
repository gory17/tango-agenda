<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banned
 *
 * @ORM\Table(name="banned")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\BannedRepository")
 */
class Banned
{


    public function __construct()
      {
        $this->dateBanned=new \Datetime();
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
     * @ORM\Column(name="eventurl", type="string", length=255,nullable=true)
     */
    private $eventurl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_banned", type="datetime")
     */
    private $dateBanned;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=true)
     */
    private $ip;


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
     * Set eventurl
     *
     * @param string $eventurl
     *
     * @return Banned
     */
    public function setEventurl($eventurl)
    {
        $this->eventurl = $eventurl;

        return $this;
    }

    /**
     * Get eventurl
     *
     * @return string
     */
    public function getEventurl()
    {
        return $this->eventurl;
    }

    /**
     * Set dateBanned
     *
     * @param \DateTime $dateBanned
     *
     * @return Banned
     */
    public function setDateBanned($dateBanned)
    {
        $this->dateBanned = $dateBanned;

        return $this;
    }

    /**
     * Get dateBanned
     *
     * @return \DateTime
     */
    public function getDateBanned()
    {
        return $this->dateBanned;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Banned
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
