<?php

class Request_RequestObject
{
    
    /**
     * @var $method string Die Aufrufmethode (Bsp.: GET, POST, ...)
     */
    public $method;
    
    /**
     * @var $target string Die Zieladresse
     */
    public $target;
    
    /**
     * @var $header string[] Der Anfragekopf
     */
    public $header;
    
    /**
     * @var $content string Der Anfrageinhalt für POST und PUT
     */
    public $content;
    
    /**
     * @var $authbool bool true = sende Authorisierungsdaten, false = sonst
     */
    public $authbool;
    
    /**
     * @var $sessiondelete bool true = entferne die aktuellen Sessiondaten, false = sonst
     */
    public $sessiondelete;
    
    /**
     * Der Konstruktor
     *
     * @param string $method Die Methode
     * @param string $target Die Zieladresse
     * @param string[] $header Die Kopfdaten
     * @param string $content Der Anfrageinhalt
     * @param bool $authbool true = sende Authorisierung
     * @param bool $sessiondelete true = entferne Session
     */
    public function __construct($method, $target, $header, $content, $authbool = true, $sessiondelete = false)
    {
        $this->method=$method;
        $this->target=$target;
        $this->header=$header;
        $this->content=$content;
        $this->authbool=$authbool;
        $this->sessiondelete=$sessiondelete;
    }
    
    /**
     * Gibt das CURL Objekt zurück
     *
     * @return curl Das Objekt
     */
    public function get()
    {
        $ch = curl_init($this->target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

        // take the SESSION, DATE and USER fields from received header and 
        // add them to the header of our curl object
        $resultHeader = array();
                
        if ($this->authbool){
            if (isset($_SESSION['UID']))
                $resultHeader['USER'] = 'USER: ' . $_SESSION['UID'];
            if (isset($_SESSION['SESSION']))
                $resultHeader['SESSION'] = 'SESSION: ' . $_SESSION['SESSION'];
                                                
            if ($this->sessiondelete) {
                if (isset($_SERVER['REQUEST_TIME']))
                    $resultHeader['DATE'] = 'DATE: ' . $_SERVER['REQUEST_TIME'];
            } else {
                if (isset($_SESSION['LASTACTIVE']))
                    $resultHeader['DATE'] = 'DATE: ' . $_SESSION['LASTACTIVE'];
            }
        }
        
        if (isset($_SERVER['HTTP_SESSION']) && !in_array('SESSION',$resultHeader))
            $resultHeader['SESSION'] = 'SESSION: ' . $_SERVER['HTTP_SESSION'];
        if (isset($_SERVER['HTTP_USER']) && !in_array('USER',$resultHeader))
            $resultHeader['USER'] = 'USER: ' . $_SERVER['HTTP_USER'];
        if (isset($_SERVER['HTTP_DATE']) && !in_array('DATE',$resultHeader))
            $resultHeader['DATE'] = 'DATE: ' . $_SERVER['HTTP_DATE'];
            
        $resultHeader = array_values($resultHeader);    
        $resultHeader = array_merge($resultHeader,$this->header);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $resultHeader);
        
        if ($this->method == 'POST' || $this->method == 'PUT'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->content);
        }
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 

        curl_setopt($ch, CURLOPT_HEADER, 1);
        return $ch; 
    }
}