<?php
/**
 * @file CSystem.php Contains the CSystem class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
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

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        $profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /timestamp',null));
        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->setRules($profiles['readonly']->getRules());
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        $profiles['public'] = GateProfile::createGateProfile(null,'public');
        $profiles['public']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /timestamp',null));
        return Model::isOk(array_values($profiles));
    }
}
