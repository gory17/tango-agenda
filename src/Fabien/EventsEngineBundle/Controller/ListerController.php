<?php
namespace Fabien\EventsEngineBundle\Controller;


use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Form\EventType;
use Fabien\EventsEngineBundle\Entity\Image;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\State;
use Fabien\EventsEngineBundle\Entity\Date;

use Fabien\EventsEngineBundle\Controller\FabienFbApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class ListerController extends Controller

{


    public function listEvents(){

      $listEvents=$this->getDoctrine()->getManager()
      ->getRepository('FabienEventsEngineBundle:Event')
      ->findAll();

      return $this->render("FabienEventsEngineBundle:Events:list.html.twig",array("listEvents"=>$listEvents));
    }




    public function bigEventsAction(){
      $listEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Event")
      ->getEventsQuery("",array(3,4,5))
      ;

      return $this->render("FabienEventsEngineBundle:Events:bigeventslisthome.html.twig",array("listEvents"=>$listEvents));
    }



    public function bigEventsListAction($slug,$amp="")
    {

      $typeEvt=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:TypeEvent")
      ->findOneBySlug($slug)
      ;

      $listDates=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array($typeEvt->getId()),"","","","","ASC")
      ;


      $lastDatesEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array($typeEvt->getId()),"","","","e.valorisation,e.date_creation","DESC")
      ;


      $countryDispo=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Country")
      ->getAllActiveCountry($typeEvt)
      ;

      $inscDates=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array($typeEvt->getId()),"","","","e.dateInscription","ASC")
      ;

      if($amp=="amp"){
        return $this->render("FabienEventsEngineBundle:Lister:bigeventslist-amp.html.twig",array("typeEvt"=>$typeEvt,"listDates"=>$listDates,"lastDatesEvents"=>$lastDatesEvents,"inscDates"=>$inscDates,"countryDispo"=>$countryDispo));
      }else{
        return $this->render("FabienEventsEngineBundle:Lister:bigeventslist.html.twig",array("typeEvt"=>$typeEvt,"listDates"=>$listDates,"lastDatesEvents"=>$lastDatesEvents,"inscDates"=>$inscDates,"countryDispo"=>$countryDispo));

      }
    }


    public function stagesListAction($location){

      $cible="";

      if($location!=""){
        $cible=$this->getDoctrine()
        ->getManager()
        ->getRepository("FabienEventsEngineBundle:Country")
        ->findOneBySlug($location)
        ;
      }

      $listDatesEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array(2),"","",$cible,"","ASC")
      ;

      $lastDatesEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array(2),"","",$cible,"e.valorisation,e.date_creation","DESC")
      ;


      return $this->render("FabienEventsEngineBundle:Lister:stagescountrylist.html.twig",array("listDatesEvents"=>$listDatesEvents,"country"=>$cible,'lastDatesEvents'=>$lastDatesEvents));

    }


}
