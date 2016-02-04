<?php
/**
 * @file CSystem.php Contains the CSystem class
 *
 * @author Till Uhlig
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * 
 */
class CSystem
{

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    private $_component = null;
    
    /**
     * Der Konstruktor
     */
    public function __construct( )
    {
        $component = new Model('', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }
    
    /**
     * Liefert den aktuellen Zeitstempel
     *
     * @param string $callName Der Name des aufgerufenen Befehls
     * @param string $input Die Eingabe 
     * @param string[] $params Die Platzhalter des Aufrufs
     * @return string[] Positive Antwort mit Zeitstempel
     */
    public function getTimestamp( $callName, $input, $params = array())
    {
        // returns the currect timestamp
        return Model::isOk(array('timestamp'=>microtime(true)));
    }
}
