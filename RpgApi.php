<?php
/**
 * API pour RPG paradize
 * @author Dysta
 */
class RpgApi {
    /**
     * HTML code
     * @var string
     */
    private $webContent;

    /**
     * RPG id
     * @var int
     */
    private $id;

    /**
     * RPG position
     * @var int
     */
    private $position;

    /**
     * RPG name
     * @var string
     */
    private $name;
    
    /**
     * RPG outs
     * @var int
     */
    private $outs;

    /**
     * RPG votes
     * @var int
     */
    private $votes;

    /**
     * RPG urls
     * @var array
     */
    private $urls;
    
    /**
     * RPG graph data
     * @var array
     */
    private $graph;
    
    private function getWebContent() {
        $opts = array(
            CURLOPT_URL             => 'http://rpg-paradize.com/site--' . $this->id,
            CURLOPT_HEADER          => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CONNECTTIMEOUT  => 3,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246'
        );
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $this->webContent = curl_exec($ch);
        curl_close($ch);
    }

    private function parseName() {
        preg_match('/<b>(.*?)<\/b>/', $this->webContent, $name);
        $this->name = $name[1];
    }

    private function parseVote() {
        preg_match('/Vote : (.*?)<\/a>/', $this->webContent, $vote);
        $this->votes = $vote[1];
    }

    private function parsePosition() {
        preg_match('/Position (.*?)<\/b>/', $this->webContent, $position);
        $this->position = $position[1];
    }

    private function parseOut() {
        preg_match('/Clic Sortant : (.*?)<\/div>/', $this->webContent, $out);
        $this->outs = $out[1];
    }

    private function parseUrls() {
        $this->urls['out'] = "https://www.rpg-paradize.com/out.php?num=" . $this->id;
        preg_match('/hidden;\">http(s)?:\/\/(.*?)<\/a>/', $this->webContent, $urls);
        $this->urls['real'] = substr(explode(">", $urls[0])[1], 0, -3);
    }

    private function parseGraph() {
        preg_match('/labels : \[(.*?)\]/',$this->webContent, $label);
        preg_match('/data : \[(.*?)\]/', $this->webContent, $data);
        $array_label = explode(",", $label[1]);
        $array_data = explode(",", $data[1]);

        foreach($array_label as $k => $v)
            $array_label[$k] = substr($v, 1, -1);
        
        $this->graph = array();
        foreach ($array_label as $k => $v) {
            $this->graph[$k]['date'] = $array_label[$k];
            $this->graph[$k]['vote'] = $array_data[$k];
        }
    }


    /**
     * Init RpgApi
     * @param int $id id of the rpg page
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * * Return RPG name
     * @return string name
     */
    public function getName() {
        if (empty($this->webContent))
            $this->getWebContent();
        
        if (empty($this->name))
            $this->parseName();
        
        return $this->name;
    }

    /**
     * Return RPG vote
     * @return int vote number
     */
    public function getVote() {
        if (empty($this->webContent))
            $this->getWebContent();

        if (empty($this->votes)) 
            $this->parseVote();

        return $this->votes;
    }

    /**
     * Return RPG position
     * @return int position number
     */
    public function getPosition() {
        if (empty($this->webContent))
            $this->getWebContent();
        
        if (empty($this->position)) 
            $this->parsePosition();

        return $this->position;
    }

    /**
     * Return RPG out
     * @return int out number
     */
    public function getOut() {
        if (empty($this->webContent))
            $this->getWebContent();
        
        if (empty($this->outs)) 
            $this->parseOut();

        return $this->outs;
    }

    /**
     * Return RPG url
     * @return array out url
     */
    public function getUrls() {
        if (empty($this->webContent))
            $this->getWebContent();
        
        if (empty($this->urls))
            $this->parseUrls();
        
        return $this->urls;
    }

    /**
     * Return RPG graph data
     * @return array RPG graph data as array('date', 'votes')
     */
    public function getGraph() {
        if (empty($this->webContent))
            $this->getWebContent();
        
        if (empty($this->graph)) 
            $this->parseGraph();
        
        return $this->graph;
    }

    /**
     * Return data as a JSON string
     * @return string the JSON string
     */
    public function toJSON() {
        $data = $this->toArray();
        return json_encode($data);
    }

    /**
     * Return data as a Array object
     * @return array the array object
     */
    public function toArray() {
        return array(
            'name'      => $this->getName(),
            'vote'      => $this->getVote(),
            'position'  => $this->getPosition(),
            'out'       => $this->getOut(),
            'urls'      => $this->getUrls(),
            'graphe'    => $this->getGraph()
        );
    }
}
?>