<?php
namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\Date;
use Fabien\EventsEngineBundle\Form\EventType;
use Fabien\EventsEngineBundle\Entity\Image;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\GroupFb;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{



  public function getInfosMarkerAction(){

    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $id = $request->request->get('id');

        $marker = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:Date')
          ->findOneById($id)
        ;

        if($marker->getEvent()->getImage()){
          $imageMap="<p><a href='./date/".$marker->getId()."' ><img height='250px' width='auto' src='../../uploads/images/thumb/medium/".$marker->getEvent()->getImage()->getUrl()."' /></a></p>";
        }else{
          $imageMap='';
        }

        $urlDate="<a href='./date/".$marker->getId()."' class='btn btn-primary'>Infos</a> ";


        $display = "<div id='content'>
              <div id='siteNotice'>
              </div>
              <div class='imgload'>$imageMap</div>
              <p class='typemap'>".$marker->getEvent()->getTypeEvent()->getTitleTrad()."</p>
              <p  class='titleMap'>".$marker->getEvent()->getTitle()."</p>
              <div id='bodyContent'>
              <p class='datemap'>".$marker->getStart()->format('d/m/Y')." > ".$marker->getEnd()->format('d/m/Y')."</p>
              <p class='countrymap'>".$marker->getEvent()->getCity()->getState()->getCountry()->getTitle().", ".$marker->getEvent()->getCity()->getTitle()."</p>

              <p>$urlDate</p>
              </div>
              </div>";


        return new response($display);
      }
    else{
      return new response("Loading faillure");
    }


  }


  public function setTypeVideoAction(){
    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $type = $request->request->get('type');
        $id = $request->request->get('id');

        $video = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:Video')
          ->findOneById($id)
        ;

        $video->setType($type);

        $em = $this->getDoctrine()->getManager();
        $em->persist($video);
        $em->flush($video);

      }


      return new response("ok");
 }



  public function listCityFromCountryAction()
  {
    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $id = $request->request->get('id');

        $repositoryCity = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:City')
          ->getCitiesFromCountry($id)
          ->getQuery()
          ->getResult()
        ;

        if($request->request->get('mode')){
          if($request->request->get('mode')=="active"){
            $repositoryCity = $this
              ->getDoctrine()
              ->getManager()
              ->getRepository('FabienEventsEngineBundle:City')
              ->getCitiesFromCountry($id,"active")
              ->getQuery()
              ->getResult()
            ;
          }
        }


        $listCities=$repositoryCity;


        $arrayCities=array();
        foreach ($listCities as $city){
          $arrayCities[]=array("id"=>$city->getId(),"title"=>$city->getTitle());
        }


        return new JsonResponse($arrayCities);
      }
    else{
      return new response("Loading faillure");
    }
  }


  public function findCityAction()
  {
    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $cityString = $request->request->get('query');

        $repositoryCity = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:City')
          ->findCity($cityString)
          ->getQuery()
          ->getResult()
        ;


        $listCities=$repositoryCity;


        $arrayCities=array();
        foreach ($listCities as $city){
          $arrayCities[]=array("value"=>$city->getTitle()." (".$city->getState()->getCountry()->getTitle().")","data"=>$city->getId());
        }

        $arrayResult=array("query"=>"Unit","suggestions"=>$arrayCities);

        return new JsonResponse($arrayResult);
      }
    else{
      return new response("Loading faillure");
    }
  }



  public function findCityMenuAction()
  {
    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $cityString = $request->request->get('query');

        $repositoryCity = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:City')
          ->findCity($cityString)
          ->getQuery()
          ->getResult()
        ;

        $listCities=$repositoryCity;


        $arrayCities=array();
        foreach ($listCities as $city){
          if($city->getSlug()!="")$arrayCities[]=array("value"=>$city->getTitle()." (".$city->getState()->getCountry()->getTitle().")","data"=>$this->generateUrl('fabien_events_city_slug',array("slug"=>$city->getSlug())));
        }

        $arrayResult=array("query"=>"Unit","suggestions"=>$arrayCities);

        return new JsonResponse($arrayResult);
      }
    else{
      return new response("Loading faillure");
    }
  }





  public function ChangeCityAction()
  {
    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

        $id_city = $request->request->get('id_city');
        $id_event = $request->request->get('id_event');

        $Event = $this->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Event')
        ->findOneById($id_event);

        $City = $this->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:City')
        ->findOneById($id_city);

        $Event->setCity($City);

        $targetState=$City->getState();
        $targetCountry=$targetState->getCountry();
        $Event->setCountry($targetCountry);


        $em = $this->getDoctrine()->getManager();
        $em->persist($Event);
        $em->flush($Event);

        return new response("ok");
      }
    else{
      return new response("Loading faillure");
    }
  }


