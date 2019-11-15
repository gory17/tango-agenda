<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 *
 * @ORM\Table(name="parameter")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\ParameterRepository")
 */
class Parameter
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
     * @ORM\Column(name="tokenfb", type="string", length=255)
     */
    private $tokenfb;

    /**
     * @var string
     *
     * @ORM\Column(name="tokendate", type="datetime")
     */
    private $tokendate;


    public function __construct()
      {

        $this->tokendate=new \Datetime();

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
     * Set tokenfb
     *
     * @param string $tokenfb
     *
     * @return Parameter
     */
    public function setTokenfb($tokenfb)
    {
        $this->tokenfb = $tokenfb;

        return $this;
    }

    /**
     * Get tokenfb
     *
     * @return string
     */
    public function getTokenfb()
    {
        return $this->tokenfb;
    }



    /**
     * Set tokendate
     *
     * @return Parameter
     */
    public function setTokenDate($tokendate)
    {
        $this->tokendate = $tokendate;

        return $this;
    }

    /**
     * Get tokenfb
     *
     * @return string
     */
    public function getTokenDate()
    {
        return $this->tokendate;
    }


}
