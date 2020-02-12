<?php

class RPG_Parser {
	private $webContent;

	function __construct($id) {
		$this->webContent = file_get_contents('http://rpg-paradize.com/site--' . $id);
	}

	function getName(){
		preg_match('/<b>(.*?)<\/b>/', $this->webContent, $name); // On cherche dans la page le nom du serv
		return $name[1]; // On renvoie le nom
	}

	function getVote(){
		preg_match('/Vote : (.*?)<\/a>/', $this->webContent, $vote); // On cherche dans la page le nombre de vote
		return $vote[1]; // On renvoie le nombre de vote
	}

	function getPos(){
		preg_match('/Position (.*?)<\/b>/', $this->webContent, $position); // On cherche dans la page la position
		return $position[1]; // On renvoi la position
	}

	function getOut(){
		preg_match('/Clic Sortant : (.*?)<\/div>/', $this->webContent, $out); // On cherche le nombre de out
		return $out[1]; // On renvoi le nombre de out
	}

  	function getGraph(){
		preg_match('/labels : \[(.*?)\]/',$this->webContent, $label);
		preg_match('/data : \[(.*?)\]/', $this->webContent, $data);
		$array_label = explode(",", $label[1]);
		$array_data = explode(",", $data[1]);

		foreach($array_label as $k => $v)
			$array_label[$k] = substr($v, 1, -1);
		
		$data = array();
		foreach ($array_label as $k => $v) {
			$data[$k]['date'] = $array_label[$k];
			$data[$k]['vote'] = $array_data[$k];
		}
		return $data;
  	}
}

if (!isset($_GET['id'])):
  echo "Usage : url.co?id={id_rpg}";
  die();
endif;

$RPG = new RPG_Parser($_GET['id']);

$Name 		= $RPG->getName();
$Vote 		= $RPG->getVote();
$Position 	= $RPG->getPos();
$Out 		= $RPG->getOut();
$Graph      = $RPG->getGraph();

$data = array(
  'name'  => $Name,
  'vote'  => $Vote,
  'position'   => $Position,
  'out'   => $Out,
  'graphe' => $Graph
);
$json_data = json_encode($data);

header("Content-Type: application/json");
echo $json_data;
?>