<?php

namespace Fabien\EventsEngineBundle\Controller;


use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Entity\Date;
use Fabien\EventsEngineBundle\Form\EventType;
use Fabien\EventsEngineBundle\Entity\Image;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\State;
use Fabien\EventsEngineBundle\Entity\Banned;
use Fabien\EventsEngineBundle\Entity\Fbimport;
use Fabien\EventsEngineBundle\Entity\RequeteFb;

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
use Symfony\Component\Security\Core\User\UserInterface;

set_time_limit(0);
class ImporterController extends Controller

{

    public function indexAction()
    {
        return $this->listEvents();
    }




    public static function removeEmoji($text) {

        $clean_text = "";

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }




    public function newFirstPageAction(){
        return $this->render('FabienEventsEngineBundle:Events:newFirstPage.html.twig');
    }



    public function newFbAction($fbid=null,$type=1,$mode="manuel"){
      if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        $userRole="admin";
      }else{
        $userRole="";
      }

      if($fbid){
        $importData=new FabienFbApi;
        $response=$importData->getFbEvent($fbid);

        $event=$this->getEventFb($response,$fbid);



        $event->setOrigin("direct fb");


        $selectType=$this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:TypeEvent')
          ->findOneById($type)
        ;
        $event->setTypeEvent($selectType);



        if($event){
          if($this->existFB($fbid)){
            //return $this->render('FabienEventsEngineBundle:Events:newFirstPage.html.twig',array("error_message"=>"L'évenement est déjà dans ma base de données, pas besoin de le rajouter ! Merci :) "));
            $eventFind=$this
        			->getDoctrine()
        			->getManager()
        			->getRepository('FabienEventsEngineBundle:Event')
              ->getByFbId($fbid);
            //return $this->redirectToRoute('events_view', array('id' => $eventFind->getId()));
            return $this->render('FabienEventsEngineBundle:Events:view.html.twig',array("event"=>$eventFind,"error_message"=>"L'évenement est déjà dans ma base de données, pas besoin de le rajouter ! Voici l'événement déjà présent : "));

          }

          $event->setDateInscription(null);

          $image=$this->getImageFb($response,$event);

          $date=$this->getDateFb($response,$event);

          $em = $this->getDoctrine()->getManager();
          $em->persist($image);
          $image->upload($event->getFile());
          $event->setImage($image);
          $em->persist($event);
          $em->persist($date);

          $em->flush();

          return $this->redirectToRoute('events_edit', array('id' => $event->getId()));

        }
        else{
          //Impossible de charger l'événement Facebook, on commence un nouveau
          $event = new Event();
          $event->setOrigin("direct");

          $formBuilder = $this->createForm(EventType::class,$event,["userRole"=>$userRole]);
          return $this->render('FabienEventsEngineBundle:Events:add.html.twig',array('form'=>$formBuilder->createView(),"event"=>$event,"error_message"=>"Impossible de charger cet évenement :( . Peut être pouvez vous m'aider en l'ajoutant manuellement grâce au formulaire ci-dessous ? :)"));
        }
      }

    }


    public function updateFbAllAction()
    {
		$request = $this->container->get('request_stack')->getCurrentRequest();
	    if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax
          $offset=$request->request->get('offset');
        }


		$listEvents=$this
			->getDoctrine()
			->getManager()
			->getRepository('FabienEventsEngineBundle:Event')
			->getUpdatedEvent(3,$offset);
		 ;

     //die($offset." ".count($listEvents));

      foreach($listEvents as $event){
        echo $event->getTitle()." <br>";
        $this->updateFbAction($event,"process");
      }

	  	$listEvents=$this
			->getDoctrine()
			->getManager()
			->getRepository('FabienEventsEngineBundle:Event')
			->getUpdatedEvent(3,"no");
		 ;

		if(count($listEvents)>0){
			return new response("il reste ".count($listEvents). " événements à traiter");
		}else{
			return new response("Termine");
		}



    }

    public function updateFbAction(Event $eventUpdate,$mode=""){
      $idFb=$eventUpdate->getUrlFb();
      $idFb=str_replace("https://facebook.com/","",$idFb);
      $em = $this->getDoctrine()->getManager();

      $eventUpdate->setDateUpdate(new \Datetime());
      $em->flush();

      if($idFb){
        $importData=new FabienFbApi;
        $response=$importData->getFbEvent($idFb);

        $event=$this->getEventFb($response,$idFb);

        if($event){


          $eventUpdate->setTitle($event->getTitle());
          $eventUpdate->setDescription($event->getDescription());
          $eventUpdate->setTitle($event->getTitle());

          //Vire l'ancienne image
          $imageActuelle = $eventUpdate->getImage();
          if($imageActuelle){
            $urlImage=$imageActuelle->getUrl();

            @unlink($imageActuelle->getUploadRootDir().$imageActuelle->getUploadDir()."/".$urlImage);
            @unlink($imageActuelle->getUploadRootDir().$imageActuelle->getUploadThumbDir()."/".$urlImage);
            $em->remove($imageActuelle);

            $em->flush();
          }

          $image=$this->getImageFb($response,$event);
          $em->persist($image);
          $image->upload($event->getFile());
          $eventUpdate->setImage($image);

          $em = $this->getDoctrine()->getManager();
          $em->persist($eventUpdate);

          $em->flush();
        }

        if($mode!="process"){
          return $this->redirectToRoute('events_view', array('id' => $eventUpdate->getId()));
        }else{
          return true;
        }

      }
    }

    public function existFB($urlFB){
      $count=false;


      $count = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Event')
      ->existFB($urlFB);
      ;

      return $count;
    }


    public function getImageFb($response,$event){
      $image=new Image();

      if($response){

        //import de l'Image
        //$nameDoc=$image->upload($response['image']);

        $image->saveFb($response['image']);
        $image->setAlt("Image issue de Facebook");

        return $image;
      }else{
        return false;
      }

    }



    public function getDateFb($response,$event){
      $date = new Date();
      $date->setStart( new \DateTime($response['date_start']));
      $date->setEnd( new \DateTime($response['date_end']));
      $date->setEvent($event);

      return $date;
    }


    public function getEventFb($response,$fbid,$genderBalance=false,$publish=false){
        //Import de l'événement FB

        $event = new Event();

        $event->setGenderBalance(false);
        $event->setPublish(false);

        if($response){

          $event->setTitle($response['title']);
          $descriptionFb= $this->removeEmoji($response['description']);

          $event->setOrigin("Facebook");
          $event->setDescription($descriptionFb);

          $event->setGenderBalance(false);
          $event->setPublish(false);


          $delimiter="";
          if($response['place_name']!="" and $response['place']!="")$delimiter=", ";

          $event->setAdress($response['place_name'].$delimiter.$response['place']);
          $event->setUrlFb("https://facebook.com/$fbid");

          //Trouve la ville correspondante
          $city = new City();
          $repositoryCity = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:City')
          ;

          $repositoryState= $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:State')
          ;

          $repositoryCountry= $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:Country')
          ;

          $lieuRaw="";

          if($response['city']!=''){
            $lieuRaw.="city: ".$response['city'].", ";
            $targetCity=$repositoryCity->findOneByTitle($response['city']);
            if($targetCity){
              $event->setCity($targetCity);
            }
          }else{
            //cherche la ville dans l'adresse
            if($response['place']!=""){

              $tabWords=explode($response['place']," ");


              foreach($tabWords as $word){
                $targetCity=$repositoryCity->findOneByTitle($word);
                if($targetCity){
                  $event->setCity($targetCity);
                }
              }
            }
          }

          if(isset($response['country'])){
              $lieuRaw.="country: ".$response['country'].", ";
          }

          if(isset($response['place'])){
              $lieuRaw.="place: ".$response['place']." ";
          }


          if($event->getCity()==""){
            //Ville inconnue et pays
            $event->setCityOther($response['city']);
            $villeInconnue=$repositoryCity->findOneById(48315);
            $paysInconnu=$repositoryCountry->findOneById(249);
            $event->setCountry($paysInconnu);
            $event->setcity($villeInconnue);

            $event->setCityOther($response['city']);
          }else{
            //On recherche le pays
            $targetCity=$event->getCity();
            $targetState=$targetCity->getState();
            $targetCountry=$targetState->getCountry();
            $event->setCountry($targetCountry);
          }

          $event->setLieuRaw($lieuRaw);

          //if($event->getCity()->getTitle()!="")$event->setPublish(1);

          return $event;
      }else{
        return $event;
      }

    }

    public function isBanned($idEvent)
    {
      $resultat = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Banned')
      ->isBanned($idEvent);
      ;

      if($resultat>0){
        return true;
      }else{
        return false;
      }
    }



    public function importAllAction(){

      $importData=new FabienFbApi;
      $affiche="";

      $request = $this->container->get('request_stack')->getCurrentRequest();
      if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

          $token_fb = $request->request->get('tokenfb');
          $offset=$request->request->get('offset');
          $importData->setToken($token_fb);
        }

        $listReq = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:RequeteFb')
          ->findBy(
            array(),
            array(),
            5,
            $offset
            )
        ;

        $texteOutput="";
        foreach($listReq as $reqFb){
          $listItemFb=array();



          $searchReq=$importData->listFbEvents($reqFb->getTitle(),$token_fb);

          foreach($searchReq as $ItemFb){
            $listItemFb[]=$ItemFb;
          }
          $texteOutput.=$reqFb->getTitle()."  <br>";

          foreach($listItemFb as $itemFb){
              $listFbEvent[]=array("item"=>$itemFb,"type"=>$reqFb->getTypeEvent()->getId());
          }


        }


          //Import des données
          $cpt=0;
          $cptSave=0;
          $em = $this->getDoctrine()->getManager();

          if(isset($listFbEvent)){
            foreach($listFbEvent as $fbEvent){
              $FBimport=new Fbimport;

              if($this->isBanned($fbEvent['item'])==false){
                if($this->existFB($fbEvent['item'])<1){
                  $cpt++;
                  $FBimport->setFbid($fbEvent['item']);
                  $FBimport->setTentative(0);

                  $typeEvt= $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('FabienEventsEngineBundle:TypeEvent')
                    ->findOneById($fbEvent['type']);

                  $FBimport->setTypeEvent($typeEvt);
                  $em->persist($FBimport);
                  $em->flush();
                }
              }
            }
          }
          return new response("$affiche <br/>$texteOutput<br/><br/>Evénements trouvés : $cpt");
    }




    public function processSaveFBAction(){

      $listFbEvent= $this->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:Fbimport')
            ->findBy(
              array(),
              array(),
              5
              )
            ;

      $cpt=0;
      $cptSave=0;
      $affiche="";

      $em = $this->getDoctrine()->getManager();

      foreach($listFbEvent as $fbEvent){
        if($this->isBanned($fbEvent->getFbid())==true){
          $em->remove($fbEvent);
          $em->flush($fbEvent);
        }

        //gère les événements à problème
        if($fbEvent->getTentative()>2){
          $bann=new Banned;
          $bann->setEventurl($fbEvent->getFbid());
          $em->persist($bann);
          $em->remove($fbEvent);
          $em->flush();

        }else{
          //incrémente le nombre de tentatives sur cet élément
          $tentatives=$fbEvent->getTentative();
          $fbEvent->setTentative($tentatives+1);
          $em->persist($fbEvent);
          $em->flush($fbEvent);

          $importData=new FabienFbApi;

          $request = $this->container->get('request_stack')->getCurrentRequest();
          if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax

              $token_fb = $request->request->get('tokenfb');
              $importData->setToken($token_fb);
          }

          $response=$importData->getFbEvent($fbEvent->getFbid());
          $event=$this->getEventFb($response,$fbEvent->getFbid());


          $type_event=$fbEvent->getTypeEvent();


          $cpt++;

          if($event){
            if($this->existFB($event->getUrlFb())!=true){
              $cptSave++;
              if($event->getTitle()!="" and strpos($event->getTitle(),"batchata")==false and strpos($event->getTitle(),"salsa")==false and strpos($event->getTitle(),"bachata")==false and strpos($event->getTitle(),"kiz")==false and strpos($event->getTitle(),"kizumba")==false and strpos($event->getTitle(),"kizomba")==false){
                $affiche= $affiche."Sauvegarde " .$fbEvent->getFbid(). " - " . $event->getTitle()."<br>";

                //Marathons et encuentros en parité par défaut
                if($type_event->getId()==4 or $type_event->getId()==3)$event->setGenderBalance(true);

                $event->setTypeEvent($type_event);
                $event->setDateInscription(null);
                $image=$this->getImageFb($response,$event);
                $date=$this->getDateFb($response,$event);

                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $image->upload($event->getFile());
                $event->setImage($image);
                $em->persist($event);
                $em->persist($date);


                try {
                  $em->remove($fbEvent);
                  $em->flush();
                } catch(Exception $e) {
                  $affiche= $affiche."Erreur de sauvegarde " .$fbEvent->getFbid(). " - " . $event->getTitle()."<br>";
                  exit;
                }
            }
          }
        }
      }
    }

    //check si c'est finit ou non
    $countFbEvent=$this->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:Fbimport')
          ->countElements();
          ;

    if($countFbEvent==0){
      return new response("termine");
    }else{
      return new response("Reste $countFbEvent éléments à sauvegarder ");
    }

  }

}
