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
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct( $data = array( ) )
    {
        if ( $data === null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                $func = 'set' . strtoupper($key[0]).substr($key,1);
                $methodVariable = array($this, $func);
                if (is_callable($methodVariable)){
                    $this->$func($value);
                } else
                    $this->{$key} = $value;
            }
        }
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeNode( $data, $extended=false )
    {
        if (gettype($data) !== 'object'){
            error_log('no object, '.gettype($data).' given');
            return null;
        }
        if (get_class($data) !== 'Node'){
            error_log('wrong type, '.get_class($data).' given, Node expected');
            return null;
        }
        return json_encode( $data );
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
    public static function decodeNode( 
                                      $data,
                                      $decode = true
                                      )
    {
        if ( $decode && 
             $data == null )
            $data = '{}';

        if ( $decode )
            $data = json_decode( $data );

        if ( is_array( $data ) ){
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new Node( $value );
            }
            return $result;
            
        } else 
            return new Node( $data );
    }
    
    /**
     * ???
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null)
            $list['id'] = $this->id;
        if ( $this->name !== null)
            $list['name'] = $this->name;
        if ( $this->childs !== null && $this->childs !== array())
            $list['childs'] = $this->childs;
        if ( $this->parent !== null)
            $list['parent'] = $this->parent;
        if ( $this->executionTime !== null)
            $list['executionTime'] = $this->executionTime;
        if ( $this->result !== null)
            $list['result'] = $this->result;
        if ( $this->resultHash !== null)
            $list['resultHash'] = $this->resultHash;
        if ( $this->nodeType !== null)
            $list['nodeType'] = $this->nodeType;
        if ( $this->parallelGroup !== null)
            $list['parallelGroup'] = $this->parallelGroup;
        if ( $this->method !== null)
            $list['method'] = $this->method;
        if ( $this->URI !== null)
            $list['URI'] = $this->URI;
        return array_merge($list,parent::jsonSerialize( ));
    }
}