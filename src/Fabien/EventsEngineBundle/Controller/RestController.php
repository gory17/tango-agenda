<?php

namespace Fabien\EventsEngineBundle\Controller;

use Fabien\EventsEngineBundle\Entity\Video;
use Fabien\EventsEngineBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RestController extends Controller
{

	public function setVideoTypeAction(Video $video,$type){
		$video->setType($type);

		$em = $this->getDoctrine()->getManager();
		$em->persist($video);
		$em->flush($video);

		return new response("ok");
	}

	public function deleteVideoAction(Video $video,$code){
		if($code=="Foclk@@di!24dolvh;pUU"){
			$em = $this->getDoctrine()->getManager();
			$video->setPublish(false);
			$em->flush($video);
			return new response("ok");
		}else{
			return new response("no");
		}
	}

	public function getMaestrosAction($lastConnexion=null){
		$em = $this->getDoctrine()->getManager();
		$list=$em->getRepository('FabienEventsEngineBundle:Person')->findAll();


		$tabResults=array();

		foreach($list as $person){
			$item['id']=$person->getId();
			$item['name']=$person->getNom();
			$item['surname']=$person->getPrenom();
			$item['nickname']=$person->getSurnom();
			$item['role']=$person->getRole();
			$item['description']=$person->getDescription();
			$item['siteweb']=$person->getSiteweb();
			$item['facebook']=$person->getFacebook();
			$item['homonyme']=$person->getHomonyme();
			$item['slug']=$person->getSlug();

			$countVideos=$em->getRepository('FabienEventsEngineBundle:Person')->findVideos($person,$lastConnexion);
			if(isset($countVideos[0]['cptvideos'])){
				$nbvideos=$countVideos[0]['cptvideos'];
			}else{
				$nbvideos=0;
			}

			$item['newvideos']=$nbvideos;
			if($person->getImage()->getUrl())$item['image']=$person->getImage()->getUrl();
			$tabResults[]=$item;
		}

		$response = new Response(json_encode($tabResults));
		$response->headers->set('Content-Type', 'application/json; charset=UTF-8');
		return $response;

	}


	public function getVideosAction($offset=0,$type=null){

		if($type=="all"){
			$type=null;
		}

		$videos=$this
			  ->getDoctrine()
			  ->getManager()
			  ->getRepository('FabienEventsEngineBundle:Video')
			  ->getVideos("",50,$offset,$type)
		;

		$tabResults=array();

		foreach($videos as $video){
			$item['id']=$video->getId();
			$item['urlImage']=$video->getUrlImage();
			$item['dateCreation']=$video->getDateCreation()->format('Y-m-d');
			$item['datePublication']=$video->getDatePublication()->format('Y-m-d');
			$item['description']=$video->getDescription();
			$item['title']=$video->getTitle();
			$item['source']=$video->getSource();
			$item['youtubeId']=$video->getYoutubeId();
			$tabResults[]=$item;
		}

		$response = new Response(json_encode($tabResults));
		$response->headers->set('Content-Type', 'application/json; charset=UTF-8');
		return $response;

	}


	public function getVideosMaestroAction($id=0,$offset=null,$type=null){

		if($type=="all"){
			$type=null;
		}

		$maestro=$this
			  ->getDoctrine()
			  ->getManager()
			  ->getRepository('FabienEventsEngineBundle:Person')
			  ->findOneById($id);

		$videos=$this
			  ->getDoctrine()
			  ->getManager()
			  ->getRepository('FabienEventsEngineBundle:Video')
			  ->getVideos($maestro,50,$offset,$type)
		;

		$tabResults=array();

		foreach($videos as $video){
			$item['id']=$video->getId();
			$item['urlImage']=$video->getUrlImage();
			$item['dateCreation']=$video->getDateCreation()->format('Y-m-d');
			$item['datePublication']=$video->getDatePublication()->format('Y-m-d');
			$item['description']=$video->getDescription();
			$item['title']=$video->getTitle();
			$item['source']=$video->getSource();
			$item['youtubeId']=$video->getYoutubeId();
			$item['type']=$video->getType();
			$tabResults[]=$item;
		}

		$response = new Response(json_encode($tabResults));
		$response->headers->set('Content-Type', 'application/json; charset=UTF-8');
		return $response;


	}


	public function  getVideosTypeAction(){


	}


	public function getVideosTypeMaestroAction(){

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
