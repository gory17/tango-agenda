<?php

namespace Fabien\EventsEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ImportYoutubeController extends Controller{


	function __construct(){

  }

  function importsAction(){


	  $DEVELOPER_KEY = 'AIzaSyDzv6Dct6e0FlxtXxEhQpDinNG-IjW9Tzw';
		//require_once __DIR__ . '/../../../../vendor/google/apiclient/src/Google/autoload.php';
	  $client = new \Google_Client();
	  $client->setDeveloperKey($DEVELOPER_KEY);

	  // Define an object that will be used to make all API requests.

	  $youtube = new \Google_Service_YouTube($client);

	  $htmlBody = '';

	  try {

	    // Call the search.list method to retrieve results matching the specified
	    // query term.
	    $searchResponse = $youtube->search->listSearch('id,snippet', array(
	      'q' => "tango",
	      'maxResults' => 50,
				'order'=>"date"
	    ));

	    $videos = '';
	    $channels = '';
	    $playlists = '';

	    // Add each result to the appropriate list, and then display the lists of
	    // matching videos, channels, and playlists.
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

		echo "<ul>";
		foreach($videos as $result){

			echo "<li><img src='".$result['snippet']['thumbnails']['high']['url']."'>".$result['snippet']['title']. "<br>".$result['snippet']['publishedAt']."<br>".$result['snippet']['description']."<br>".$result['id']['videoId']."<br>"."</li>";


			echo "<pre>";
			print_r($result);
			echo"</pre>";

		}
		echo "</ul>";

		var_dump($searchResult);

		die("termine");
  }

}
