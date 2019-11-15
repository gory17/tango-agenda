<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Fabien\EventsEngineBundle\Entity\Image;
use Fabien\EventsEngineBundle\Entity\Event;

/**
 * Person controller.
 *
 */
class PersonController extends Controller
{
    /**
     * Lists all person entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('FabienEventsEngineBundle:Person')->findAll();

        return $this->render('FabienEventsEngineBundle:person:index.html.twig', array(
            'people' => $people,
        ));
    }


    /**
     * Creates a new person entity.
     *
     */
    public function newAction(Request $request)
    {
        $person = new Person();
        $form = $this->createForm('Fabien\EventsEngineBundle\Form\PersonType', $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);

            if($person->getFile()){
              $this->deleteImages($person);
              $image=new Image();
              $nameDoc=$image->upload($person->getFile());
              $image->setUrl($nameDoc);
              $image->setAlt("Image de la personne");

              $em->persist($image);
              $person->setImage($image);
            }

            $em->flush();
            return $this->redirectToRoute('person_index');
        }

        return $this->render('FabienEventsEngineBundle:person:new.html.twig', array(
            'person' => $person,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a person entity.
     *
     */
    public function showAction(Person $person)
    {


        return $this->render('FabienEventsEngineBundle:person:show.html.twig', array(
            'person' => $person,

        ));
    }

    /**
     * Displays a form to edit an existing person entity.
     *
     */
    public function editAction(Request $request, Person $person)
    {

        $editForm = $this->createForm('Fabien\EventsEngineBundle\Form\PersonType', $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
			       $em = $this->getDoctrine()->getManager();
          if($person->getFile()){
            $this->deleteImages($person);
            $image=new Image();
            $nameDoc=$image->upload($person->getFile());
            $image->setUrl($nameDoc);
            $image->setAlt("Image de la personne");

            $em->persist($image);
            $person->setImage($image);
          }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('person_edit', array('id' => $person->getId()));
        }

        return $this->render('FabienEventsEngineBundle:person:edit.html.twig', array(
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a person entity.
     *
     */
    public function deleteAction(Person $person)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();


        return $this->redirectToRoute('person_index');
    }


    public function deleteImages($person){
        $em = $this->getDoctrine()->getManager();
        $image=$person->getImage();
        if($image){
          $urlImage=$image->getUrl();
          @unlink($image->getUploadRootDir().$image->getUploadDir()."/".$urlImage);
          @unlink($image->getUploadRootDir().$image->getUploadThumbDir()."/".$urlImage);

        //  $event->setImage(null);
          //$em->flush($event);

          $em->remove($image);

          $em->flush();
        }
    }



    public function listMaestroAction(){

      $listmaestros=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Person")
      ->findAll()
      ;

      $countMaestros=count($listmaestros);

      return $this->render('FabienEventsEngineBundle:person:listmaestro.html.twig',array("listmaestros"=>$listmaestros,"countMaestros"=>$countMaestros));

    }



    public function showMaestroAction(Person $maestro){
      $listEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array(1,2,3,4,5,12,10),"","","","","ASC",null,null,1,null,null,null,$maestro)
      //->getDatesQuery("",array(3,4,5,12,10),"","","","","ASC","","")
      ;

      $listVideos=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Video")
      ->getVideos($maestro);


      return $this->render('FabienEventsEngineBundle:person:maestro.html.twig',array('maestro'=>$maestro,"listevents"=>$listEvents,"listvideos"=>$listVideos));



    }


    public function maestroRssAction(Person $person,$period=200)
    {

      $listEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array(1,2,3,4,5,12,10),"","","","","ASC",null,null,1,null,null,null,$person)
      //->getDatesQuery("",array(3,4,5,12,10),"","","","","ASC","","")
      ;

      $videos=$this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Video')
        ->getVideos($person,50,null)
      ;
        return $this->render('FabienEventsEngineBundle:person:rss-person.html.twig',array("urlRss"=>"http://www.tango-agenda.com/maestro/rss","dateGeneration"=>date("D, d M y H:i:s O"),"videos"=>$videos,"maestro"=>$person,"events"=>$listEvents));
      }


    public function linkMaestrosAction(){
      $listDatesBigEvents=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Date")
      ->getDatesQuery("",array(1,2,3,4,5,12,10),"","","","","ASC")
      ;

      $maestros=$this->getDoctrine()
      ->getManager()
      ->getRepository("FabienEventsEngineBundle:Person")
      ->findAll();

      $cpt=0;

      foreach($maestros as $maestro){

        $searchTab=array();

        $searchTab[]=$this->clarifyText($maestro->getPrenom()." ".$maestro->getNom());
        if($maestro->getSurnom()){
          $searchTab[]=$this->clarifyText($maestro->getPrenom()." ".$maestro->getSurnom()." ".$maestro->getNom());
          $searchTab[]=$this->clarifyText($maestro->getPrenom().' "'.$maestro->getSurnom().'" '.$maestro->getNom());
        }

        if($maestro->getPartner()){
          $searchTab[]=$this->clarifyText($maestro->getPrenom()." & ".$maestro->getPartner()->getPrenom());
    		  $searchTab[]=$this->clarifyText($maestro->getPrenom()." y ".$maestro->getPartner()->getPrenom());
          $searchTab[]=$this->clarifyText($maestro->getPrenom()." and ".$maestro->getPartner()->getPrenom());
        }

          foreach($listDatesBigEvents as $dateBigEvent){
            $link=false;
            $cpt++;



              foreach($searchTab as $recherche){

                if(strpos($this->clarifyText($dateBigEvent->getEvent()->getTitle()),$recherche)!==false){
        					echo $this->clarifyText($maestro->getPrenom())." ".$this->clarifyText($maestro->getNom())." - ".$this->clarifyText($dateBigEvent->getEvent()->getTitle())."<br>";
        					$link=true;
        				}

              if($dateBigEvent->getEvent()->getTypeEvent()->getId() != 1 and $dateBigEvent->getEvent()->getTypeEvent()->getId() != 2 and $dateBigEvent->getEvent()->getTypeEvent()->getId() != 10){

                if(strpos($this->clarifyText($dateBigEvent->getEvent()->getDescription()),$recherche)!==false){
                  echo $this->clarifyText($maestro->getPrenom())." ".$this->clarifyText($maestro->getNom())." - ".$this->clarifyText($dateBigEvent->getEvent()->getTitle())."<br>";
                  $link=true;
                }
              }

        		}

            foreach($dateBigEvent->getEvent()->getPersons() as $personInEvent){
              if($personInEvent->getId()==$maestro->getId())$link=false;
            }


            if($link==true){
              $em = $this->getDoctrine()->getManager();
              $dateBigEvent->getEvent()->addPerson($maestro);
              $em->persist($dateBigEvent->getEvent());
              $em->flush();
              echo "trouvé<br>";
            }

          }

      }
      die("fin pami $cpt");


    }


    function clarifyText($texte){

      $texte=strtolower($texte);

      $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u','ü'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
      $texte = strtr( $texte, $unwanted_array );

      return $texte;
    }


}
