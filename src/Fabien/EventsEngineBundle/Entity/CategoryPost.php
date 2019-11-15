<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryPost
 *
 * @ORM\Table(name="categorypost")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\CategoryPostRepository")
 */
class CategoryPost
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
     * @ORM\Column(name="titletrad", type="string", length=255, nullable=true)
     */
    private $titleTrad;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=true)
     */
    private $rank;


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
     * @return CategoryPost
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
     * Set slug
     *
     * @param string $slug
     *
     * @return CategoryPost
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
     * Set rank
     *
     * @param integer $rank
     *
     * @return CategoryPost
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }
}
