<?php
/**
 * @file Reference.php contains the Reference class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

include_once ( dirname( __FILE__ ) . '/StructureObject.php' );
include_once dirname(__FILE__) . '/../fileUtils.php';

/**
 * the reference structure
 */
class Reference extends StructureObject implements JsonSerializable
{

    /**
     * @var string $localRef
     */
    private $localRef = null;

    /**
     * the $localRef getter
     *
     * @return the value of $localRef
     */
    private function getLocalRef( )
    {
        return $this->localRef;
    }

    /**
     * the $localRef setter
     *
     * @param string $value the new value for $localRef
     */
    private function setLocalRef( $value = null )
    {
        $this->localRef = $value;
    }

    /**
     * @var string $globalRef
     */
    private $globalRef = null;

    /**
     * the $globalRef getter
     *
     * @return the value of $globalRef
     */
    private function getGlobalRef( )
    {
        return $this->globalRef;
    }

    /**
     * the $globalRef setter
     *
     * @param string $value the new value for $globalRef
     */
    private function setGlobalRef( $value = null )
    {
        $this->globalRef = $value;
    }

    /**
     * @var string $ownerExternalUrl
     */
    private $ownerExternalUrl = null;

    /**
     * the $ownerExternalUrl getter
     *
     * @return the value of $ownerExternalUrl
     */
    private function getOwnerExternalUrl( )
    {
        return $this->ownerExternalUrl;
    }

    /**
     * the $ownerExternalUrl setter
     *
     * @param string $value the new value for $ownerExternalUrl
     */
    private function setOwnerExternalUrl( $value = null )
    {
        $this->ownerExternalUrl = $value;
    }

    public function getContent( )
    {
        // eine externe URL muss aufgelöst werden
        if ($this->ownerExternalUrl !== null){
            global $externalURI; // kommt aus UI/include/Config.php
            if ($this->ownerExternalUrl != $externalURI){
                // TODO: wir müssen die Datei beim Ziel abfragen
                // TODO: wir müssen die Datei beim Ziel abfragen
                // TODO: wir müssen die Datei beim Ziel abfragen
            }
        }
        
        return file_get_contents($this->localRef);
    }

    /**
     * Creates an reference object
     * Not needed attributes can be set to null.
     *
     * @param string $localReference The id of the user.
     * @param string $globalReference The id of the session.
     *
     * @return an reference object
     */
    public static function createReference(
                                         $localReference,
                                         $globalReference=null,
                                         $ownerExternalUrl=null
                                         )
    {
        $localReference = realpath($localReference);
        if ($globalReference === null){
            // wir müssen die Datei nun zusätzlich in einen reference Ordner im Dateisystem verschieben
            if (file_exists($localReference)){
                $targetHash = sha1($localReference);
                $target = fileUtils::generateFilePath('reference',$targetHash);
                
                global $filesPath; // kommt aus UI/include/Config.php
                
                // der Pfad muss existieren, damit wir dort unsere Datei hin verschieben können
                fileUtils::generatepath($filesPath.'/'.dirname($target));

                if (copy($localReference, $filesPath.'/'.$target)){
                    $globalReference = $target;
                    $localReference = $filesPath.'/'.$target;
                }
            }
        }
        
        global $externalURI; // kommt aus UI/include/Config.php
        
        if ($ownerExternalUrl === null){
            $ownerExternalUrl = $externalURI;
        }
        
        return new Reference( array(
                                  'localRef' => $localReference,
                                  'globalRef' => $globalReference,
                                  'ownerExternalUrl' => $ownerExternalUrl
                                  ) );
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
    public static function encodeReference( $data )
    {
        /*if (is_array($data))reset($data);
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
        }*/
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
    public static function decodeReference(
                                         $data,
                                         $decode = true
                                         )
    {
        if ( $decode &&
             $data == null )
            $data = '{}';

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
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new Reference( $value );
            }
            return $result;

        } else
            return new Reference( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->localRef !== null )
            $list['localRef'] = $this->localRef;
        if ( $this->globalRef !== null )
            $list['globalRef'] = $this->globalRef;
        if ( $this->ownerExternalUrl !== null )
            $list['ownerExternalUrl'] = $this->ownerExternalUrl;
        return array_merge($list,parent::jsonSerialize( ));
    }
}

 