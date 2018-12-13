<?php
/**
 * @file Platform.php contains the Platform class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the platform structure
 */
class Platform extends StructureObject implements JsonSerializable
{

    private $baseUrl = null;

    /**
     * the $baseUrl getter
     *
     * @return the value of $baseUrl
     */
    public function getBaseUrl( )
    {
        return $this->baseUrl;
    }

    /**
     * the $baseUrl setter
     *
     * @param string $value the new value for $baseUrl
     */
    public function setBaseUrl( $value = null )
    {
        $this->baseUrl = $value;
    }

   
    private $databaseUrl = null;

    /**
     * the $databaseUrl getter
     *
     * @return the value of $databaseUrl
     */
    public function getDatabaseUrl( )
    {
        return $this->databaseUrl;
    }

    /**
     * the $databaseUrl setter
     *
     * @param string $value the new value for $databaseUrl
     */
    public function setDatabaseUrl( $value = null )
    {
        $this->databaseUrl = $value;
    }


    private $databaseName = null;

    /**
     * the $databaseName getter
     *
     * @return the value of $databaseName
     */
    public function getDatabaseName( )
    {
        return $this->databaseName;
    }

    /**
     * the $databaseName setter
     *
     * @param string $value the new value for $databaseName
     */
    public function setDatabaseName( $value = null )
    {
        $this->databaseName = $value;
    }

   
    private $databaseRootUser = null;

    /**
     * the $databaseRootUser getter
     *
     * @return the value of $databaseRootUser
     */
    public function getDatabaseRootUser( )
    {
        return $this->databaseRootUser;
    }

    /**
     * the $databaseRootUser setter
     *
     * @param string $value the new value for $databaseRootUser
     */
    public function setDatabaseRootUser( $value = null )
    {
        $this->databaseRootUser = $value;
    }


    private $databaseRootPassword = null;

    /**
     * the $databaseRootPassword getter
     *
     * @return the value of $databaseRootPassword
     */
    public function getDatabaseRootPassword( )
    {
        return $this->databaseRootPassword;
    }

    /**
     * the $databaseRootPassword setter
     *
     * @param string $value the new value for $databaseRootPassword
     */
    public function setDatabaseRootPassword( $value = null )
    {
        $this->databaseRootPassword = $value;
    }

   
    private $databaseOperatorUser = null;

    /**
     * the $databaseOperatorUser getter
     *
     * @return the value of $databaseOperatorUser
     */
    public function getDatabaseOperatorUser( )
    {
        return $this->databaseOperatorUser;
    }

    /**
     * the $databaseOperatorUser setter
     *
     * @param string $value the new value for $databaseOperatorUser
     */
    public function setDatabaseOperatorUser( $value = null )
    {
        $this->databaseOperatorUser = $value;
    }

   
    private $databaseOperatorPassword = null;

    /**
     * the $databaseOperatorPassword getter
     *
     * @return the value of $databaseOperatorPassword
     */
    public function getDatabaseOperatorPassword( )
    {
        return $this->databaseOperatorPassword;
    }

    /**
     * the $databaseOperatorPassword setter
     *
     * @param string $value the new value for $databaseOperatorPassword
     */
    public function setDatabaseOperatorPassword( $value = null )
    {
        $this->databaseOperatorPassword = $value;
    }

   
    private $tempDirectory = null;

    /**
     * the $tempDirectory getter
     *
     * @return the value of $tempDirectory
     */
    public function getTempDirectory( )
    {
        return $this->tempDirectory;
    }

    /**
     * the $tempDirectory setter
     *
     * @param string $value the new value for $tempDirectory
     */
    public function setTempDirectory( $value = null )
    {
        $this->tempDirectory = $value;
    }

   
    private $filesDirectory = null;

    /**
     * the $filesDirectory getter
     *
     * @return the value of $filesDirectory
     */
    public function getFilesDirectory( )
    {
        return $this->filesDirectory;
    }

