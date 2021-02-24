<?php
// example usage of RPGParser.php
// passing RPG's ID by url and returning a json stream
require "../RpgApi.php";
use Dysta\RpgApi;

if (!isset($_GET['id'])):
    echo "Usage : url.co?id={id_rpg}";
    die();
endif;

$RPG = new RpgApi($_GET['id']);
$json_data = $RPG->toJSON();

header("Content-Type: application/json");
echo $json_data;
?>