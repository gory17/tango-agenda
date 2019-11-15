<?php
namespace Fabien\EventsEngineBundle\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('exist', array($this, 'existFilter')),
        );
    }

    public function existFilter($url)
    {

      //$path=$_SERVER['PHP_SELF'];

       $chemin=$url;
       //echo $chemin;die();
        if(file_exists($chemin)){
          $result = "oui";
        }else{
          $result =  "non";
        }

        return $result;
    }
}

?>
