<?php

namespace Fabien\EventsEngineBundle\Controller;



//require_once __DIR__ . '/../FbApi/php-graph-sdk-5/src/Facebook/autoload.php';

class FabienFbApi{


	function __construct(){
		/*
		$this->accessToken= "EAACwhIfUxt8BALQPT7cpLbcttzHDe85rUXXEvY3d7g7BQu8PGEnLSctBvgPrw9yDmxyraizo1kIrv1602MKZCDQGzY1eBbVWcWxwfAdC7f0XgVaoKki1lLX3ZAJUQ2QzHQvWqrZAIMXZAG2b2JzVZBrPTvBvejnwZD";
		$this->userToken="EAACwhIfUxt8BALQPT7cpLbcttzHDe85rUXXEvY3d7g7BQu8PGEnLSctBvgPrw9yDmxyraizo1kIrv1602MKZCDQGzY1eBbVWcWxwfAdC7f0XgVaoKki1lLX3ZAJUQ2QzHQvWqrZAIMXZAG2b2JzVZBrPTvBvejnwZD";
		$this->appId="194083261040351";
		$this->appSecret='bb0a35d5a7265c92e18f181588069359';
		$this->graphVersion='v2.11';
		*/
		$this->accessToken= "EAACauAjhbWUBAEdvZCwZBt5hwXckgqSvrtpFvN7cNX1JDEXns5N4VY653OpbObAmCrXgWoYcm9XuS3ipETNk9VtrllepPfQIKKn6GZBGg7KZAVZCiWlSQ73l4qHLjYlwTZBWyEVOK1RASCD3ZADyHFZC2ggc07sJs0t9OHZAACingwQZDZD";
		$this->userToken="EAACauAjhbWUBAEdvZCwZBt5hwXckgqSvrtpFvN7cNX1JDEXns5N4VY653OpbObAmCrXgWoYcm9XuS3ipETNk9VtrllepPfQIKKn6GZBGg7KZAVZCiWlSQ73l4qHLjYlwTZBWyEVOK1RASCD3ZADyHFZC2ggc07sJs0t9OHZAACingwQZDZD";
		$this->appId="170115213651301";
		$this->appSecret='062e5973f126524906cb968fb2425b7e';
		$this->graphVersion='v3.0';
	}


	function setToken($token){
		$this->userToken=$token;
	}





