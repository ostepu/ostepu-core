<?php
/**
 * @file DBBeispiel.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.4
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

// bindet die Modellklasse und weitere wichtige ein (hier ist auch Slim enthalten)
include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

class DBBeispiel
{

    private $_component = null;
    public function __construct( )
    {
        // erzeugt das Model Objekt, welches einiges erleichtern soll
        $component = new Model('course', dirname(__FILE__), $this);
        $this->_component=$component;

        // startet nun unsere Komponente, dabei kommt die Zuordnung von Aufruf und Funktion
        // aus der Commands.json
        $component->run();
    }

    // ist aber nicht Aufrufbar
    public function editCourse( $callName, $input, $params = array() )
    {
        // ruft eine SQL Datei über den Ausgang 'out' auf
        return $this->_component->callSqlTemplate(
                                                  'out',    // der Name des Ausgangs, über welchen der Aufruf erfolgen soll
                                                  dirname(__FILE__).'/Sql/EditCourse.sql', // Pfad zur SQL Datei (ebenfalls Template, mit PHP kombinierbar)
                                                  array_merge($params,array('values' => $input->getInsertData( ))), // gibt diese Werte an das SQL Template
                                                  201, // eine positive Antwort der Anfrage wäre eine 201
                                                  'Model::isCreated', // dann soll unsere Antwort ebenfalls eine 201 (hier kann eigene aufzurufende Funktion angegeben werden, falls Antwort positiv ist)
                                                  array(new Course()), // bei positiver Antwort soll ein leeres Veranstaltungsobjekt zurückgegeben werden
                                                  'Model::isProblem', // wenn keine 201 zurückkommt, geben wir eine 409 zurück bzw. einen Problemcode
                                                  array(new Course()) // zur negativen Antwort noch ein leeres Veranstaltungsobjekt
                                                  );
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        // in diesem Beispiel wird der Name, welcher in der Commands.json für diesen Funktionsaufruf angegeben wurde,
        // direkt Verwendet, um einen gleichnamigen Ausgang der Component.json anzusprechen und den dort
        // angegeben Aufruf auzulösen (wobei courseid entsprechend ersetzt wird)

        // diese Funktion soll aufgerufen werden, wenn unsere Anfrage an die Datenbank positiv war
        $positive = function($input, $singleResult) {
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract course data from db answer
                    $result['content'] = array_merge($result['content'], Course::ExtractCourse( $inp->getResponse( ), $singleResult));
                    $result['status'] = 200;
                }
            }
            return $result;
        };

        // hier wird eine MySql stored-procedure aufgerufen
        // dabei haben die aufzurufen Befehle die Form /funktionsname/:idA/:idB  (stehen in der Component.json)
        // dabei werden idA und idB durch die Werte in $params ersetzt Bsp.: $params = array("idA"=>2, "idB"=>3)
        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, // der Name der Verknüpfungen welche aufgerufen werden soll
                                       $params, // übergibt eventuelle Parameter an das Aufruftemplate
                                       '', // einen Aufrufinhalt (content)
                                       200, // erwartete positive Antwort
                                       $positive, // falls positive Antwort, rufe diese Funktion auf
                                       array($singleResult), // übergib zusätzlich an $positive diese Parameter
                                       'Model::isProblem', // bei negativer Antwort eine Fehlernummer
                                       array(), // und bei negativer Antwort an die "Negativfunktion" diese Parameter übergeben
                                       'Query' // der Rückgabetyp der Anfrage, unsere Funktionen erhalten diesen Typ als $input (muss daher eine /Assistants/Structures sein)
                                       );
    }

    public function getDaten($callName, $input, $params = array())
    {
        return Model::isProblem();
    }

    public function getMatch($callName, $input, $params = array())
    {
        // soll GET Anfragen behandeln, welche nur ein einzelnes Ergebnis erwarten
        // (Bsp.: getUser oder getCourse als Einzelobjekt)
        return $this->get($callName,$callName,$params);
    }
    public function getMatchSingle($callName, $input, $params = array())
    {
        // soll GET Anfragen behandeln, welche eine mehrzahl von Ergebnissen erwarten
        // (Bsp.: getAllUsers als Array)
        return $this->get($callName,$callName,$params,true,false);
    }
}
