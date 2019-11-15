<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Video;
use Fabien\EventsEngineBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{


  public function videosRssAction()
  {

    $videos=$this
      ->getDoctrine()
      ->getManager()
      ->getRepository('FabienEventsEngineBundle:Video')
      ->getVideos("",50,null)
    ;
      return $this->render('FabienEventsEngineBundle:video:rss-videos.html.twig',array("urlRss"=>"http://www.tango-agenda.com/tango-videos/rss","dateGeneration"=>date("D, d M y H:i:s O"),"videos"=>$videos));
    }


  function listVideosAction(){


    $listMaestros=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Person")
    ->getPersonByVideosSemaine();

    $listMaestrosNotActive=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Person")
    ->getPersonByVideosPlusSemaine();


    $listVideos=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Video")
    ->getVideos();

    $allVideos=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Video")
    ->findAll();

    $countVideos=count($allVideos);

    return $this->render('FabienEventsEngineBundle:video:listVideos.html.twig',array("listvideos"=>$listVideos,"countVideos"=>$countVideos,"listMaestros"=>$listMaestros,"listMaestrosNotActive"=>$listMaestrosNotActive));

  }


  function deleteAction(Video $video){

    $em = $this->getDoctrine()->getManager();

    $em->remove($video);
    $em->flush($video);

    return $this->redirectToRoute('fabien_video_list');
  }


  function importsAction(){


    $DEVELOPER_KEY = 'AIzaSyDzv6Dct6e0FlxtXxEhQpDinNG-IjW9Tzw';
    //require_once __DIR__ . '/../../../../vendor/google/apiclient/src/Google/autoload.php';
    $client = new \Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);

    // Define an object that will be used to make all API requests.

    $youtube = new \Google_Service_YouTube($client);

    $htmlBody = '';


    $em = $this->getDoctrine()->getManager();


    $maestros=$this->getDoctrine()
    ->getManager()
    ->getRepository("FabienEventsEngineBundle:Person")
    ->findByType("maestro");


    foreach($maestros as $maestro){

        if($maestro->getSurnom()!=""){
          $reqMaestro=$maestro->getPrenom()." ".$maestro->getSurnom()." ".$maestro->getNom();
        }else{
          $reqMaestro=$maestro->getPrenom()." ".$maestro->getNom();
        }

        if($maestro->getHomonyme()==true){
          $reqMaestro=$reqMaestro. " tango";
        }

        try {
            $searchResponse = $youtube->search->listSearch('id,snippet', array(
              'q' => $reqMaestro,
              'maxResults' => 20,
              'order'=>"date"
            ));

            $videos = array();
            $channels = '';
            $playlists = '';

            foreach ($searchResponse['items'] as $searchResult) {
              switch ($searchResult['id']['kind']) {
                case 'youtube#video':
                  $videos[]=$searchResult;

                  break;
                /*
                case 'youtube#channel':
                  $channels .= sprintf('<li>%s (%s)</li>',
                      $searchResult['snippet']['title'], $searchResult['id']['channelId']);
                  break;
                case 'youtube#playlist':
                  $playlists .= sprintf('<li>%s (%s)</li>',
                      $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
                  break;
                */
              }

            }

          } catch (Google_Service_Exception $e) {
            $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
              htmlspecialchars($e->getMessage()));
          } catch (Google_Exception $e) {
            $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
              htmlspecialchars($e->getMessage()));
          }


          foreach($videos as $result){

            $testExist=$this->getDoctrine()
            ->getManager()
            ->getRepository("FabienEventsEngineBundle:Video")
            ->findOneByYoutubeId($result['id']['videoId']);


           if($testExist==false){
              $video= new Video();


              $video->setTitle($result['snippet']['title']);
              $video->setUrlImage($result['snippet']['thumbnails']['high']['url']);
              //$dateYt=$result['snippet']['publishedAt'];
              $video->setDatePublication(new \DateTime($result['snippet']['publishedAt']));
              $video->setDescription($result['snippet']['description']);
              $video->setSource("youtube");
              $video->setYoutubeId($result['id']['videoId']);
              $video->setPublish(true);

              if(strpos($result['snippet']['title'],"vals")!==false or strpos($result['snippet']['description'],"vals")!==false)$video->setType("vals");
              if(strpos($result['snippet']['title'],"milonga")!==false or strpos($result['snippet']['description'],"milonga")!==false)$video->setType("milonga");
              if(strpos($result['snippet']['title'],"alternativ")!==false or strpos($result['snippet']['description'],"alternativ")!==false)$video->setType("other");

        $video->addPerson($maestro);

      //Ajout du Partenaire
      foreach($maestros as $partenaire){
        //  echo $partenaire->getNom();
				$findTitle=strtolower ($this->clarifyText($result['snippet']['title']));
				$findDescription=strtolower ($this->clarifyText($result['snippet']['description']));

				$prenomPartner=strtolower ($this->clarifyText($partenaire->getPrenom()));
				$nomPartner=strtolower ($this->clarifyText($partenaire->getNom()));

				$added=false;


        $listPersons=$video->getPersons();

        foreach ($listPersons as $personCheck){
          if($personCheck->getId() == $partenaire->getId())$added=true;
        }

				if($prenomPartner){
          if($added==false and $partenaire->getId()!=$maestro->getId() and strpos($findTitle,$prenomPartner)!==false and strpos($findTitle,$nomPartner)!==false){

            $video->addPerson($partenaire);
  				  $added=true;
          }


  				if($added==false and $partenaire->getId()!=$maestro->getId() and strpos($findDescription,$prenomPartner)!==false and strpos($findDescription,$nomPartner)!==false ){
  				  $video->addPerson($partenaire);
  				}
        }

      }
				echo "ajouté ".$video->getTitle()." ".$maestro->getPrenom()." ".$maestro->getNom()." <br>";
              $em->persist($video);

            }else{
				$addNew=false;

				$video=$testExist;


				$arrayIdMaestro=array();

				foreach($video->getPersons() as $testPerson){
					$arrayIdMaestro[]=$testPerson->getId();
				}

				if(!in_array($maestro->getId(),$arrayIdMaestro)){
					$video->addPerson($maestro);
					$em->persist($video);
				}


			}

            /*
            echo "<pre>";
            print_r($result);
            echo"</pre>";
            */
            $em->flush();
          }




    }
    //var_dump($searchResult);

    die("termine");
  }


function guessTypeAllAction(){
  $videos=$this->getDoctrine()
  ->getManager()
  ->getRepository("FabienEventsEngineBundle:Video")
  ->findAll();

  foreach($videos as $video){
    if(strpos($video->getTitle(),"vals")!==false or strpos($video->getDescription(),"vals")!==false)$video->setType("vals");
    if(strpos($video->getTitle(),"milonga")!==false or strpos($video->getDescription(),"milonga")!==false)$video->setType("milonga");
    if(strpos($video->getTitle(),"alternativ")!==false or strpos($video->getDescription(),"alternativ")!==false)$video->setType("other");
  }

  $this->getDoctrine()->getManager()->flush();

  return new response("done");

}


    function clarifyText($texte){

      $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u','ü'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
      $texte = strtr( $texte, $unwanted_array );

      //$texte=$this->stripEmojis($texte);

	     $regexEmoji = '/[[:^print:]]/';
       $texte = preg_replace($regexEmoji, '', $texte);

      return $texte;
    }


  function stripEmojis($text)
    {
      $text=iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
      $regexEmoji = '/[[:^print:]]/';
      $text = preg_replace($regexEmoji, '', $text);
      return $text;
    }


}
