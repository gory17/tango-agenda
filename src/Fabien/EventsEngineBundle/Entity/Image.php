<?php

namespace Fabien\EventsEngineBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;


use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="Fabien\EventsEngineBundle\Repository\ImageRepository")
* @ORM\HasLifecycleCallbacks
 */
class Image
{

  private $file;

  private $tempFilename;


  /**
   * @ORM\OneToMany(targetEntity="Fabien\EventsEngineBundle\Entity\Event", mappedBy="image")
   */
  private $events;

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
     * @ORM\Column(name="legend", type="string", length=255, nullable=true)
     */
    private $legend;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;


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
     * Set legend
     *
     * @param string $legend
     *
     * @return Image
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get legend
     *
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $year=date("Y");
        $month=date("m");
        $this->url = "$year/$month/$url";

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
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }


    /**
     * Set genderBalance
     *
     * @param boolean $principal
     *
     * @return Image
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Get genderBalance
     *
     * @return bool
     */
    public function getPrincipal()
    {
        return $this->principal;
    }





    public function upload($file)
    {


      // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
      if (null === $file) {
        return;
      }

      // On récupère le nom original du fichier de l'internaute
      $name = md5(uniqid()).".".$file->getClientOriginalExtension();

      $year=date("Y");
      $month=date("m");


      $testDirYear=$this->getUploadRootDir().$this->getUploadDir()."/$year";
      $testDirThumbYear=$this->getUploadRootDir().$this->getUploadThumbDir()."/$year";
      $testDirMonth=$this->getUploadRootDir().$this->getUploadDir()."/$year/$month";
      $testDirThumbMonth=$this->getUploadRootDir().$this->getUploadThumbDir()."/$year/$month";
      $testDirThumbMonthSmall=$this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year/$month";
      $testDirThumbMonthMedium=$this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year/$month";
      $testDirThumbYearSmall=$this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year";
      $testDirThumbYearMedium=$this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year";


      if(file_exists($testDirYear)==False && is_dir($testDirYear)==False){
        mkdir($testDirYear);
      }

      if(file_exists($testDirThumbYear)==False && is_dir($testDirThumbYear)==False){
        mkdir($testDirThumbYear);
      }


      if(file_exists($testDirMonth)==False && is_dir($testDirMonth)==False){
        mkdir($testDirMonth);
      }


      if(file_exists($testDirThumbYearSmall)==False){
        mkdir($testDirThumbYearSmall);
      }

      if(file_exists($testDirThumbYearMedium)==False){
        mkdir($testDirThumbYearMedium);
      }

      if(file_exists($testDirThumbMonthSmall)==False){
        mkdir($testDirThumbMonthSmall);
      }

      if(file_exists($testDirThumbMonthMedium)==False){
        mkdir($testDirThumbMonthMedium);
      }

      // On déplace le fichier envoyé dans le répertoire de notre choix
      $file->move($this->getUploadRootDir().$this->getUploadDir()."/$year/$month", $name);
      //$this->createThumbnail($this->getUploadRootDir().$this->getUploadDir()."/$year/$month".$name , $this->getUploadRootDir().$this->getUploadDir()."/$year/$month"."/$year/$month/".$name,600,450);


      // On sauvegarde le nom de fichier dans notre attribut $url
      $this->url = $name;

      $this->createThumbnail($this->getUploadRootDir().$this->getUploadDir()."/$year/$month/".$name , $this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year/$month/".$name,250,160);
      $this->createThumbnail($this->getUploadRootDir().$this->getUploadDir()."/$year/$month/".$name , $this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year/$month/".$name,600,450);


      return $name;
    }


    public function saveFb($url){
      $urlClear = strtok($url, '?');
      $type = strtolower(substr(strrchr($urlClear,"."),1));

      $name = md5(uniqid()).".".$type;

      $year=date("Y");
      $month=date("m");


      $testDirYear=$this->getUploadRootDir().$this->getUploadDir()."/$year";
      $testDirThumbYear=$this->getUploadRootDir().$this->getUploadThumbDir()."/$year";
      $testDirMonth=$this->getUploadRootDir().$this->getUploadDir()."/$year/$month";
      $testDirThumbMonth=$this->getUploadRootDir().$this->getUploadThumbDir()."/$year/$month";
      $testDirThumbMonthSmall=$this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year/$month";
      $testDirThumbMonthMedium=$this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year/$month";
      $testDirThumbYearSmall=$this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year";
      $testDirThumbYearMedium=$this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year";


      if(file_exists($testDirYear)==False && is_dir($testDirYear)==False){
        mkdir($testDirYear);
      }

      if(file_exists($testDirThumbYear)==False && is_dir($testDirThumbYear)==False){
        mkdir($testDirThumbYear);
      }


      if(file_exists($testDirMonth)==False && is_dir($testDirMonth)==False){
        mkdir($testDirMonth);
      }


      if(file_exists($testDirThumbYearSmall)==False){
        mkdir($testDirThumbYearSmall);
      }

      if(file_exists($testDirThumbYearMedium)==False){
        mkdir($testDirThumbYearMedium);
      }

      if(file_exists($testDirThumbMonthSmall)==False){
        mkdir($testDirThumbMonthSmall);
      }

      if(file_exists($testDirThumbMonthMedium)==False){
        mkdir($testDirThumbMonthMedium);
      }

      $chemin=$this->getUploadRootDir().$this->getUploadDir()."/$year/$month/".$name;
      if($url){
        copy($url, $chemin);

        $this->createThumbnail($chemin, $this->getUploadRootDir().$this->getUploadThumbDir()."/small/$year/$month/".$name,250,160);
        $this->createThumbnail($chemin, $this->getUploadRootDir().$this->getUploadThumbDir()."/medium/$year/$month/".$name,600,450);

        $this->setUrl($name);

        return $name;
      }else{
        return false;
      }
    }


    function createThumbnail($src, $dst, $width, $height, $crop=0){

      if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

      $type = strtolower(substr(strrchr($src,"."),1));
      if($type == 'jpeg') $type = 'jpg';
      switch($type){
        case 'bmp': $img = imagecreatefromwbmp($src); break;
        case 'gif': $img = imagecreatefromgif($src); break;
        case 'jpg': $img = imagecreatefromjpeg($src); break;
        case 'png': $img = imagecreatefrompng($src); break;
        default : return "Unsupported picture type!";
      }

      // resize
      if($crop){
        if($w < $width or $h < $height) return "Picture is too small!";
        $ratio = max($width/$w, $height/$h);
        $h = $height / $ratio;
        $x = ($w - $width / $ratio) / 2;
        $w = $width / $ratio;
      }
      else{
        if($w < $width and $h < $height) return "Picture is too small!";
        $ratio = min($width/$w, $height/$h);
        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;
      }

      $new = imagecreatetruecolor($width, $height);

      // preserve transparency
      if($type == "gif" or $type == "png"){
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
      }

      imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);


      switch($type){
        case 'bmp': imagewbmp($new, $dst); break;
        case 'gif': imagegif($new, $dst); break;
        case 'jpg': imagejpeg($new, $dst); break;
        case 'png': imagepng($new, $dst); break;
      }
      return true;
    }



    public function getUploadDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'uploads/images';
    }

    public function getUploadThumbDir()
    {
      // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
      return 'uploads/images/thumb';
    }

    public function getUploadRootDir()
    {
      // On retourne le chemin relatif vers l'image pour notre code PHP
      return __DIR__.'/../../../../web/';
    }




    public function addEvent(Event $event)
    {
      $this->events[] = $event;

      $event->setImage($this);
      return $this;
    }

    public function removeEvent(Event $event)
    {
      $this->events->removeState($event);
    }

    public function getEvents()
    {
      return $this->events;
    }

}
