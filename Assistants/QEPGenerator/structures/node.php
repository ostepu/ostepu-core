<?php

/**
 * @file node.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
class node implements JsonSerializable {

    /**
     * @var $id int Die eindeutige ID des Knotens.
     */
    public $id = null;

    /**
     * @var $name string Der Name des Knotens.
     */
    public $name = null;

    /**
     * @var $childs int[] Die IDs der Kinder dieses Knotens.
     */
    public $childs = array();

    /**
     * @var $parent int Die ID des Vaters oder null bei der Wurzel
     */
    public $parent = null;

    /**
     * @var $executionTime int Die Ausführungszeit im Knoten in ms
     * Dieser Wert muss aus $beginTime und $endTime berechnet werden
     */
    public $executionTime = null;

    /**
     * @var $beginTime int Der Zeitstempel,
     * bei dem der Knoten betreten wurde (in ms).
     */
    public $beginTime = null;

    /**
     * @var $endTime int Der Zeitstempel,
     * bei dem der Knoten verlassen wurde (in ms).
     */
    public $endTime = null;

    /**
     * @var $result string Das Berechnungsergebnis des Knotens.
     */
    public $result = null;

    /**
     * @var $input string Die Eingabe für den Knoten
     */
    public $input = null;

    /**
     * @var $resultHash string Der Hash von $result.
     */
    public $resultHash = null;

    /**
     * @var $nodeType string Der bestimmte Typ des Knotens
     * (Arbeiter, Quelle, ...)
     */
    public $nodeType = null;

    /**
     * @var $parallelGroup int Die ID der Gruppe, zu welcher der Knoten gehört
     * sofern er mit anderen Knoten in einer parallelen Anfrage
     * ausgeführt wurde
     */
    public $parallelGroup = null;

    /**
     * @var $method string Die HTTP Aufrufmethode des Knotens (GET, POST, ...)
     */
    public $method = null;

    /**
     * @var $URI string Die aufgerufene URI
     * Bsp.: /user
     */
    public $URI = null;
    
    /*
     * ein Teil des lokalen Pfades zur Komponente (für DBUser dann sowas wie
     * DB/DBUser)
     */
    public $path = null;
    
    /*
     * der mime-Type des Resultats
     */
    public $mimeType = null;
    
    /*
     * gibt an, ob das Resultat des Knotens im Cache hinterlegt wurde
     * true = ja, false = nein
     */
    public $storedResult = false;

    /**
     * @var $status Der HTTP-Status
     */
    public $status = null;

    /**
     * @var $label Hier kann für Berechnungen etwas vermerkt werden
     */
    public $label = null;
    
    public function hasChilds(){
        return count($this->childs)>0;
    }
    
    public function getChilds(){
        return $this->childs;
    }
    
    public function hasParent(){
        return $parent !== null;
    }

    /**
     * Prüft, ob der Knoten ein Blatt/Quelle ist
     *
     * @return true = ist ein Blatt, false = ist kein Blatt
     */
    public function isLeaf() {
        return empty($this->childs) ? true : false;
    }

    /**
     * Prüft, ob der Knoten die Wurzel ist
     *
     * @return true = ist die Wurzel, false = ist nicht die Wurzel
     */
    public function isRoot() {
        return $this->parent === null ? true : false;
    }

    /**
     * Fügt eine Kante an einen Knoten an
     */
    public function addEdge($newChildId) {
        if ($newChildId !== null) {
            if (!in_array($newChildId, $this->childs)) {
                $this->childs[] = $newChildId;
            }
        }
    }

    /*
     * berechnet den TAG des Knotens anhand der URL und der Methode
     * 
     * @return String der TAG
     */

    public function generateUTag() {
        return md5($this->method . '_' . $this->URL);
    }

    /*
     * berechnet einen TAG anhand des Inhalts (result)
     * 
     * @return String der TAG
     */

    public function generateETag() {
        if (!is_string($this->result)) {
            $this->result = json_encode($this->result);
        }
        $this->resultHash = md5($this->result);
        return $this->resultHash;
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct($data = array()) {
        if ($data === null) {
            $data = array();
        }

        foreach ($data as $key => $value) {
            if (isset($key)) {
                $func = 'set' . strtoupper($key[0]) . substr($key, 1);
                $methodVariable = array($this, $func);
                if (is_callable($methodVariable)) {
                    $this->$func($value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
        return $this;
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeNode($data, $extended = false) {
        return json_encode($data);
    }

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeNode($data, $decode = true) {
        if ($decode && $data === null) {
            $data = '{}';
        }

        if ($decode) {
            $data = json_decode($data);
        }

        if (is_array($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[] = new node($value);
            }
            return $result;
        } else {
            return new node($data);
        }
    }

    /**
     * dient der serialisierung des Objekts
     * 
     * @return ein assoziatives Array welches das Element darstellt
     */
    public function jsonSerialize() {
        $list = array();
        if ($this->id !== null) {
            $list['id'] = $this->id;
        }
        if ($this->name !== null) {
            $list['name'] = $this->name;
        }
        if ($this->childs !== null && $this->childs !== array()) {
            $list['childs'] = $this->childs;
        }
        if ($this->parent !== null) {
            $list['parent'] = $this->parent;
        }
        if ($this->executionTime !== null) {
            $list['executionTime'] = $this->executionTime;
        }
        if ($this->result !== null && !empty($this->result)) {
            $list['result'] = $this->result;
        }
        if ($this->resultHash !== null) {
            $list['resultHash'] = $this->resultHash;
        }
        if ($this->nodeType !== null) {
            $list['nodeType'] = $this->nodeType;
        }
        if ($this->parallelGroup !== null) {
            $list['parallelGroup'] = $this->parallelGroup;
        }
        if ($this->method !== null) {
            $list['method'] = $this->method;
        }
        if ($this->URI !== null) {
            $list['URI'] = $this->URI;
        }
        if ($this->status !== null) {
            $list['status'] = $this->status;
        }
        if ($this->input !== null && !empty($this->input)) {
            $list['input'] = $this->input;
        }
        if ($this->path !== null && !empty($this->path)) {
            $list['path'] = $this->path;
        }
        if ($this->mimeType !== null && !empty($this->mimeType)) {
            $list['mimeType'] = $this->mimeType;
        }
        if ($this->beginTime !== null && !empty($this->beginTime)) {
            $list['beginTime'] = $this->beginTime;
        }
        if ($this->endTime !== null && !empty($this->endTime)) {
            $list['endTime'] = $this->endTime;
        }
        if ($this->storedResult !== null && !empty($this->storedResult)) {
            $list['storedResult'] = $this->storedResult;
        }
        return $list;
    }

}
