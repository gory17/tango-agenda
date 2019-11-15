<?php
namespace Fabien\EventsEngineBundle\Controller;


use Fabien\EventsEngineBundle\Entity\Event;
use Fabien\EventsEngineBundle\Form\EventType;
use Fabien\EventsEngineBundle\Entity\Image;
use Fabien\EventsEngineBundle\Entity\City;
use Fabien\EventsEngineBundle\Entity\TypeEvent;
use Fabien\EventsEngineBundle\Entity\Country;
use Fabien\EventsEngineBundle\Entity\State;
use Fabien\EventsEngineBundle\Entity\Banned;

use Fabien\EventsEngineBundle\Controller\FabienFbApi;
use Doctrine\Common\Collections\ArrayCollection;

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


class EventController extends Controller

{

    public function indexAction()
    {
        return new response("Pas d'accueil");
    }


    public function newFirstPageAction(){
      $listTypeEvt=$this->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:TypeEvent')
      ->findAll()
      ;
      return $this->render('FabienEventsEngineBundle:Events:newFirstPage.html.twig',array("listTypeEvt"=>$listTypeEvt));
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


    public function rssBigEventsAction($days="150",$format="complet")
    {
      $typeObj="";

      $type=array(3,4,5,12);


      $events=$this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Event')
        ->getLastEvents($days,$type)
      ;


      return $this->render('FabienEventsEngineBundle:General:rss.html.twig',array("urlRss"=>"http://www.tango-agenda.com/event/rss","dateGeneration"=>date("D, d M y H:i:s O"),"events"=>$events,"format"=>$format,"days"=>$days,"type"=>$typeObj));

    }







    public function lastEventsAction($type="",$days="7",$format="complet")
    {
      $typeObj="";

      if($type=="" or $type=="all"){

        $type=array(3,4,5,12);

      }else{
        $typeObj=$this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:TypeEvent')
          ->findOneById($type)
        ;
        $type=array($type);
      }

      $events=$this
        ->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Event')
        ->getLastEvents($days,$type)
      ;

      return $this->render('FabienEventsEngineBundle:Events:lastEvents.html.twig',array("events"=>$events,"format"=>$format,"days"=>$days,"type"=>$typeObj));

    }

    public function newDirectAction(Request $request,$type=""){
      if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
        $userRole="admin";
      }else{
        $userRole="";
      }



      $event = new Event();
      $event->setOrigin("direct");

      if($type!=""){
        $selectType=$this
          ->getDoctrine()
          ->getManager()
          ->getRepository('FabienEventsEngineBundle:TypeEvent')
          ->findOneBySlug($type)
        ;
        $event->setTypeEvent($selectType);
      }

      if ($request->isMethod('POST')) {

          $repositoryCountry = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('FabienEventsEngineBundle:Country')
          ;

          if(!$event->getCountry()){
            $country=$repositoryCountry->findOneById(75);
            $event->setCountry($country);
          }

          $formBuilder = $this->createForm(EventType::class,$event,["userRole"=>$userRole]);
          $formBuilder->handleRequest($request);

          if($formBuilder->isValid()){
            $em = $this->getDoctrine()->getManager();

            $event->setDescription($this->removeEmoji($event->getDescription()));
            $event->setTitle($this->removeEmoji($event->getTitle()));

            $em->persist($event);


            $em->flush();


            if($event->getFile()){

              $image=new Image();
              $this->deleteImagesEvent($event);
              $nameDoc=$image->upload($event->getFile());
              $image->setUrl($nameDoc);
              $image->setAlt($event->getAlt());

              $em->persist($image);
              $event->setImage($image);
            }

            $em->flush();


            //envoi du mail de modification

            if($event->getMailCreator()){
              $urlModif="http://www.tango-agenda.com".$this->generateUrl('fabien_user_edit_event', [ 'id' => $event->getId(), 'keyEvt' =>$event->getKeyEvt()  ]);;
              $bodyMail="
                [ENGLISH]<br><br>
                Thank you :)! <br><br>
                Your event has been successfully proposed to Tango-Agenda.<br>
                You can modify your event by clicking the link : <br>
                $urlModif<br><br>
                Fabien

                <br><br><hr><br><br>

                [FRENCH]
                <br><br>
                Merci :) !<br><br>
                Votre événement a été proposé avec succès sur Tango-Agenda !<br>
                Vous pouvez le modifier à tout moment en cliquant sur le lien :<br>
                $urlModif<br><br>
                Fabien<br><br> \n
              ";


              // Test fonction mail();

               // *** A configurer

               $to    = $event->getMailCreator();

               // adresse MAIL OVH liée à l’hébergement.
               $from  = "contact@tango-agenda.com";

               ini_set("SMTP", "smtp.tango-agenda.com");   // Pour les hébergements mutualisés Windows de OVH

               // *** Laisser tel quel

               $JOUR  = date("Y-m-d");
               $HEURE = date("H:i");

               $Subject = "tango-agenda - ".$event->getTitle();

               $mail_Data = "";
               $mail_Data .= "<html> \n";
               $mail_Data .= "<head> \n";
               $mail_Data .= "<title>"."tango-agenda - ".$event->getTitle()."</title> \n";
               $mail_Data .= "</head> \n";
               $mail_Data .= "<body> \n";


               $mail_Data .= $bodyMail;
               $mail_Data .= "</body> \n";
               $mail_Data .= "</HTML> \n";

               $headers  = "MIME-Version: 1.0 \n";
               $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
               $headers .= "From: $from  \n";
               $headers .= "Disposition-Notification-To: $from  \n";

               // Message de Priorité haute
               // -------------------------
               $headers .= "X-Priority: 1  \n";
               $headers .= "X-MSMail-Priority: High \n";

               $CR_Mail = TRUE;

               $CR_Mail = @mail ($to, $Subject, $mail_Data, $headers);

           }
           $urlModif="http://www.tango-agenda.com".$this->generateUrl('fabien_user_edit_event', [ 'id' => $event->getId(), 'keyEvt' =>$event->getKeyEvt()  ]);;


            $request->getSession()->getFlashBag()->add('notice', $urlModif);

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirectToRoute('events_view', array('id' => $event->getId()));

            $request->getSession()->getFlashBag()->add('notice', 'Evénement bien enregistrée.');

            // On redirige vers la page de visualisation de l'annonce nouvellement créée
            return $this->redirectToRoute('events_view', array('id' => $event->getId()));
          }else{
            //ormulaire non valide
            die("Formulaire non valide");
          }
    }else{
      $formBuilder = $this->createForm(EventType::class,$event,["userRole"=>$userRole]);
      return $this->render('FabienEventsEngineBundle:Events:add.html.twig',array('form'=>$formBuilder->createView(),"event"=>$event));
    }
  }








    public function purgeEventsAction(){
      //nettoyage des dates
      $listDates=$this
        ->getDoctrine()
        ->getManager()
        ->getRepository("FabienEventsEngineBundle:Date")
        ->getOldDates()
      ;

      $cptdate=0;
      $cpt=0;

      $em = $this->getDoctrine()->getManager();

      foreach($listDates as $date){
        $em->remove($date);
        $cptdate++;
      }
      $em->flush();

      $listEvents = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository("FabienEventsEngineBundle:Event")
        ->getOrphanedEvents()
      ;


      foreach($listEvents as $event){

        $image=$event->getImage();

        if($image){
          $urlImage=$image->getUrl();
          @unlink($image->getUploadRootDir().$image->getUploadDir()."/".$urlImage);
          @unlink($image->getUploadRootDir().$image->getUploadThumbDir()."/".$urlImage);
          $em->remove($image);
        }

        //Suppression de l'objet
        $em->remove($event);
        $em->flush();
        $cpt++;
      }



      return new response("Supprimé : $cpt événements et $cptdate dates");
    }





    public function deleteImagesEvent($event){
        $em = $this->getDoctrine()->getManager();
        $image=$event->getImage();
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



    public function deleteAction(Event $event,$id=null)
    {

      $em = $this->getDoctrine()->getManager();

      if($event->getUrlFb()!=""){
        $bann=new Banned;
        $bann->setEventurl($event->getUrlFb());
        $em->persist($bann);
        $em->flush($bann);
      }

      $this->deleteImagesEvent($event);

      //Suppression de l'objet
      $em->remove($event);
      $em->flush();

      //return $this->redirectToRoute('fabien_events_engine_bigevents_list',array("titletype"=>$event->getTypeEvent()->getTitle()));
      return new response("Evénement supprimé");
    }


    public function deleteUserVersionAction(Event $event,$keyEvt){
      if($event->getKeyEvt()!=$keyEvt){
        return $this->redirectToRoute('events_view', array('id' => $event->getId()));
      }

        $execute=$this->deleteAction($event);
        return $execute;

    }


    public function publishAction(Event $event,$id,$mode){
      $urlModif="http://www.tango-agenda.com".$this->generateUrl('fabien_user_edit_event', [ 'id' => $event->getId(), 'keyEvt' =>$event->getKeyEvt()  ]);


      if($mode=="publier"){
        $event->setPublish(1);

        $bodyMail="
          [ENGLISH]<br><br>
          Congratulation !<br><br>
          Your event has been successfully added to Tango-Agenda !<br>
          You can modify your event by clicking the link : <br>
          $urlModif<br><br>
          Fabien

          <br><br><hr><br><br>

          [FRENCH]
          Félicitations ! <br><br>
          Votre événement a été ajouté à Tango-Agenda avec succès !<br>
          Vous pouvez le modifier à tout moment en cliquant sur le lien :<br>
          $urlModif<br><br>
          Fabien<br><br> \n
        ";
      }else{
        $event->setPublish(0);

        $bodyMail="
          [ENGLISH]<br><br>
          We are sorry but your event has not been approved by Tango-Agenda administrator.<br>
          We hope you'll submit new tango event soon !
          <br><br>
          Fabien

          <br><br><hr><br><br>

          [FRENCH]
          Nous sommes désolé, mais votre événement n'a pas été approuvé sur Tango-Agenda.<br>
          Nous espérons avoir de vos nouvelles prochainement,
          <br><br>
          Fabien<br><br> \n
        ";
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($event);
      $em->flush();

      //envoi du mail de modification

      if($event->getMailCreator()){

        // Test fonction mail();

         // *** A configurer

         $to    = $event->getMailCreator();

         // adresse MAIL OVH liée à l’hébergement.
         $from  = "contact@tango-agenda.com";

         ini_set("SMTP", "smtp.tango-agenda.com");   // Pour les hébergements mutualisés Windows de OVH

         // *** Laisser tel quel

         $JOUR  = date("Y-m-d");
         $HEURE = date("H:i");

         $Subject = "Validation tango-agenda - ".$event->getTitle();

         $mail_Data = "";
         $mail_Data .= "<html> \n";
         $mail_Data .= "<head> \n";
         $mail_Data .= "<title>"."Validation tango-agenda - ".$event->getTitle()."</title> \n";
         $mail_Data .= "</head> \n";
         $mail_Data .= "<body> \n";


         $mail_Data .= $bodyMail;
         $mail_Data .= "</body> \n";
         $mail_Data .= "</HTML> \n";

         $headers  = "MIME-Version: 1.0 \n";
         $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
         $headers .= "From: $from  \n";
         $headers .= "Disposition-Notification-To: $from  \n";

         // Message de Priorité haute
         // -------------------------
         $headers .= "X-Priority: 1  \n";
         $headers .= "X-MSMail-Priority: High \n";

         $CR_Mail = TRUE;

         $CR_Mail = @mail ($to, $Subject, $mail_Data, $headers);

     }

      return $this->redirectToRoute('events_view', array('id' => $event->getId()));
    }

    public function valoriseAction(Event $event,$id,$mode){
      if($mode=="valoriser"){
        $event->setValorisation(1);
      }else{
        $event->setValorisation(0);
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($event);
      $em->flush();

      return $this->redirectToRoute('events_view', array('id' => $event->getId()));
    }


    public function editUserAction(Event $event, $keyEvt,Request $request){
      if($event->getKeyEvt()!=$keyEvt){
        return $this->redirectToRoute('events_view', array('id' => $event->getId()));
      }
        $edition=$this->editAction($event->getId(),$request,$mode='user');
        return $edition;
    }


    public function editAction($id,Request $request,$mode='admin'){

        //Récupere l'id balancé par la route. Utilise doctrine pour faire la recherche avec Doctrine
        $event = $this->getDoctrine()
        ->getManager()
        ->getRepository('FabienEventsEngineBundle:Event')
        ->find($id)
        ;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
          $userRole="admin";
        }else{
          $userRole="";
        }

        //Crée le formulaire avec l'événement dedans
        $formBuilder = $this->createForm(EventType::class,$event,["userRole"=>$userRole]);

        // A mettre a l'extérieur de la boucle request
        $dates = new ArrayCollection();

         foreach ($event->getDates() as $date) {
             $dates->add($date);
         }

        if ($request->isMethod('POST')) {

            //Traite le formulaire
            $formBuilder->handleRequest($request);

            if($formBuilder->isValid()){
              $em = $this->getDoctrine()->getManager();

              foreach ($dates as $date) {
                $em->persist($date);
                $em->flush($date);

                 if (false === $event->getDates()->contains($date)) {
                     // remove the Task from the Tag
                     $em->remove($date);

                     $em->persist($event);
                     $em->flush($date);
					           $em->flush($event);
                 }
               }

               if($event->getFile()){

                 $image=new Image();
                 $this->deleteImagesEvent($event);
                 $nameDoc=$image->upload($event->getFile());
                 $image->setUrl($nameDoc);
                 $image->setAlt($event->getAlt());

                 $em->persist($image);
                 $event->setImage($image);
               }

              $event->setDescription($this->removeEmoji($event->getDescription()));
              $event->setTitle($this->removeEmoji($event->getTitle()));

              $em->persist($event);
              $em->flush();

              // On redirige vers la page de visualisation de l'annonce nouvellement créée
              return $this->redirectToRoute('events_view', array('id' => $event->getId()));
            }else{
              $this->addFlash("error", "Erreur tous les champs ne sont pas correctement remplis");
              return $this->render('FabienEventsEngineBundle:Events:add.html.twig',array('form'=>$formBuilder->createView(),"event"=>$event));
            }

        }else{
            //S'occupe du formulaire
            return $this->render('FabienEventsEngineBundle:Events:add.html.twig',array('form'=>$formBuilder->createView(),"event"=>$event));

        }
    }


    public function viewAction(Event $event,$id){
        return $this->render("FabienEventsEngineBundle:Events:view.html.twig",array("event"=>$event));

    }
}
