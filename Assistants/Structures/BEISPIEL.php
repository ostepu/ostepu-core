<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.4
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

// fügt die Objektklasse hinzu, hier sind noch allgemeine Eigenschaften enthalten (Statuscode, Antworttext etc.)
include_once ( dirname( __FILE__ ) . '/Object.php' );

class BEISPIEL extends Object implements JsonSerializable // muss eingebunden werden, damit das Objekt serialisierbar wird
{

    // Attribute sollten stets über getParam und setParam angesprochen werden
    private $param = null;
    public function getParam( )
    {
        return $this->param;
    }
    public function setParam( $value = null )
    {
        $this->param = $value;
    }

    // diese Funktionen sollen das Erstellen neuer Objekte erleichtern, vorallem wenn
    // die Strukturen aus verschiedenen Strukturen zusammengesetzt wurden und
    // einzelne Felder für einen Datenbankeintrag benötigt werden
    public static function createBEISPIEL( $newParam )
    {
        return new BEISPIEL( array('param' => $param ) );
    }

    // wandelt Datenbankfelder namentlich in Objektattribute um
    public static function getDbConvert( )
    {
        return array(
                     'P_pa' => 'param'
                     );
    }

    // wandelt die gesetzten Attribute des Objekts in eine Zusammenstellung
    // für einen UPDATE oder INSERT Befehl einer MySql Anweisung um
    public function getInsertData( $doubleEscaped=false )
    {
        $values = '';

        if ( $this->param !== null )
            $this->addInsertData(
                                 $values,
                                 'P_pa',
                                 DBJson::mysql_real_escape_string( $this->param )
                                 );

        if ( $values != '' ){
            $values = substr(
                             $values,
                             1
                             );
        }
        return ($doubleEscaped ? DBJson::mysql_real_escape_string($values) : $values);
    }

    // gibt den primären Datenbankschlüssel (eventuell auch ein array) der Struktur zurück
    public static function getDbPrimaryKey( )
    {
        return'P_pa';
    }

    // ruft passende set() Funktionen des Objekts auf, um dessen Attribute zu belegen
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

    // wandelt ein solches Objekt in eine Textdarstellung um (Serialisierung)
    public static function encodeBEISPIEL( $data )
    {
        if (is_array($data))reset($data);
        if (gettype($data) !== 'object' && !(is_array($data) && (current($data)===false || gettype(current($data)) === 'object'))){
            $e = new Exception();
            error_log(__FILE__.':'.__LINE__.' no object, '.gettype($data)." given\n".$e->getTraceAsString());           
            ///return null;
        }
        if ((is_array($data) && (is_array(current($data)) || (current($data)!==false && get_class(current($data)) !== get_called_class()))) || (!is_array($data) && get_class($data) !== get_called_class())){
            $e = new Exception();
            $class = (is_array($data) && is_array(current($data)) ? 'array' : (is_array($data) ? (current($data)!==false ? get_class(current($data)) : 'array') : get_class($data)));
            error_log(__FILE__.':'.__LINE__.' wrong type, '.$class.' given, '.get_called_class()." expected\n".$e->getTraceAsString());
            ///return null;
        }
        return json_encode( $data );
    }

    // wandelt die Textdarstellung des Objekts in ein Objekt um (Deserialisierung
    // ,behandelt auch Objektlisten
    public static function decodeBEISPIEL(
                                                   $data,
                                                   $decode = true
                                                   )
    {
        if ( $decode &&
             $data == null )
            $data = '{}'; // stellt sicher, dass übergebene Daten nicht zu einem Absturz führen

        if ( $decode )
            $data = json_decode( $data );

        $isArray = true;
        if ( !$decode ){
            if ($data !== null){
                reset($data);
                if (current($data)!==false && !is_int(key($data))) {
                    $isArray = false;
                }
            } else {
               $isArray = false;
            }
        }

        if ( $isArray && is_array( $data ) ){
            $result = array( ); // erzeugt eine Liste von Objekten
            foreach ( $data AS $key => $value ){
                $result[] = new BEISPIEL( $value );
            }
            return $result;

        } else // erzeugt ein einzelnes Objekt
            return new BEISPIEL( $data );
    }

    // bereitet die Attribute des Objekts für die
    // Serialisierung vor (nur belegte Felder sollen übertragen werden)
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->param !== null )
            $list['param'] = $this->param;

        // ruft auch die Serialisierung des darüber liegenden Objekts auf (Object.php)
        return array_merge($list,parent::jsonSerialize( ));
    }

    // wandelt ein assoziatives Array, welches einer Datenbankanfrage entstammt
    // anhand der DBConvert und der Primärschlüssel in Objekte um
    public static function ExtractBEISPIEL(
                                                    $data
                                                    )
    {

        $res = DBJson::getResultObjectsByAttributes(
                                                    $data,
                                                    BEISPIEL::getDBPrimaryKey( ),
                                                    BEISPIEL::getDBConvert( )
                                                    );
        return $res;
    }
}

 