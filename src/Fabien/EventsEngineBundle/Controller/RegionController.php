<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\Departement;
use Fabien\EventsEngineBundle\Entity\Region;
use Fabien\EventsEngineBundle\Entity\TypeEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionController extends Controller
{

  public function listAction()
  {
    $repositoryRegion = $this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Region')
    ;

    $Regions=$repositoryRegion->findAll();

    return $this->render('FabienEventsEngineBundle:Regions:list.html.twig',array("listRegions"=>$Regions));

  }

  public function viewAction(Region $Region,$slug,$period)
  {
    $repositoryRegion = $this->getDoctrine()
    ->getManager()
    ->getRepository('FabienEventsEngineBundle:Event')
    ;
    //$listEvents=$repositoryRegion->getEventsQuery("",array(1),$period,$Region,"dateStart","","");
    $listEvents=$repositoryRegion->getEventsQuery("",array(1),$period,$Region,"","dateStart","ASC",0,0);
    return $this->render('FabienEventsEngineBundle:Regions:view.html.twig',array("region"=>$Region,"listEvents"=>$listEvents,"period"=>$period));
  }


}


?>
