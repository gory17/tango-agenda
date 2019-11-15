<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\TypeEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\JsonResponse;

class CityController extends Controller
{
    public function indexAction()
    {
        return $this->render('FabienEventsEngineBundle:Default:index.html.twig');
    }


    public function viewSlugAction(City $city,$slug,$period=30)
    {
      $repoDates = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Date')
      ;


        //$urlImageCity="http://localhost".$this->get('assets.packages')->getUrl("/img/villes/").str_replace("-fr","",$city->getSlug()).".jpg";
        $urlImageCity=__DIR__.'/../../../../web/'."img/villes/".str_replace("-fr","",$city->getSlug()).".jpg";


        if(!file_exists($urlImageCity)){
          $urlImageCity=$this->get('assets.packages')->getUrl("/img/villes/")."default.jpg";
        }else{
          $urlImageCity=$this->get('assets.packages')->getUrl("/img/villes/").str_replace("-fr","",$city->getSlug()).".jpg";
        }


        $dureePeriod=250;
        $endDays=$dureePeriod;

        $startDate=new \DateTime("now");
        $endDate=new \DateTime("now +$endDays Days");

        $datesListImportant=$repoDates->getDatesQuery("",array(3,4,5),"all",$city->getState(),'','','','','',1,0,$startDate,$endDate);


        $dureePeriod=200;
        $endDays=$dureePeriod;
        $startDate=new \DateTime("now");
        $endDate=new \DateTime("now +$endDays Days");

        $datesListMois=$repoDates->getDatesQuery("",array(1,2,3,4,5,6,7,9,10,11,13,14),"",$city->getState(),'','','','','',1,0,$startDate,$endDate);
        $nbEvt=count($datesListMois);

        switch($nbEvt){
          case $nbEvt>=0 and $nbEvt<=3 :
            $dureePeriod=200;
            break;
          case $nbEvt>=5 and $nbEvt<=10 :
              $dureePeriod=60;
              break;
          default : $dureePeriod=$period;
            break;
        }

        $endDays=$dureePeriod;
        $endDays=$dureePeriod;

        $startDate=new \DateTime("now");
        $endDate=new \DateTime("now +$endDays Days");
        $yearDate=new \DateTime("now +365 Days");



        $datesListChrono=$repoDates->getDatesQuery("",array(1,2,3,4,5,6,7,9,10,11,14),"",$city->getState(),'','','','','',1,0,$startDate,$endDate);

        $coursCity=$repoDates->getDatesQuery("",array(8),"",$city->getState(),'','','','','',1,0,$startDate,$yearDate);



        return $this->render("FabienEventsEngineBundle:City:view.html.twig",array("city"=>$city,"datesListChrono"=>$datesListChrono,"datesListImportant"=>$datesListImportant,"coursCity"=>$coursCity,"period"=>$period,"start_date"=>$startDate,"end_date"=>$endDate,"urlImageCity"=>$urlImageCity,"nbEvtMonth"=>$nbEvt,"dureePeriod"=>$dureePeriod));
    }










    public function viewSlugRSSAction(City $city,$slug,$period=200)
    {

      $type=array(1,2,3,4,5,6,7,9,10,11,12,13);
      $order="DESC";
      $orderField="date_creation";

      $events=$this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Event')
        ->getEventsQuery("",$type,"",$city->getState(),"",$orderField,$order)
      ;
        return $this->render('FabienEventsEngineBundle:General:rss.html.twig',array("urlRss"=>"http://www.tango-agenda.com/event/rss","dateGeneration"=>date("D, d M y H:i:s O"),"events"=>$events));
      }

    public function listCoursAction(City $city,$slug){

      $repoDates = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Date')
      ;

      $coursCity=$repoDates->getDatesQuery("",array(8),"",$city->getState());

      return $this->render("FabienEventsEngineBundle:City:listCours.html.twig",array("city"=>$city,"coursCity"=>$coursCity));

    }

    public function viewIdAction()
    {

      $request = $this->container->get('request_stack')->getCurrentRequest();

      if($request->isXmlHttpRequest()){
          $id = $request->request->get('id');

          $city = $this->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:City')
          ->findOneBy(array('id'=>$id))
          ;

          return $this->render("FabienEventsEngineBundle:City:view.html.twig",array("city"=>$city));

      }else{
        return new response("Loading faillure");
      }

    }










    public function indexAdminAction($iditem)
    {

      $state = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:State')
        ->findOneById($iditem)
      ;

      $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:City')
      ;


      $cities=$repository->listCityState($state);


      return $this->render("FabienEventsEngineBundle:City:listAdmin.html.twig",array("entity"=>'city','entityNext'=>'','listItem'=>$cities,'state'=>$iditem,'idstate'=>$iditem));

    }

    public function editAction(Request $request,City $City)
    {

      $state=$City->getState();

      $form = $this->createForm('Fabien\EventsEngineBundle\Form\CityType', $City);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($City);
          $em->flush($City);

          return $this->redirectToRoute('admin_city_index',array("state"=>$state->getId(),"iditem"=>$state->getId()));
      }

      return $this->render('FabienEventsEngineBundle:City:new.html.twig', array(
          'City' => $City,
          'state'=>$state->getId(),
          'form' => $form->createView(),
      ));

      return $this->render("FabienEventsEngineBundle:City:new.html.twig");

    }

    public function newAction(Request $request,$idstate)
    {

      $state=$this->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:State')
        ->findOneById($idstate)
      ;

      $City = new City();
      $City->setState($state);
      $form = $this->createForm('Fabien\EventsEngineBundle\Form\CityType', $City);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($City);
          $em->flush($City);

          return $this->redirectToRoute('admin_city_index',array("state"=>$idstate,"iditem"=>$idstate));
      }

      return $this->render('FabienEventsEngineBundle:City:new.html.twig', array(
          'City' => $City,
          'state'=>$idstate,
          'form' => $form->createView(),
      ));

      return $this->render("FabienEventsEngineBundle:City:new.html.twig");

    }

    public function deleteAction(City $city)
    {
        $idstate= $city->getState()->getId();
        $em = $this->getDoctrine()->getManager();

        $em->remove($city);
        $em->flush($city);

        return $this->redirectToRoute('admin_city_index',array("iditem"=>$idstate));

    }


}