public function moreVideosAction(){
  $request = $this->container->get('request_stack')->getCurrentRequest();

  if($request->isXmlHttpRequest()){
    $person=$request->request->get('personId');
    $offset=$request->request->get('offset');

    $videos=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Video")
    ->getVideos($person,12,$offset);

    return $this->render('FabienEventsEngineBundle:video:listItems.html.twig', array(
      "videos"=>$videos
    ));
  }else{
      return new response("Loading failure");
  }
}


public function moreDatesAction(){
  $request = $this->container->get('request_stack')->getCurrentRequest();

  if($request->isXmlHttpRequest()){
    $cityId=$request->request->get('cityId');
    $startDays=$request->request->get('startDays');

    $city = $this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:City')
    ->findOneById($cityId);

    $repoDates = $this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Date');


    $dureePeriod=30;
    $endDays=$startDays+$dureePeriod;

    $startDate=new \DateTime("now +$startDays Days");
    $endDate=new \DateTime("now +$endDays Days");


    $dates=$repoDates->getDatesQuery("",array(1,2,6,7,9,10,13,14),"",$city->getState(),'','','','','',1,0,$startDate,$endDate);
    $coursCity=$repoDates->getDatesQuery("",array(8),"",$city->getState(),'','','','','',1,0,$startDate,$endDate);

    return $this->render('FabienEventsEngineBundle:Dates:List.html.twig', array(
        "city"=>$city,'dates' => $dates,'period'=>"week","startCounter"=>$startDays,"start_date"=>$startDate,"end_date"=>$endDate,"coursCity"=>$coursCity,"lazyload"=>false
    ));

  }else{
      return new response("Loading failure");
  }
}



public function ChangeTypeAction()
{
  $request = $this->container->get('request_stack')->getCurrentRequest();

  if($request->isXmlHttpRequest()){
      $id_type = $request->request->get('id_type');

      $Type = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:TypeEvent')
      ->findOneById($id_type);

      $id_event = $request->request->get('id_event');

      $Event = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Event')
      ->findOneById($id_event);

      $Event->setTypeEvent($Type);

      $em = $this->getDoctrine()->getManager();
      $em->persist($Event);
      $em->flush($Event);
      return new response("Ok");
  }
  else{
    return new response("Loading faillure");
  }
}

public function changeDateInscAction(){

  $request = $this->container->get('request_stack')->getCurrentRequest();

  if($request->isXmlHttpRequest()){
      $dateEvt = $request->request->get('dateinsc');


      // $tabDate = explode('/' , $dateEvt);
      // $enDate  = $tabDate[2].'-'.$tabDate[1].'-'.$tabDate[0];

      $enDate=new \DateTime($dateEvt);

      $id_event = $request->request->get('id_event');

      $Event = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Event')
      ->findOneById($id_event);

      $Event->setDateInscription($enDate);

      $em = $this->getDoctrine()->getManager();
      $em->persist($Event);
      $em->flush($Event);
      return new response("Ok");
  }
  else{
    return new response("Loading faillure");
  }

}



