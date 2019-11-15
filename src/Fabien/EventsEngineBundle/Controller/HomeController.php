<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Date;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends Controller
{


  public function jsonAction(){


    $listDates=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("",array(3,4,5,12,10),"","","","","ASC")
    ;

    $tabEvent=array();
    foreach($listDates as $date){
      $imageUrl = "";
      if($date->getEvent()->getImage()!==null){
        if($date->getEvent()->getImage()->getUrl()!==null){
          $imageUrl = "uploads/images/thumb/medium".$date->getEvent()->getImage()->getUrl();
        }
      }

      if($date->getEvent()->getCity()->getLat())$tabEvent[]=array(
        "date_id"=>$date->getId(),
        //"image"=>$imageUrl,
        //"image"=>"",
        "month"=>$date->getStart()->format("m-Y"),
        //"title"=>$date->getEvent()->getTitle(),
        //"city"=>$date->getEvent()->getCity()->getTitle(),
        //"country"=>$date->getEvent()->getCity()->getState()->getCountry()->getTitle(),
        //"start_fr"=>$date->getStart()->format("d/m/Y"),
        //"end_fr"=>$date->getEnd()->format("d/m/Y"),
        //"start_en"=>$date->getStart()->format("Y-m-d"),
        //"end_en"=>$date->getEnd()->format("Y-m-d"),
        "lat"=>$date->getEvent()->getCity()->getLat(),
        "lng"=>$date->getEvent()->getCity()->getLng(),
        "type"=>$date->getEvent()->getTypeEvent()->getSlug());

    }

    return json_encode($tabEvent);
  }


  public function createJsonAction(){
    $json=$this->jsonAction();
    $chemin=__DIR__.'/../../../../web/events.json';
    $fp = fopen($chemin, 'w');
    fwrite($fp, $json);
    fclose($fp);

      return new Response("json ok");
  }



  public function mapAction(){
    return $this->render('FabienEventsEngineBundle:General:map.html.twig');
  }


  public function searchAction(){
    $request = $this->container->get('request_stack')->getCurrentRequest();

    $typeArray=false;
    $city=false;
    $state=false;
    $country=false;
    $start=false;
    $end=false;
	   $types=array();
    $searchText="";

    $em = $this->getDoctrine()->getManager();




    if($request->request->get('city')){
      $city = $request->query->get('city');
      $repoCity = $em->getRepository('FabienEventsEngineBundle:City')
                  ->findOneById($request->request->get('city'))
          ;

        $city=$request->request->get('city');

        $state=$repoCity->getState()->getId();

        $searchText.=$repoCity->getTitle().", ";
    }


    if($request->request->get('state')){
      $repoState= $em->getRepository('FabienEventsEngineBundle:State')
                  ->findOneById($request->request->get('state'));
      $searchText.=$repoState->getTitle().", ";
      $state = $request->request->get('state');

    }



    if($request->request->get('country')){
      $repoCountry = $em->getRepository('FabienEventsEngineBundle:Country')
                  ->findOneById($request->request->get('country'))
          ;
      $country = $request->request->get('country');

      $searchText.=$repoCountry->getTitle().", ";
    }

    if($state!=false)$country='';



    if($request->request->get('type')){
      $typeArray=$request->request->get('type');

      foreach ($typeArray as $type){
        $repoType = $em->getRepository('FabienEventsEngineBundle:TypeEvent')
                    ->findOneById($type)
            ;
		    $types[]=$repoType;
        $searchText.=$repoType->getTitle().", ";
      }

    }





    if($request->request->get('start')){
      $start =$request->request->get('start');
      $start_us = implode('-', array_reverse(explode('/', $start)));
      $searchText.=" du $start";

      $start=new \DateTime($start_us);
    }


    if($request->request->get('end')){
      $end = $request->request->get('end');

      $end_us = implode('-', array_reverse(explode('/', $end)));
      $searchText.=" au $end, ";

      $end=new \DateTime($end_us." +1day");
    }



    //gère les raccourcis qui envoient au différentes parties du site
    if(!$request->request->get('end') and !$request->request->get('end')){
      //pas de période

        if($request->request->get('country') and !$request->request->get('city')){
          if(in_array(1,$typeArray) and in_array(2,$typeArray) and in_array(3,$typeArray) and in_array(5,$typeArray) and in_array(8,$typeArray) and in_array(9,$typeArray) and in_array(10,$typeArray) and in_array(12,$typeArray) and in_array(13,$typeArray)  ){
            //affiche le pays
            return $this->redirectToRoute('fabien_events_coutry_listcities', array('slug' => $repoCountry->getSlug()));
          }
        }


        if(in_array(1,$typeArray) and in_array(8,$typeArray) ){
          if($request->request->get('country') and $request->request->get('city')){
            //affiche la ville
            return $this->redirectToRoute('fabien_events_city_slug', array('slug' => $repoCity->getSlug()));
          }
        }



    }



    $searchText=substr($searchText,0,-2);

    $dates = $em->getRepository('FabienEventsEngineBundle:Date')
                  ->getDatesQuery("",$typeArray,false,$state,$country,$orderField="d.start",$order="ASC",150,0,1,false,$start,$end)
                  ;


    return $this->render('FabienEventsEngineBundle:General:search.html.twig',array("dates"=>$dates,"searchText"=>$searchText,"types"=>$types));

  }


  public function displayHomeAction(){
    $em = $this->getDoctrine()->getManager();



    $posts = $em->getRepository('FabienEventsEngineBundle:Post')->findBy(array("publish"=>1),array('date'=>"DESC"),7);

    $countEvents=$em->getRepository('FabienEventsEngineBundle:Event')->countTotalEvents();

    $countAddedToday=$em->getRepository('FabienEventsEngineBundle:Event')->countAddedToday();

    $countEventsToday=$em->getRepository('FabienEventsEngineBundle:Event')->countEventsToday();

    $lastVideos=$em->getRepository('FabienEventsEngineBundle:Video')->lastVideos(3);

    $listMaestros=$em->getRepository("FabienEventsEngineBundle:Person")->getPersonByVideosSemaine();



    $listDatesBigEvents=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("",array(3,4,5,12,10),"","","","","ASC")
    ;

    $countBigEvents=count($listDatesBigEvents);

    /*
    $inscDates=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("","","","","","e.dateInscription","ASC",8,0);
    ;
    */
    $inscDates="";

    $lastEvents=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("",array(3,4,5,12),"","","","e.date_creation","DESC",12)
    ;


    /*
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
    */

    /*
    $countries=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Country')
    ->getCountryEventsResume()
    ;

    $citiesActive=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:City')
    ->getTopCities(40)
    ;
    */

    $listTypeEvt=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:TypeEvent')
    ->findAll()
    ;


    //return $this->render('FabienEventsEngineBundle:General:home.html.twig',array('posts'=>$posts,"countEvents"=>$countEvents,"countAddedToday"=>$countAddedToday,"countEventsToday"=>$countEventsToday,"countBigEvents"=>$countBigEvents,"inscDates"=>$inscDates,'lastEvents'=>$lastEvents,"valoEvents"=>$valoEvents,'countPosts'=>$countPosts,"countries"=>$countries,"citiesActive"=>$citiesActive,"listTypeEvt"=>$listTypeEvt,"listMaestros"=>$listMaestros,"lastVideos" => $lastVideos));
    return $this->render('FabienEventsEngineBundle:General:home.html.twig',array("countEvents"=>$countEvents,"countAddedToday"=>$countAddedToday,"countEventsToday"=>$countEventsToday,"countBigEvents"=>$countBigEvents,'lastEvents'=>$lastEvents,"listTypeEvt"=>$listTypeEvt,"listMaestros"=>$listMaestros,"lastVideos" => $lastVideos));

  }

  public function getMenuAction(Request $request,$mode="")
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

    $listBlog=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Post')
    ->listPublic()
    ;

    $listTopCities=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:City')
    ->getTopCities()
    ;

    $listCountry=$this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Country')
    ->getAllActiveCountry()
    ;

    $currentUrl= $_SERVER['REQUEST_URI'];

    if($currentUrl=="/"){
      $currentUrl.="fr";
    }

    if($mode=="amp"){
      return $this->render('FabienEventsEngineBundle:General:menu-amp.html.twig',array("listBlog"=>$listBlog,"listTypeEvt"=>$listTypeEvt,"listCountryStages"=>$listCountryStages,"listTopCities"=>$listTopCities,"currentUrl"=>$currentUrl,"listCountry"=>$listCountry));
    }else{
      return $this->render('FabienEventsEngineBundle:General:menu.html.twig',array("listBlog"=>$listBlog,"listTypeEvt"=>$listTypeEvt,"listCountryStages"=>$listCountryStages,"listTopCities"=>$listTopCities,"currentUrl"=>$currentUrl,"listCountry"=>$listCountry));
    }
  }

  public function createSiteMapAction($typesitemap){

    $elements=array();

    if($typesitemap == "general"){
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"");
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"blog-tango");
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"tango-videos");
      $elements[]=array("changefreq"=>"daily","priority"=>1,"url"=>"tango-maestro");

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



    if($typesitemap == "maestros"){
      $maestros=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Person')
      ->findAll()
      ;

      foreach ($maestros as $maestro) {
          $elements[]=array("changefreq"=>"daily","priority"=>"0.9","url"=>"tango-maestro"."/".$maestro->getSlug());
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
