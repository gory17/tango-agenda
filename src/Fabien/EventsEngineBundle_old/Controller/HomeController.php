<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Date;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{

  public function displayHomeAction(){
    $em = $this->getDoctrine()->getManager();

    $posts = $em->getRepository('FabienEventsEngineBundle:Post')->findBy(array("publish"=>1),array('date'=>"DESC"),3);

    $countEvents=$em->getRepository('FabienEventsEngineBundle:Event')->countTotalEvents();

    $inscDates=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("","","","","","e.dateInscription","ASC",8,0);
    ;

    $lastEvents=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("",array(3,4,5,12),"","","","e.date_creation","DESC",8)
    ;

    $valoEvents=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("","","","","","e.date_creation","DESC",4,"",1,1)
    ;

    $countPosts = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Post')
      ->countPosts()
    ;

    $countries=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Country')
    ->getCountryEventsResume()
    ;

    $citiesActive=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:City')
    ->getTopCities(60)
    ;

    $listTypeEvt=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:TypeEvent')
    ->findAll()
    ;


    return $this->render('FabienEventsEngineBundle:General:home.html.twig',array('posts'=>$posts,"countEvents"=>$countEvents,"inscDates"=>$inscDates,'lastEvents'=>$lastEvents,"valoEvents"=>$valoEvents,'countPosts'=>$countPosts,"countries"=>$countries,"citiesActive"=>$citiesActive,"listTypeEvt"=>$listTypeEvt));
  }

  public function getMenuAction(Request $request)
  {

    $listCountryStages=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Country')
    ->getAllActiveCountry(2)
    ;

    $listTypeEvt=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:TypeEvent')
    ->getTypeWithEvent()
    ;

    $listTopCities=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:City')
    ->getTopCities()
    ;

    $currentUrl= $_SERVER['REQUEST_URI'];

    if($currentUrl=="/"){
      $currentUrl.="fr";
    }

    return $this->render('FabienEventsEngineBundle:General:menu.html.twig',array("listTypeEvt"=>$listTypeEvt,"listCountryStages"=>$listCountryStages,"listTopCities"=>$listTopCities,"currentUrl"=>$currentUrl));
  }

  public function createSiteMapAction($typesitemap){

    $elements=array();

    if($typesitemap == "general"){
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"");
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"blog-tango");

      $listTypeEvt=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:TypeEvent')
      ->getTypeWithEvent()
      ;

      foreach ($listTypeEvt as $typeEvt) {
          $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"category/".$typeEvt['slug']);
      }

    }


    if($typesitemap == "articles"){
      $articles=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Post')
      ->findByPublish(1)
      ;


      foreach ($articles as $article) {
          $elements[]=array("changefreq"=>"weekly","priority"=>1,"url"=>"blog-tango/".$article->getSlug());
      }

    }


    if($typesitemap == "cities"){
      $cities=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
      ->getAllActiveCity()
      ;

      foreach ($cities as $city) {
          $elements[]=array("changefreq"=>"daily","priority"=>"0.9","url"=>"city"."/".$city->getSlug());
      }
    }



    if($typesitemap == "countries"){
      $countries=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Country')
      ->getCountryEventsResume()
      ;

      foreach ($countries as $country) {
          $elements[]=array("changefreq"=>"daily","priority"=>"0.9","url"=>"country/".$country['slug']);
      }
    }

    $response = new Response($this->renderView('FabienEventsEngineBundle:General:sitemap.html.twig',array("elements"=>$elements)));
    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
    return $response;

  }


}


?>
