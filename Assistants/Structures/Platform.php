<?php


/**
 * @file Platform.php contains the Platform class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );
 
/**
 * the platform structure
 *
 * @author Till Uhlig
 * @date 2014
 */
class Platform extends Object implements JsonSerializable
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
                                          $filesDirectory
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
    public function getInsertData( )
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
        if ( is_array( $data ) ){
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

?>