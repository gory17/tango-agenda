<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\State;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class StateController extends Controller
{
  public function viewStateAction(State $State)
  {
    return true;
  }


  public function indexAdminAction($iditem)
  {

    $country = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Country')
      ->findOneById($iditem)
    ;

    $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:State')
    ;


    $states=$repository->listStateCountry($country);


    return $this->render("FabienEventsEngineBundle:States:listAdmin.html.twig",array("entity"=>'state','entityNext'=>'city','listItem'=>$states,'country'=>$country));

  }

  public function editAction(Request $request,State $State)
  {

    $form = $this->createForm('Fabien\EventsEngineBundle\Form\StateType', $State);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($State);
        $em->flush($State);
        $idcountry=$State->getCountry()->getId();
        return $this->redirectToRoute('admin_state_index',array("iditem"=>$idcountry));
    }

    return $this->render('FabienEventsEngineBundle:States:new.html.twig', array(
        'State' => $State,
        'form' => $form->createView(),
        "Country"=>$State->getCountry()
    ));

    return $this->render("FabienEventsEngineBundle:States:new.html.twig",array("state"=>$State,"Country"=>$State->getCountry()));

  }

  public function newAction(Request $request,$idcountry)
  {

    $country = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Country')
      ->findOneById($idcountry)
    ;

    $State = new State();
    $State->setCountry($country);
    $form = $this->createForm('Fabien\EventsEngineBundle\Form\StateType', $State);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($State);
        $em->flush($State);

        return $this->redirectToRoute('admin_state_index',array("iditem"=>$idcountry));
    }

    return $this->render('FabienEventsEngineBundle:States:new.html.twig', array(
        'State' => $State,
        'Country'=>$country,
        'form' => $form->createView(),
    ));

    return $this->render("FabienEventsEngineBundle:States:new.html.twig");

  }

  public function deleteAction(State $state)
  {
      $idcountry= $state->getCountry()->getId();
      $em = $this->getDoctrine()->getManager();

      $em->remove($state);
      $em->flush($state);

      return $this->redirectToRoute('admin_state_index',array("iditem"=>$idcountry));

  }



}
