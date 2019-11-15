<?php

namespace Fabien\EventsEngineBundle\Controller;



//require_once __DIR__ . '/../FbApi/php-graph-sdk-5/src/Facebook/autoload.php';

class FabienFbApi{


	function __construct(){
		$this->accessToken= "194083261040351|xTameia5DT5ZFr1ZyZA-VHfSxeA";
		$this->userToken="EAACwhIfUxt8BAEgb4sTUeibBStpOoi047egFoPC2ZBJijQ7t07Bpwdgo1iVYOJR6X15mfRsVqJhKUYas5cNOzmOCv7spnfVzl848IDZB6EZCjSbZCTjHKDg6ZBoO4p6CHToT4RUuS1smvxpP7mBSO96osfhqfXOZBJx20d0geISQZDZD";
		$this->appId="194083261040351";
		$this->appSecret='bb0a35d5a7265c92e18f181588069359';
		$this->graphVersion='v2.8';
	}


	function setToken($token){
		$this->userToken=$token;
	}


	public function listFbEvents($searchReq,$userTokenFB){
			$fb = new \Facebook\Facebook([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'default_graph_version' => $this->graphVersion,
			]);

			$fb->setDefaultAccessToken($this->accessToken);

			try {
			  // Returns a `Facebook\FacebookResponse` object
			 $response = $fb->get("search?q=$searchReq&type=event&fields=id",$userTokenFB);
		 } catch(Facebook\Exceptions\FacebookResponseException $e) {
 		  return 'Graph returned an error: ' . $e->getMessage();
 		  exit;
 		} catch(Facebook\Exceptions\FacebookSDKException $e) {
 		  return 'Facebook SDK returned an error: ' . $e->getMessage();
 		  exit;
 		}


 		$graphEdge = $response->getGraphEdge();
		try {
			// Returns a `Facebook\FacebookResponse` object
		 $response = $fb->get("search?q=$searchReq&type=event&fields=id",$userTokenFB);
		 } catch(Facebook\Exceptions\FacebookResponseException $e) {
			return 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			return 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}

		$listId=array();

		foreach($graphEdge as $graphNode){
			$listId[]=$graphNode['id'];
		}


		for($cpt=1;$cpt<=3;$cpt++){
			try {
				$nextFeed = $fb->next($graphEdge);
			}catch (Exception $e) {
				exit;
			}

			if($nextFeed){
				foreach($nextFeed as $graphNode){
					$listId[]=$graphNode['id'];
				}
			}
		}


		return $listId;


	}


	public function getFbEvent($id,$userTokenFB=""){
		//Récupère les infos sur l'événement
		$fb = new \Facebook\Facebook([
		  'app_id' => $this->appId,
		  'app_secret' => $this->appSecret,
		  'default_graph_version' => $this->graphVersion,
		  ]);
			$fb->setDefaultAccessToken($this->accessToken);

			$request = $fb->request('GET', $id);

		try {
		  // Returns a `Facebook\FacebookResponse` object
			//die(">".$userTokenFB);

		 	$response = $fb->get("$id", $userTokenFB);

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		try {
		$infosEvt = $response->getGraphObject();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			return 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			return 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}


		//Récupère la photo
		try {
		  // Returns a `Facebook\FacebookResponse` object
		 $responseImg = $fb->get("$id/?fields=cover", $this->accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}


		try {
		  // Returns a `Facebook\FacebookResponse` object
		 $imgEvt=$responseImg->getGraphUser();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}


		$evt=array('image'=>"","title"=>"","description"=>"","date_start"=>"","date_end"=>"","place_name"=>"","place"=>"","city"=>"","cp"=>"");

		if(!isset($imgEvt['cover']['source']))return false;
		$evt['image']=$imgEvt['cover']['source'];
		$evt['title']=$infosEvt['name'];
		if(isset($infosEvt['description'])){$evt['description']=$infosEvt['description'];}else{$evt['description']="Pas de description.";}
		$evt['date_start']=$infosEvt['start_time']->format('Y-m-d H:i:s');

		if(!isset($infosEvt['end_time']))return;
		$evt['date_end']=$infosEvt['end_time']->format('Y-m-d H:i:s');



		if(!isset($infosEvt['place']['name']))return;

		$evt['adress']=$infosEvt['place']['name'];

		if(isset($infosEvt['place']['location'])){
			if(isset($infosEvt['place']['location']['street']))$evt['place']=$infosEvt['place']['location']['street'];
			if(isset($infosEvt['place']['location']['city']))$evt['city']=$infosEvt['place']['location']['city'];
			if(isset($infosEvt['place']['location']['zip']))$evt['cp']=$infosEvt['place']['location']['zip'];
		}else{
			$evt['place']=$infosEvt['place']['name'];
		}



		if(count($evt)>0){

			return $evt;
		}else{
			return false;
		}

	}

}

?>
