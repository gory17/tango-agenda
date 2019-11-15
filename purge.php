<?php

$db = new PDO('mysql:host=tangoageplfabien.mysql.db;dbname=tangoageplfabien', 'tangoageplfabien', 'Tang0AgendA');


//suppression des vieilles dates
$resultDates = $db->query("delete from date where end < curdate()");

// liste des événements sans date
$resultEvents = $db->query("select * from event where id not in (select e.id from event e, date d where e.id = d.event_id)");

// suppression des images
foreach ($resultEvents as $event){

    $queryImage="select * from image, event where event.image_id=image.id and event.id=".$event['id'];
    $resultImage=$db->query($queryImage);

    foreach($resultImage as $image){
       @unlink(".web/uploads/img/".$image['url']);
       $db->query("delete from image where id=".$image['id']);
    }

    $db->query("delete from event where id=".$event['id']);
    
}
echo "<h1>Terminé ! :)</h1>";




?>