	public function listFbEvents($searchReq,$userTokenFB,$mode="requete",$banned){
			$listId=array();
			$fb = new \Facebook\Facebook([
			'app_id' => $this->appId,
			'app_secret' => $this->appSecret,
			'default_graph_version' => $this->graphVersion,
			'http_client_handler' => 'stream',
			]);



			$fb->setDefaultAccessToken($this->accessToken);
			$startreq=date("Y-m-d", time() - 60 * 60 * 24);

			try {
			  // Returns a `Facebook\FacebookResponse` object
				if($mode=="requete"){
					sleep(0.5);
			 		$response = $fb->get("search?q=$searchReq&type=event&since=$startreq",$userTokenFB);

				}else{

					$response = $fb->get("$searchReq/event",$userTokenFB);
					//print_r($response);
				}
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
	 		  return 'Graph returned an error: ' . $e->getMessage();
	 		  exit;
	 		} catch(Facebook\Exceptions\FacebookSDKException $e) {
	 		  return 'Facebook SDK returned an error: ' . $e->getMessage();
	 		  exit;
	 		}catch(Facebook\Exceptions\FacebookAuthenticationException $e){
				return "Probleme d'authentification au niveau du groupe" . $e->getMessage();
				exit;
			}catch(Facebook\Exceptions\FacebookAuthorizationException $e){
				return "Probleme d'authorisation au niveau du groupe" . $e->getMessage();
				exit;
			}


 		$graphEdge = $response->getGraphEdge();



		foreach($graphEdge as $graphNode){

				if(isset($graphNode['end_time']) and !in_array($graphNode['id'],$banned)){
					$dateNode=$graphNode['end_time'];

					if( $dateNode>new \DateTime("now")){
						$listId[]=$graphNode['id'];
					}
				}

		}

		/*
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
		*/

		//Traitement des groupes

		if($mode=="groups"){
				try {
					$responseFeed = @$fb->get("$searchReq/feed?since=$startreq",$userTokenFB);
				} catch(Facebook\Exceptions\FacebookResponseException $e) {
					return 'Graph returned an error: ' . $e->getMessage();
					exit;
				} catch(Facebook\Exceptions\FacebookSDKException $e) {
					return 'Facebook SDK returned an error: ' . $e->getMessage();
					exit;
				}

				$graphFeed = $responseFeed->getGraphEdge();

				$cptcount=0;

				foreach($graphFeed as $feed){
					//echo "ctp $cptcount - ";


					/*
					if($cptcount>4)break;
					$cptcount++;
					*/

					$timeChecker =new \DateTime("now -6 days");

					if(isset($feed['created_time']))$timeChecker=$feed['created_time'];
					if(isset($feed['updated_time']))$timeChecker=$feed['updated_time'];

					//if($feed['updated_time']>new \DateTime("now -2 days") and !in_array($feed['id'],$banned)){
					if($timeChecker>new \DateTime("now -3 days") and !in_array($feed['id'],$banned)){
						try {
							$responseAttachement = $fb->get($feed['id']."/attachments?since=$startreq",$userTokenFB);


						} catch(Facebook\Exceptions\FacebookResponseException $e) {
							return 'Graph returned an error au niveau du groupe: ' . $e->getMessage();
							exit;
						} catch(Facebook\Exceptions\FacebookSDKException $e) {
							return 'Facebook SDK returned an error au niveau du groupe: ' . $e->getMessage();
							exit;
						}catch(Facebook\Exceptions\FacebookAuthenticationException $e){
							return "Probleme d'authentification au niveau du groupe" . $e->getMessage();
							exit;
						}catch(Facebook\Exceptions\FacebookAuthorizationException $e){
							return "Probleme d'authorisation au niveau du groupe" . $e->getMessage();
							exit;
						}


						$graphEdgeAttachement = $responseAttachement->getGraphEdge();

						$cpAttachements=0;
						foreach($graphEdgeAttachement as $graphNode){
							//print_r($graphNode);
							if($cpAttachements>4)break;
							$cpAttachements++;
							if($graphNode['type']=='event'){

								try {
									if(!in_array($graphNode['target']['id'],$banned)){
									$responseEventFeed=$fb->get($graphNode['target']['id']."?fields=id,start_time,end_time",$userTokenFB);
									}else{
										$responseEventFeed=false;
									}


								} catch(Facebook\Exceptions\FacebookResponseException $e) {
									return 'Graph returned an error: ' . $e->getMessage();
									exit;
								} catch(Facebook\Exceptions\FacebookSDKException $e) {
									return 'Facebook SDK returned an error: ' . $e->getMessage();
									exit;
								}catch(Facebook\Exceptions\FacebookAuthenticationException $e){
									return "Probleme d'authentification au niveau de l'événement" . $e->getMessage();
									exit;
								}catch(Facebook\Exceptions\FacebookAuthorizationException $e){
									return "Probleme d'authorisation au niveau de l'événement" . $e->getMessage();
									exit;
								}
								if($responseEventFeed){
									$graphEventAttachement = $responseEventFeed->getGraphNode();

									if(isset($graphEventAttachement['end_time'])){
										$dateNode=$graphEventAttachement['end_time'];
									}else{
										$dateNode=$graphEventAttachement['start_time'];
									}


									if( $dateNode>new \DateTime("now")){
										$listId[]=$graphEventAttachement['id'];
									}
								}

							}

						}

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
			'http_client_handler' => 'stream',
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



		$evt=array('image'=>"","title"=>"","description"=>"","dates_list"=>array(),"date_start"=>"","date_end"=>"","place_name"=>"","place"=>"","city"=>"","cp"=>"");

		if(isset($imgEvt['cover']['source'])){
			$evt['image']=$imgEvt['cover']['source'];
		}else{
			$evt['image']="";
		}

		if($infosEvt['name'])$evt['title']=$infosEvt['name'];



		if(isset($infosEvt['description'])){$evt['description']=$infosEvt['description'];}else{$evt['description']="Pas de description.";}

		if(isset($infosEvt["event_times"])){
			foreach($infosEvt["event_times"] as $event_time){
				$evt['dates_list'][]=$event_time;
			}
		}else{
			$evt['date_start']=$infosEvt['start_time']->format('Y-m-d H:i:s');

			if(!isset($infosEvt['end_time'])){
				$evt['date_end']=$evt['date_start'];
			}else{
				$evt['date_end']=$infosEvt['end_time']->format('Y-m-d H:i:s');
			}

		}


		if(isset($infosEvt['place']['name'])){

			$evt['adress']=$infosEvt['place']['name'];

			if(isset($infosEvt['place']['location'])){
				if(isset($infosEvt['place']['location']['street']))$evt['place']=$infosEvt['place']['location']['street'];
				if(isset($infosEvt['place']['location']['city']))$evt['city']=$infosEvt['place']['location']['city'];
				if(isset($infosEvt['place']['location']['zip']))$evt['cp']=$infosEvt['place']['location']['zip'];
			}else{
				$evt['place']=$infosEvt['place']['name'];
			}

		}


		if(count($evt)>0){

			return $evt;
		}else{
			return false;
		}

	}

}

?>
