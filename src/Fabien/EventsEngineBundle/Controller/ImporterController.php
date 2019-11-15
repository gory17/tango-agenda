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

        $clean_text=preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $clean_text);

        $clean_text=str_replace("\xF0\x9F\xA5\x8B","",$clean_text);
        $clean_text=str_replace("\n.","",$clean_text);

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

        $em = $this->getDoctrine()->getManager();

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

          //print_r($response);die();

          $event->setDateInscription(null);

          $tabdDates=array();

          if(sizeof($response['dates_list'])==0){
            $tabDates[]=$this->getDateFb($response,$event,"single");

          }else{
            foreach($response['dates_list'] as $dates_list )
              $tabDates[]=$this->getDateFb($dates_list,$event,"multiple");
          }
          /*
          echo "<pre>";
          print_r($tabDates);
          echo"</pre>";
          die();
          */

          if($response['image']!=""){
            $image=$this->getImageFb($response,$event);
            if($image){
              $em->persist($image);
              $image->upload($event->getFile());
                $event->setImage($image);
            }
          }



          $em->persist($event);

          foreach($tabDates as $date){
            $em->persist($date);
          }


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

		$param=$em->getRepository('FabienEventsEngineBundle:Parameter')
                    ->findOneById(1);
	  
		$token_fb=$param->getTokenfb();
	  
      $eventUpdate->setDateUpdate(new \Datetime());
      $em->flush();

      if($idFb){
        $importData=new FabienFbApi;
        $response=$importData->getFbEvent($idFb,$token_fb);

        $event=$this->getEventFb($response,$idFb);


        if($event){


          $eventUpdate->setTitle($event->getTitle());
          $eventUpdate->setDescription($event->getDescription());
          $eventUpdate->setTitle($event->getTitle());
			
			//Update des dates
			if($eventUpdate->getTypeEvent()->getId()==1 or $eventUpdate->getTypeEvent()->getId()==2 or $eventUpdate->getTypeEvent()->getId()==8  or $eventUpdate->getTypeEvent()->getId()==9){
				foreach($event->getDates() as $newDate){
					$addDate=true;
					foreach($eventUpdate->getDates() as $currentDate){
						if($currentDate->getStart() == $newDate->getStart()){
							$addDate=false;
						}
					}
					if($addDate==true){
						$eventUpdate->addDate($newDate);
					}
				}
			}
		  $em->flush();
		  
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



    public function alreadyProcesssing($urlFB){
      $count=false;

      $count = $this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Fbimport')
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



    public function getDateFb($response,$event,$mode='single'){

      $date = new Date();

      if($mode=='single'){
        $date->setStart( new \DateTime($response['date_start']));
    	  if($response['date_end']){
    		$date->setEnd( new \DateTime($response['date_end']));
    	  }else{
    		 $date->setEnd( new \DateTime($response['date_start']));
    	  }
        $date->setEvent($event);
      }else{
        $date->setStart( new \DateTime($response['start_time']->format('Y-m-d H:i:s')));
        if($response['end_time']){
        $date->setEnd( new \DateTime($response['end_time']->format('Y-m-d H:i:s')));
        }else{
         $date->setEnd( new \DateTime($response['end_time']->format('Y-m-d H:i:s')));
        }
        $date->setEvent($event);
      }

      return $date;
    }


    public function getEventFb($response,$fbid,$genderBalance=false,$publish=false){
        //Import de l'événement FB

        $event = new Event();

        $event->setGenderBalance(false);
        $event->setPublish(false);


        if($response){
          $event->setTitle($response['title']);
          //echo $this->guessType($response['title']);die();
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

          $targetCity=false;




          if(!$targetCity){

            if($response['city']!=''){
              $lieuRaw.="city: ".$response['city'].", ";


              $targetCity=$repositoryCity->searchCity($response['city'],"title");
              if(!$targetCity)$targetCity=$repositoryCity->searchCity($response['city'],"titleBis");


              if($targetCity){
                $event->setCity($targetCity);
              }
            }
          }


          if(!$targetCity){
            $tabWords=explode(" ",$response['place']);
            foreach($tabWords as $word){

              $word=ucfirst($word);

              $targetCity=$repositoryCity->findOneByTitle($word);

              if(!$targetCity){
                $targetCity=$repositoryCity->findOneByTitleBis($word);
              }

              if($targetCity){
                $event->setCity($targetCity);
              }

            }
          }


          if(!$targetCity){

            $usedCities=$repositoryCity->getUsedCities();
            foreach($usedCities as $usedCity){

                if(strpos($response['city'],$usedCity->getTitle())!==false){
                  $event->setCity($usedCity);
                  break;
                }
                if(strpos($response['city'],$usedCity->getTitleBis())!==false){
                  $event->setCity($usedCity);
                  break;
                }
                if(strpos($response['place'],$usedCity->getTitle())!==false){
                  $event->setCity($usedCity);
                  break;
                }
                if(strpos($response['place'],$usedCity->getTitleBis())!==false){
                  $event->setCity($usedCity);
                  break;
                }

            }
          }


          if(!$targetCity){
            $targetCity=$this->getGeocodeCity($response);
          }

          /*
          echo "<pre>";
          print_r($targetCity->getTitle());
          echo "</pre>";
          */

          if(isset($response['country'])){
              $lieuRaw.="country: ".$response['country'].", ";
          }

          if(isset($response['place'])){
              $lieuRaw.="place: ".$response['place']." ";
          }

          $defaultType= $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:TypeEvent')
            ->findOneById(11);


		       if($event->getCity() and ($event->getTypeEvent()!=$defaultType) and ($event->getTypeEvent()!=null)){
             if($event->getTypeEvent()->getId()!=3 and ($event->getTypeEvent()->getId()!=4) and ($event->getTypeEvent()->getId()!=12) and ($event->getTypeEvent()->getId()!=10) and ($event->getTypeEvent()->getId()!=5) ){
               $event->setPublish(1);
             }
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
      $texteOutput="Aucun import";
      $em=$this->getDoctrine()->getManager();
      $texteOutput="";



      $request = $this->container->get('request_stack')->getCurrentRequest();

      if($request->isXmlHttpRequest()){ // pour vérifier la présence d'une requete Ajax
          $token_fb = $request->request->get('tokenfb');
          $importData->setToken($token_fb);
          $importEntity= $request->request->get('importEntity');

      }else{
        die("erreur Ajax");
      }
		if($importEntity=="GroupFb"){
      $importEntity="GroupFb";
			$listGroup = $this
			  ->getDoctrine()
			  ->getManager()
			  ->getRepository("FabienEventsEngineBundle:$importEntity")
			  ->findBy(
				array("statut"=>"todo","valide"=>true),
				array(),
				3
				)
			;
		}else{
      $importEntity="RequeteFb";
			$listGroup = $this
			  ->getDoctrine()
			  ->getManager()
			  ->getRepository("FabienEventsEngineBundle:$importEntity")
			  ->findBy(
				array("statut"=>"todo"),
				array(),
				3
				)
			;

		}


		$bannedObjects= $this->getDoctrine()
			->getManager()
		  ->getRepository('FabienEventsEngineBundle:Banned')
		  ->findAll();
		  ;

		$banned=array();
		foreach($bannedObjects as $bann){
			$banned[]=$bann->GetEventUrl();
		}


        foreach($listGroup as $group){

          $listItemFb=array();

          if($importEntity=="GroupFb"){
            $tentatives=$group->getTentatives();
            $group->setTentatives($tentatives+1);

    			if($importEntity=="GroupFb"){
    				if($tentatives>5)$group->setValide(false);
    				$em->persist($group);
    				$em->flush();
    			}

            $searchReq=$importData->listFbEvents($group->getFbId(),$token_fb,"groups",$banned);
          }else{
            $searchReq=$importData->listFbEvents($group->getTitle(),$token_fb,$mode="requete",$banned);
          }


          $cptResult=0;


          if($importEntity=="GroupFb"){

            $defaultType= $this
              ->getDoctrine()
              ->getManager()
              ->getRepository('FabienEventsEngineBundle:TypeEvent')
              ->findOneById(11);
          }
          else{
            $defaultType=$group->getTypeEvent();
          }


          //Si on a un groupe il faut récupérer les posts
          if($importEntity=="GroupFb"){
            //get le feed du groupe
            $listFeed=$importData->listFbEvents($group->getFbId(),$token_fb,"groupFeed",$banned);

              foreach($listFeed as $ItemFb){
                $listItemFb[]=$ItemFb;
                $cptResult++;

            }

          }



          foreach($searchReq as $ItemFb){
            $listItemFb[]=$ItemFb;
            $cptResult++;
          }
          $texteOutput.=$group->getTitle()."  <br>";

          foreach($listItemFb as $itemFb){
              $listFbEvent[]=array("item"=>$itemFb,"type"=>$defaultType->getId());

          }

          $texteOutput.=$group->getTitle()."<br>";

          $group->setStatut("done");
          $group->setCount($group->getCount()+$cptResult);
          $em->persist($group);
          $em->flush();

        }


        //Import des données
        $cpt=0;
        $cptSave=0;
        $em = $this->getDoctrine()->getManager();
        if(isset($listFbEvent)){
          foreach($listFbEvent as $fbEvent){
            if($this->isBanned($fbEvent['item'])==false){
              if($this->alreadyProcesssing($fbEvent['item'])<1){
                if($this->existFB($fbEvent['item'])<1){
                  $FBimport=new Fbimport;
                  $cpt++;
                  $FBimport->setFbid($fbEvent['item']);
                  $FBimport->setTentative(0);

                  $typeEvent= $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('FabienEventsEngineBundle:TypeEvent')
                    ->findOneById($fbEvent['type']);


                  $FBimport->setTypeEvent($typeEvent);
                  $em->persist($FBimport);
                  $em->flush();

                }
              }
            }
          }
        }


        //Vérifie si terminé
  	  if($importEntity=="GroupFb"){
  		  $totalGroup = $this
  			->getDoctrine()
  			->getManager()
  			->getRepository("FabienEventsEngineBundle:$importEntity")
  			->findByValide(true);
  	  }else{
  		  $totalGroup = $this
  			->getDoctrine()
  			->getManager()
  			->getRepository("FabienEventsEngineBundle:$importEntity")
  			->findAll();
  	  }

      $totalDoneGroup = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository("FabienEventsEngineBundle:$importEntity")
        ->findBy(
          array("statut"=>"done"),
          array()
          )
      ;



      if(count($totalDoneGroup)<count($totalGroup)){
        //on continue
        //return new response (count($totalDoneGroup) ." ".count($totalGroup));
        return new response(count($totalDoneGroup). "Import des $importEntity <br/>$texteOutput<br/><br/>Evénements trouvés : $cpt");

      }else{
        // terminé

        //on remet tout le monde en totalDone
        foreach($totalDoneGroup as $group){
          $group->setStatut("todo");
          if($importEntity=="GroupFb"){
            $group->setTentatives(0);
          }
          $em->persist($group);
          $em->flush();
        }

        return new response("done");
      }

    }




    public function processSaveFBAction(){

      $em = $this->getDoctrine()->getManager();

      //Parcours tous les nons définis et tente de les réafecter
      $listTypeEventUndefined=$em
        ->getRepository("FabienEventsEngineBundle:Event")
        ->getEventsQuery("",array(11),"","","","date_creation","DESC","","","all")
                ;

      foreach($listTypeEventUndefined as $event){
        $idGuessType=$this->guessType($event->getTitle());

        if($idGuessType==11){
          $idGuessType=$this->guessType($event->getDescription());
        }


        $typeFind= $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:TypeEvent')
          ->findOneById($idGuessType);



        $event->setTypeEvent($typeFind);
        $em->persist($event);
      }
      $em->flush();
      //fin du parcours

      $listFbEvent= $em
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
              if($event->getTitle()!="" and strpos($event->getTitle(),"atchata")===false and strpos($event->getTitle(),"alsa")===false and strpos($event->getTitle(),"achata")===false and strpos($event->getTitle(),"lindy")===false and strpos($event->getTitle(),"swing")===false and strpos($event->getTitle(),"kiz")===false and strpos($event->getTitle(),"izumba")===false and strpos($event->getTitle(),"izomba")===false){
                $affiche= $affiche."Sauvegarde " .$fbEvent->getFbid(). " - " . $event->getTitle()."<br>";

                //Marathons et encuentros en parité par défaut
                if($type_event->getId()==4 or $type_event->getId()==3)$event->setGenderBalance(true);

                //devine le type
                if($type_event->getId()==11){


                  $idGuessType=$this->guessType($event->getTitle());
                  if($idGuessType==11){
                    $idGuessType=$this->guessType($event->getDescription());
                  }

                  $defaultType= $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('FabienEventsEngineBundle:TypeEvent')
                    ->findOneById($idGuessType);

                    $type_event=$defaultType;
                }


                $event->setTypeEvent($type_event);
                $event->setDateInscription(null);



                if($type_event->getId()!=11 and $type_event->getId()!=10  and $type_event->getId()!=12 and $type_event->getId()!=3 and $type_event->getId()!=5 and $type_event->getId()!=4  and $event->getCity()->getId()!=48315)$event->setPublish(1);

                $em = $this->getDoctrine()->getManager();
                $em->persist($event);



                $tabdDates=array();

                if(sizeof($response['dates_list'])==0){
                  $tabDates[]=$this->getDateFb($response,$event,"single");
                }else{
                  foreach($response['dates_list'] as $dates_list )
                    $tabDates[]=$this->getDateFb($dates_list,$event,"multiple");
                }

                /*
                echo "<pre>";
                print_r($tabDates);
                echo"</pre>";
                die();
                */

                foreach($tabDates as $date){
                  $em->persist($date);
                }
                $em->flush();

                $image=$this->getImageFb($response,$event);
                $em->persist($image);
                $image->upload($event->getFile());
                $event->setImage($image);

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



  public function getGeocodeCity($response){
    $repositoryCity = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:City')
    ;

    //Geocodage de l'adresse
    $search="";
    $result=false;
    //if($response['place_name']) $search.=$response['place_name']." ";
    if($response['place']) $search.=$response['place']." ";
    if($response['city'])$search.=$response['city'];

    if($search!=""){
      $urlGoogle="https://maps.google.com/maps/api/geocode/json?sensor=false&language=en&address=".urlencode($search)."&key=AIzaSyDaGgZMxyHsTiZW_Ha5UMztq0rx7jC68Ck";
      $jsonAdresse = file_get_contents($urlGoogle);
      $objectJsonAdresse = json_decode($jsonAdresse);




      if($objectJsonAdresse->status!="ZERO_RESULTS"){
        if(isset($objectJsonAdresse->results[0]->address_components)){
          foreach($objectJsonAdresse->results[0]->address_components as $node){
            $long_name= $node->long_name;
            $long_name=str_replace(" Province","",$long_name);
            $result=$repositoryCity->searchCity($long_name,"title");
            if($result){break;}

            if(!$result)$result=$repositoryCity->searchCity($long_name,"titleBis");
            if($result){break;}
            //if($targetCity) return $result;
          }
        }
      }
    }

    /*
    if($result){
      echo 'resultat : '.$result->getTitle();
    }


    echo"<pre>";
    print_r($objectJsonAdresse);
    echo "</pre>";
    die($objectJsonAdresse->error_message);
    */

    if($result){
      if(isset($objectJsonAdresse->error_message)){
        return false;
      }else{
        return $result;
      }
    }else{
      return false;
    }

  }



  public function guessType($texte){
    $idGuessType=11;

    $texte=strtolower($texte);

    $arrayTexte=explode(" ",$texte);




    // cours 8
    $arrayCours=array("технику","техникa","opetukset","lezioni","урок","Техники","tecnica","μάθημα","technique","cours","ateliers","atelier","corsi","clase","corso","lesson","lezione","kurz","course","class","classes","technik","kurs","μαθήματα","コース","赛道","tecnica","technica","technika","ТЕХНИК","techniki");

    //milonga 1
    $arrayMilonga=array("milongon","Peña","cafémilonga","Nuevo","","Musicaliza","aperi","longa","bar","milonga","café" ,"serata","bal","dj","tdj","tj","milonguita","café","cafe","noche","nieve","brunch","Милонга","ミロンガ"," ミロンガ","舞会","nuevomilonga","balul","Танцевальное","pratilonga","practilonga","praktilonga","neolonga");


    //encuentro 4
    $arrayEncuentro=array("encuentro");

    //pratique 9
    $arrayPratique=array("practique","pratica","ПРАКТИК","práctica","practice","practica","praticie","praktik","praktika","pratika",'pratik',"pratique", "Patrycją","Практика","无疫通行证","プラティーク","ελευθεροκοινωνία","praktyka","практика");

    //initiations 13
    $arrayInitiation=array("initiations","initiation","inizio","open door","open day");

    //workshop 2
    $arrayWS=array("workshop","Семинар","ws","stage","taller","seminar","bewegung","bewegung","bootcamp","阶段","ステージ","στάδιο");

    //Festival 5
    $arrayFestival=array("festival","festivalito","Φεστιβάλ","フェスティバル");

    //marathon 3
    $arrayMarathon=array("marathon","maraton","maracuentro");

    //weekend 12
    $arrayWe=array("week end","week-end");

    //voyages 10
    $arrayVacs=array("vacances","vancacia","vacaciones","trip","holidays","voyage","vacanza");


    //live 6
    $arrayLive=array("concert","live","conzert","concerto","koncert","concert","コンサート","音乐会");


    //show 7
    $arrayShow=array("show","spectacle");

    foreach($arrayTexte as $item){
      if(strlen($item)>=4){
        $item=$this->deleteAccents($item);

        switch ($item){
          /*
            case in_array($item,$arrayInitiation):
              $idGuessType=13;
              break;
            */

          case $this->inArrayType($item,$arrayMarathon):
            $idGuessType=3;

            break;

          case $this->inArrayType($item,$arrayFestival):
            $idGuessType=5;
            break;


          case $this->inArrayType($item,$arrayWe):
            $idGuessType=12;
            break;

          case $this->inArrayType($item,$arrayVacs):
            $idGuessType=10;
            break;

          case $this->inArrayType($item,$arrayEncuentro):
            $idGuessType=4;
            break;


          case $this->inArrayType($item,$arrayWS):
            $idGuessType=2;
            break;


          case $this->inArrayType($item,$arrayMilonga):
            $idGuessType=1;
            break;


          case $this->inArrayType($item,$arrayCours):
            $idGuessType=8;
            break;



          case $this->inArrayType($item,$arrayPratique):
              $idGuessType=9;
              break;



          case $this->inArrayType($item,$arrayShow):
            $idGuessType=7;
            break;

          case $this->inArrayType($item,$arrayLive):
            $idGuessType=6;
            break;


          default :
            //$idGuessType=11;
            if(strpos($texte,"longa")!==false)$idGuessType=1;
            break;
        }

      }
    }
    return $idGuessType;

  }


  private function inArrayType($needle,$arrayType){

    foreach ($arrayType as $item){

      if(strpos($item,$needle)!== false)return true;
    }
    return false;
  }

    private function deleteAccents($string){
      $string = strtolower($string);

      $replace = array(
              'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
              'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
              'Þ'=>'B',
              'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
              'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
              'Ğ'=>'G',
              'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
              'Ł'=>'L',
              'Ñ'=>'N', 'Ń'=>'N',
              'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
              'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
              'Ț'=>'T',
              'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
              'Ý'=>'Y',
              'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
              'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
              'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
              'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
              'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
              'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
              'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
              'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
              'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
              'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
              'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
              'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
              'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
              'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
              'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
              'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
              'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
              'ק'=>'q',
              'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
              'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
              'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
              'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
              'в'=>'v', 'ו'=>'v', 'В'=>'v',
              'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
              'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
              'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
          );
          return strtr($string, $replace);
    }

}
