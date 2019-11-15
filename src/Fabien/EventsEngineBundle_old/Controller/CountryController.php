<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\State;
use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\TypeEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\JsonResponse;

class CountryController extends Controller
{

  public function listAction()
  {

    $repositoryCountry = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Country')
    ;


    $repositoryCity = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
    ;

    $CountryArray=$repositoryCountry->getCountryEventsResume();

    foreach ($CountryArray as $country) {
      $country["cities"]=$repositoryCity->getCountryCities($country['slug']);
      $CountryList[]=$country;
    }

    return $this->render("FabienEventsEngineBundle:Country:list.html.twig",array("CountryList"=>$CountryList));
  }

  public function listCitiesAction(Country $country,$slug){
    $repositoryCity = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
    ;

    $lastEvents=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Date")
    ->getDatesQuery("",array(2,3,4,5,10,12),"","",$country)
    ;

    $start_date=new \DateTime("now");
    $end_date=new \DateTime("now 180 Days");

    $mostActiveCityList=$repositoryCity->getCountryCities($country->getSlug(),"cptevt","DESC",10);
    $cityList=$repositoryCity->getCountryCities($country->getSlug());

    return $this->render("FabienEventsEngineBundle:Country:countryView.html.twig",array("country"=>$country,"mostActiveCityList"=>$mostActiveCityList,"cityList"=>$cityList,"lastEvents"=>$lastEvents,"start_date"=>$start_date,"end_date"=>$end_date));
  }


  public function indexAdminAction()
  {

    $repositoryCountry = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Country')
    ;


    $countries=$repositoryCountry->findAll();


    return $this->render("FabienEventsEngineBundle:Country:listAdmin.html.twig",array("entity"=>'country','entityNext'=>'state','listItem'=>$countries));

  }

  public function editAction(Request $request,Country $Country)
  {
    $form = $this->createForm('Fabien\EventsEngineBundle\Form\CountryType', $Country);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($Country);
        $em->flush($Country);

        return $this->redirectToRoute('admin_country_index');
    }

    return $this->render('FabienEventsEngineBundle:Country:new.html.twig', array(
        'Country' => $Country,
        'form' => $form->createView(),
    ));

    return $this->render("FabienEventsEngineBundle:Country:new.html.twig");

  }

  public function newAction(Request $request)
  {

    $Country = new Country();
    $form = $this->createForm('Fabien\EventsEngineBundle\Form\CountryType', $Country);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($Country);
        $em->flush($Country);

        return $this->redirectToRoute('admin_country_index');
    }

    return $this->render('FabienEventsEngineBundle:Country:new.html.twig', array(
        'Country' => $Country,
        'form' => $form->createView(),
    ));

    return $this->render("FabienEventsEngineBundle:Country:new.html.twig");

  }

  public function deleteAction(Country $country)
  {
      $em = $this->getDoctrine()->getManager();

      $em->remove($country);
      $em->flush($country);

      return $this->redirectToRoute('admin_country_index');
  }

}
