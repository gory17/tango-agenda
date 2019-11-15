<?php

require_once __DIR__ . '/php-graph-sdk-5/src/Facebook/autoload.php';

class FbData{
	

	function __construct(){
		$this->accessToken= "194083261040351|12EfGxc0tDTj0PEabOSExn3JBgA";
		$this->appId="194083261040351";
		$this->appSecret='fcc215faad787d600febeae7efe03999';
		$this->graphVersion='v2.8';
	}

	public function listData(){
		
		return "coucou";
	
	}

	

	public function getFbEvent($id){
		//Récupère les infos sur l'événement	
		$fb = new Facebook\Facebook([
		  'app_id' => $this->appId,
		  'app_secret' => $this->appSecret,
		  'default_graph_version' => $this->graphVersion,
		  ]);

		try {
		  // Returns a `Facebook\FacebookResponse` object
		 $response = $fb->get("$id", $this->accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  return 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  return 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		$infosEvt = $response->getGraphUser();

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

		$imgEvt=$responseImg->getGraphUser();

		
		$evt=array();
		$evt['image']=$imgEvt['cover']['source'];
		$evt['title']=$infosEvt['name'];
		$evt['description']=$infosEvt['description'];
		$evt['date_start']=$infosEvt['start_time']->format('Y-m-d H:i:s');
		$evt['date_end']=$infosEvt['end_time']->format('Y-m-d H:i:s');
		$evt['place']=$infosEvt['place']['name'];


		if(count($evt)>0){
			return $evt;
		}else{
			return false;
		}

	}

}
?>
