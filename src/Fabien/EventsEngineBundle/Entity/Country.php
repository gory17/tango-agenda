<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\CountryRepository")
 */
class Country
{


  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\State", mappedBy="country")
   */
  private $states;


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
     * @ORM\Column(name="sortname", type="string", length=10)
     */
    private $sortname;

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
     * @return Country
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
     * @return Country
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
     * Set sortname
     *
     * @param string $sortname
     *
     * @return Country
     */
    public function setSortname($sortname)
    {
        $this->sortname = $sortname;

        return $this;
    }

    /**
     * Get sortname
     *
     * @return string
     */
    public function getSortname()
    {
        return $this->sortname;
    }




    public function addState(State $state)
    {
      $this->states[] = $state;

      $state->setState($this);
      return $this;
    }

    public function removeState(State $state)
    {
      $this->states->removeState($state);
    }

    public function getStates()
    {
      return $this->states;
    }



    public function getCities(){

      $states=$this->getStates();
      $cities=array();
      $cpt=0;
      foreach ($states as $state){
        foreach ($state->getCities() as $city){
          $cities[]=$city;
        }
      }
      return $cities;
    }


}