public function findGroupsAction(){
  $request = $this->container->get('request_stack')->getCurrentRequest();

  $reponse="";

  if($request->isXmlHttpRequest()){
      $requete = $request->request->get('requete');

      $accessToken= "EAACwhIfUxt8BANZBfGgLDnZCxMu6tFthvLwpkXVZAYc6uL80Uc26VeUPlwTvdjZAbTD2B50uZAtC49D5fCGL7er0p3ySY83uBi1v00C17lAxfZCtuiGBhiyaCofQtdRUhCW8ZAKGpnCZCVWV6EZASS8Mp3l18RsOcpTVk3yQjefURQwZDZD";

      $appId="194083261040351";
      $appSecret='bb0a35d5a7265c92e18f181588069359';
      $graphVersion='v2.10';

      $param = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Parameter')
      ->findOneById(1);

      $accessToken=$param->getTokenfb();

      $groupsSaved=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:GroupFb')
      ->findAll();

      $listIdGroup=array();
      foreach($groupsSaved as $groupeSave){
        $listIdGroup[]=$groupeSave->getFbId();
      }


      $userTokenFB=$param->getTokenfb();

      $fb = new \Facebook\Facebook([
      'app_id' => $appId,
      'app_secret' => $appSecret,
      'default_graph_version' => $graphVersion,
      ]);

      $fb->setDefaultAccessToken($accessToken);

			try {
			 		$responseGroupe = $fb->get("search?q=$requete&type=group",$userTokenFB);
          $responsePage = $fb->get("search?q=$requete&type=page",$userTokenFB);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
	 		  return 'Graph returned an error: ' . $e->getMessage();
	 		  exit;
	 		} catch(Facebook\Exceptions\FacebookSDKException $e) {
	 		  return 'Facebook SDK returned an error: ' . $e->getMessage();
	 		  exit;
	 		}catch(Facebook\Exceptions\FacebookAuthenticationException $e){
				return "Probleme d'authentification au niveau du groupe" . $e->getMessage();
				exit;
			}catch(Facebook\Exceptions\FacebookAuthorizationException $e){
				return "Probleme d'authorisation au niveau du groupe" . $e->getMessage();
				exit;
			}


      $graphEdge = $responseGroupe->getGraphEdge();

      $reponse.="<div class='col-md-6'>";
  		for($cpt=0;$cpt<=10;$cpt++){
  			if($graphEdge){
  				foreach($graphEdge as $graphNode){

            if($graphNode['privacy']=="OPEN" and !in_array($graphNode['id'],$listIdGroup) ){
  					       $reponse.="<p><a href='#' class='btn btn-primary addGroup' data-id='".$graphNode['id']."' data-name='".$graphNode['name']."' ><span class='glyphicon glyphicon-plus'></span></a> <a href='http://www.facebook.com/".$graphNode['id']."' target='_blank'>".$graphNode['name']."</a></p>";
            }
          }


          try {
            $graphEdge = $fb->next($graphEdge);
          }catch (Exception $e) {
            exit;
          }
        }
  		}
      $reponse.="</div>";

      $graphEdge = $responsePage->getGraphEdge();

      $reponse.="<div class='col-md-6'>";
  		for($cpt=0;$cpt<=10;$cpt++){
  			if($graphEdge){
  				foreach($graphEdge as $graphNode){
            if( !in_array($graphNode['id'],$listIdGroup) ){
  					       $reponse.="<p><a href='#' class='btn btn-primary addGroup' data-id='".$graphNode['id']."' data-name='".$graphNode['name']."' ><span class='glyphicon glyphicon-plus'></span></a> <a href='http://www.facebook.com/".$graphNode['id']."' target='_blank'>".$graphNode['name']."</a></p>";
            }
          }


          try {
            $graphEdge = $fb->next($graphEdge);
          }catch (Exception $e) {
            exit;
          }
        }
  		}
      $reponse.="</div>";

      return new response($reponse);

  }
  else{
    return new response("Loading faillure");
  }
}



public function addGroupAction(){
  $request = $this->container->get('request_stack')->getCurrentRequest();

  $reponse="";

  if($request->isXmlHttpRequest()){
      $id = $request->request->get('id');
      $name=  $request->request->get('name');
      $url="https://facebook.com/$id";

      $groupe=new GroupFb();
      $groupe->setTitle($name);
      $groupe->setUrl($url);
      $groupe->setFbId($id);

      $em = $this->getDoctrine()->getManager();
      $em->persist($groupe);
      $em->flush();

      return new response("groupe ajouté");
  }

}

public function cityUpdateCoordAction(){
  $request = $this->container->get('request_stack')->getCurrentRequest();

  $reponse="";

  if($request->isXmlHttpRequest()){
      $id = $request->request->get('id');
      $lat=  $request->request->get('lat');
      $lng=  $request->request->get('lng');

      $city = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:City')
        ->findOneById($id)
      ;

      if($city->getLat()==""){
        $city->setLat($lat);
        $city->setLng($lng);

        $em = $this->getDoctrine()->getManager();
        $em->persist($city);
        $em->flush();

        return new response("City mise a jour");
      }else{
        return new response("City deja ok");
      }

  }

}



}


?>
