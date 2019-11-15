<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\StateRepository")
 */
class State
{

  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\City", mappedBy="state")
   */
  private $cities;

    /**
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Country", inversedBy="states")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

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
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;


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
     * @return State
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
     * Set slug
     *
     * @param string $slug
     *
     * @return State
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





public function addCity(City $city)
{
  $this->cities[] = $city;

  $city->setCity($this);
  return $this;
}

public function removeCity(City $city)
{
  $this->cities->removeState($city);
}

public function getCities()
{
  return $this->cities;
}
}
