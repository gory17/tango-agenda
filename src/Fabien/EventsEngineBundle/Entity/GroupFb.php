<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupFb
 *
 * @ORM\Table(name="group_fb")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\GroupFbRepository")
 */
class GroupFb
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


    /**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
     private $valide;


     /**
      * @var int
      *
      * @ORM\Column(name="tentatives", type="integer", nullable=true)
      */
     private $tentatives;


    /**
     * @var string
     *
     * @ORM\Column(name="fbid", type="string",length=65, nullable=false)
     */
    private $fbid;


    public function __construct()
    {
      $this->statut="todo";
      $this->valide=true;
      $this->tentatives=0;
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
     * @return GroupFb
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
     * @return GroupFb
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
     * Set valide
     *
     * @param boolean $valide
     *
     * @return GroupFb
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean
     */
    public function getValide()
    {
        return $this->valide;
    }



    /**
     * Set count
     *
     * @param integer $count
     *
     * @return GroupFb
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
     * Set tentatives
     *
     * @param integer $tentatives
     *
     * @return GroupFb
     */
    public function setTentatives($tentatives)
    {
        $this->tentatives = $tentatives;

        return $this;
    }

    /**
     * Get tentatives
     *
     * @return int
     */
    public function getTentatives()
    {
        return $this->tentatives;
    }



    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return GroupFb
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
     * Set fbid
     *
     * @param integer $fbid
     *
     * @return
     */
    public function setFbId($fbid)
    {
        $this->fbid = $fbid;

        return $this;
    }

    /**
     * Get fbid
     *
     * @return int
     */
    public function getFbId()
    {
        return $this->fbid;
    }
}
