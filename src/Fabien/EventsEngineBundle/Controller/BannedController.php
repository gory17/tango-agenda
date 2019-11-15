<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Fabien\EventsEngineBundle\Entity\Banned;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BannedController extends Controller
{
  public function addBannUrlAction($url=null)
  {

    if($url!=""){
      $em = $this->getDoctrine()->getManager();
      $bann=new Banned;
      $bann->setEventurl($url);
      $em->persist($bann);
      $em->flush($bann);
      return new response("Url bannie");
    }else{
      return new response("Fail ! Url non bannie");
    }
  }

  public function addBannUrlAjaxAction(){

    $request = $this->container->get('request_stack')->getCurrentRequest();

    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax
        $url = $request->request->get('urlbann');
    }

    $this->addBannUrlAction($url);

    return new response("Url bannie");

  }


}

?>
