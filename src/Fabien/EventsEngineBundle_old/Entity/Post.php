<?php

namespace Fabien\EventsEngineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\PostRepository")
 */
class Post
{


      /**
       * @Assert\Image(
       *     maxSize="3M",
       *  maxSizeMessage="Image trop volumineuse (max 3Mo)"
       * )
       */
      private $file;

      private $tempFilename;

      public function getFile()
      {
        return $this->file;
      }

      public function setFile(UploadedFile $file = null)
      {
        $this->file = $file;
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
     * @ORM\ManyToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Image", cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="Fabien\EventsEngineBundle\Entity\CategoryPost", cascade={"persist"})
     */
    private $categories_post;

    /**
     * @ORM\ManyToOne(targetEntity="Fabien\EventsEngineBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_default_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private $imageDefault;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(name="publish", type="boolean",nullable=true)
     */
    private $publish;

    /**
     * @var string
     *
     * @ORM\Column(name="title_trad", type="string", length=255,nullable=true)
     */
    private $titleTrad;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="text", nullable=true)
     */
    private $header;


    /**
     * @var string
     *
     * @ORM\Column(name="header_trad", type="text", nullable=true)
     */
    private $headerTrad;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="content_trad", type="text", nullable=true)
     */
    private $contentTrad;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_author", type="string", length=255)
     */
    private $mailAuthor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    public function __construct()
    {
      $this->date = new \Datetime();
      $this->images = new ArrayCollection();
      $this->categories_post = new ArrayCollection();
    }

    // Notez le singulier, on ajoute une seule catégorie à la fois
     public function addImage(Image $image)
     {
       // Ici, on utilise l'ArrayCollection vraiment comme un tableau
       $this->images[] = $image;

       return $this;
     }

     public function removeImage(Image $image)
     {
       // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
       $this->images->removeElement($image);
     }

     // Notez le pluriel, on récupère une liste de catégories ici !
     public function getImages()
     {
       return $this->images;
     }


     /**
      * Set imageDefault
      *
      * @param \Fabien\EventsEngineBundle\Entity\Image $mageDefault
      *
      * @return Post
      */
     public function setImageDefault(\Fabien\EventsEngineBundle\Entity\Image $imageDefault)
     {
         $this->imageDefault = $imageDefault;

         return $this;
     }

     /**
      * Get image
      *
      * @return \Fabien\EventsEngineBundle\Entity\Image
      */
     public function getImageDefault()
     {
         return $this->imageDefault;
     }



     // Notez le singulier, on ajoute une seule catégorie à la fois
      public function addCategoryPost(CategoryPost $CategoryPost)
      {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau
        $this->categories_post[] = $CategoryPost;

        return $this;
      }

      public function removeCategoryPost(CategoryPost $CategoryPost)
      {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
        $this->categories_post->removeElement($CategoryPost);
      }

      // Notez le pluriel, on récupère une liste de catégories ici !
      public function getCategoriesPost()
      {
        return $this->categories_post;
      }

      public function getCategoryPost()
      {
        return $this->categories_post;
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
     * @return Post
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
     * Set publish
     *
     * @param boolean $publish
     *
     * @return Post
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return bool
     */
    public function getPublish()
    {
        return $this->publish;
    }



    /**
     * Set titleTrad
     *
     * @param string $titleTrad
     *
     * @return Post
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
     * @return Post
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
     * Set header
     *
     * @param string $header
     *
     * @return Post
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }



    /**
     * Set headerTrad
     *
     * @param string $headerTrad
     *
     * @return Post
     */
    public function setHeaderTrad($headerTrad)
    {
        $this->headerTrad = $headerTrad;

        return $this;
    }

    /**
     * Get headerTrad
     *
     * @return string
     */
    public function getHeaderTrad()
    {
        return $this->headerTrad;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }



    /**
     * Set contentTrad
     *
     * @param string $contentTrad
     *
     * @return Post
     */
    public function setContentTrad($contentTrad)
    {
        $this->contentTrad = $contentTrad;

        return $this;
    }

    /**
     * Get contentTrad
     *
     * @return string
     */
    public function getContentTrad()
    {
        return $this->contentTrad;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Post
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set mailAuthor
     *
     * @param string $mailAuthor
     *
     * @return Post
     */
    public function setMailAuthor($mailAuthor)
    {
        $this->mailAuthor = $mailAuthor;

        return $this;
    }

    /**
     * Get mailAuthor
     *
     * @return string
     */
    public function getMailAuthor()
    {
        return $this->mailAuthor;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Post
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
