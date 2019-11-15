<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\TypeEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminController extends Controller
{




  public function displayHomeAdminAction()
  {

    $repositoryCity = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
    ;

    $listActiveCity=$repositoryCity
      ->getCitiesFromCountry()
      ->getQuery()
      ->getResult()
      ;


      $listTypes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:TypeEvent')
        ->findAll()
      ;

      $listCount = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:TypeEvent')
        ->findAllWithCountEvents()
      ;


      $countReq = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:RequeteFb')
        ->countReq()
      ;

      $em = $this->getDoctrine()->getManager();

      $posts = $em->getRepository('FabienEventsEngineBundle:Post')->findByDate(null,array("date"=>"DESC"));



    return $this->render('FabienEventsEngineBundle:General:admin.html.twig',array(
      "listTypes"=>$listTypes,
      "listCount"=>$listCount,
      "listActiveCity"=>$listActiveCity,
      'posts' => $posts,
      "countReq"=>$countReq

    ));
  }


  public function displayHomeAdminTypeAction(TypeEvent $type,$id)
  {

    $listEvent=array();
    $titlePage="";

    $titlePage=$type->getTitle();

    $listTypeEvent=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Event")
    ->getEventsQuery("",array($type->getId()),"","","","date_creation","DESC","","","all")
    ;

    $repositoryCity = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
    ;

    $listActiveCity=$repositoryCity
      ->getCitiesFromCountry()
      ->getQuery()
      ->getResult()
      ;

    $listTypes = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:TypeEvent')
        ->findAll()
      ;

    return $this->render('FabienEventsEngineBundle:General:adminPage.html.twig',array("listActiveCity"=>$listActiveCity,"titlePage"=>$titlePage,"listEvent"=>$listTypeEvent,'listTypes'=>$listTypes));


  }


}


?>
