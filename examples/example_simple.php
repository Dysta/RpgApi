<?php
// example usage of RPGParser.php
// parsing ID and display simple name and position
require "../RpgApi.php";
use Dysta\RpgApi;

$RPG = new RpgApi(111514);
echo "<p> Name : " . $RPG->getName(); "</p>";
echo "<p> Position : " . $RPG->getPosition(); "</p>";
?>