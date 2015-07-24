<?php

class node extends Object implements JsonSerializable
{
    
    /**
     * @var $id int Die eindeutige ID des Knotens.
     */
    public $id=null;
    
    /**
     * @var $name string Der Name des Knotens.
     */
    public $name=null;
    
    /**
     * @var $childs int[] Die IDs der Kinder dieses Knotens.
     */
    public $childs=array();
    
    /**
     * @var $parent int Die ID des Vaters.
     */
    public $parent=null;  
    
    
    /**
     * @var $executionTime int Die Ausführungszeit im Knoten in ms
     * Dieser Wert muss aus $beginTime und $endTime berechnet werden
     */
    public $executionTime=null;
    
    /**
     * @var $beginTime int Der Zeitstempel, bei dem der Knoten betreten wurde (in ms).
     */
    public $beginTime=null;
    
    /**
     * @var $endTime int Der Zeitstempel, bei dem der Knoten verlassen wurde (in ms).
     */
    public $endTime=null;
    
    
    /**
     * @var $result string Das Berechnungsergebnis des Knotens.
     */
    public $result=null;
    
    /**
     * @var $resultHash string Der Hash von $result.
     */
    public $resultHash=null;
    
    /**
     * @var $nodeType string Der bestimmte Typ des Knotens (Arbeiter, Quelle, ...)
     */
    public $nodeType=null;
    
    
    
    /**
     * @var $parallelGroup int Die ID der Gruppe, zu welcher der Knoten gehört
     * sofern er mit anderen Knoten in einer parallelen Anfrage ausgeführt wurde
     */
    public $parallelGroup=null;
    
    /**
     * @var $method string Die HTTP Aufrufmethode des Knotens (GET, POST, ...)
     */
    public $method=null;
    
    /**
     * @var $URI string Die aufgerufene URI
     * Bsp.: /user
     */
    public $URI=null;    
    
    /**
     * @var $label Hier kann für Berechnungen etwas vermerkt werden
     */
    public $label=null;
    
    /**
     * Der Konstruktor
     *
     * @param string $name Der Name des Knotens
     */
    public function __construct( $name )
    {
        $this->name=$name;
    }
    
    /**
     * Prüft, ob der Knoten ein Blatt/Quelle ist
     *
     * @return true = ist ein Blatt, false = ist kein Blatt
     */
    public function isLeaf()
    {
        return empty($this->childs) ? true : false;
    }

    /**
     * Prüft, ob der Knoten die Wurzel ist
     *
     * @return true = ist die Wurzel, false = ist nicht die Wurzel
     */
    public function isRoot()
    {
       return $this->parent === null ? true : false; 
    }
    
    /**
     * ???
     */
    public function jsonSerialize( )
    {
        return array( 
                     'id'=>$this->id,
                     'name'=>$this->name,
                     'childs'=>$this->childs,
                     'parent'=>$this->parent,
                     'executionTime'=>$this->executionTime,
                     'result'=>$this->result,
                     'resultHash'=>$this->resultHash,
                     'nodeType'=>$this->nodeType,
                     'parallelGroup'=>$this->parallelGroup,
                     'method'=>$this->method,
                     'URI'=>$this->URI
                     );
    }
}