    /**
     * the $filesDirectory setter
     *
     * @param string $value the new value for $filesDirectory
     */
    public function setFilesDirectory( $value = null )
    {
        $this->filesDirectory = $value;
    }

   
    private $externalUrl = null;

    /**
     * the $externalUrl getter
     *
     * @return the value of $externalUrl
     */
    public function getExternalUrl( )
    {
        return $this->externalUrl;
    }

    /**
     * the $externalUrl setter
     *
     * @param string $value the new value for $externalUrl
     */
    public function setExternalUrl( $value = null )
    {
        $this->externalUrl = $value;
    }


    private $settings = array();

    /**
     * the $settings getter
     *
     * @return the value of $settings
     */
    public function getSettings( )
    {
        return $this->settings;
    }

    /**
     * the $settings setter
     *
     * @param string $value the new value for $settings
     */
    public function setSettings( $value = null )
    {
        $this->settings = $value;
    }

    /**
     * Creates an patform object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $baseUrl The components URL.
     * @param string $databaseUrl The URL where the database is located.
     * @param string $databaseName The name of the database.
     * @param string $databaseRootUser The root user name.
     * @param string $databaseRootPassword The root password.
     * @param string $databaseOperatorUser The user name of the platform operator.
     * @param string $databaseOperatorPassword The password of the operator.
     *
     * @return a platform object
     */
    public static function createPlatform(
                                          $baseUrl,
                                          $databaseUrl,
                                          $databaseName,
                                          $databaseRootUser,
                                          $databaseRootPassword,
                                          $databaseOperatorUser,
                                          $databaseOperatorPassword,
                                          $tempDirectory,
                                          $filesDirectory,
                                          $externalUrl='',
                                          $settings=array()
                                          )
    {
        return new Platform( array(
                                   'baseUrl' => $baseUrl,
                                   'databaseUrl' => $databaseUrl,
                                   'databaseName' => $databaseName,
                                   'databaseRootUser' => $databaseRootUser,
                                   'databaseRootPassword' => $databaseRootPassword,
                                   'databaseOperatorUser' => $databaseOperatorUser,
                                   'databaseOperatorPassword' => $databaseOperatorPassword,
                                   'tempDirectory' => $tempDirectory,
                                   'filesDirectory' => $filesDirectory,
                                   'externalUrl' => $externalUrl,
                                   'settings' => $settings,
                                   ) );
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     * @todo currently not in use
     */
    public static function getDbConvert( )
    {
        return array();
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     * @todo currently not in use
     */
    public function getInsertData( $doubleEscaped=false )
    {
        return '';
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     * @todo currently not in use
     */
    public static function getDbPrimaryKey( )
    {
        return '';
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
    public static function encodePlatform( $data )
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
    public static function decodePlatform(
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
                $result[] = new Platform( $value );
            }
            return $result;

        } else
            return new Platform( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->baseUrl !== null )
            $list['baseUrl'] = $this->baseUrl;
        if ( $this->databaseUrl !== null )
            $list['databaseUrl'] = $this->databaseUrl;
        if ( $this->databaseName !== null )
            $list['databaseName'] = $this->databaseName;
        if ( $this->databaseRootUser !== null )
            $list['databaseRootUser'] = $this->databaseRootUser;
        if ( $this->databaseRootPassword !== null )
            $list['databaseRootPassword'] = $this->databaseRootPassword;
        if ( $this->databaseOperatorUser !== null )
            $list['databaseOperatorUser'] = $this->databaseOperatorUser;
        if ( $this->databaseOperatorPassword !== null )
             $list['databaseOperatorPassword'] = $this->databaseOperatorPassword;
        if ( $this->tempDirectory !== null )
             $list['tempDirectory'] = $this->tempDirectory;
        if ( $this->filesDirectory !== null )
             $list['filesDirectory'] = $this->filesDirectory;
        if ( $this->externalUrl !== null )
             $list['externalUrl'] = $this->externalUrl;
        if ( $this->settings !== null )
             $list['settings'] = $this->settings;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractPlatform(
                                           $data,
                                           $singleResult = false,
                                           $isResult = true
                                           )
    {
        return array();
    }
}
