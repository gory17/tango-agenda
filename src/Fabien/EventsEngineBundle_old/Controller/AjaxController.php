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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{

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
        'dates' => $dates,'period'=>"week","startCounter"=>$startDays,"start_date"=>$startDate,"end_date"=>$endDate,"coursCity"=>$coursCity
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


}


?>
