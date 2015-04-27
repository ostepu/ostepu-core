<?php


/**
 * @file Plugin.php contains the Plugin class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the plugin structure
 *
 * @author Till Uhlig
 * @date 2014
 */
class Plugin extends Object implements JsonSerializable
{

    /**
     * @var string $name.
     */
    private $name = null;

    /**
     * the $name getter
     *
     * @return the value of $name
     */
    public function getName( )
    {
        return $this->name;
    }

    /**
     * the $name setter
     *
     * @param string $value the new value for $name
     */
    public function setName( $value = null )
    {
        $this->name = $value;
    }
    

    /**
     * @var string $version.
     */
    private $version = null;

    /**
     * the $version getter
     *
     * @return the value of $version
     */
    public function getVersion( )
    {
        return $this->version;
    }

    /**
     * the $version setter
     *
     * @param string $value the new value for $version
     */
    public function setVersion( $value = null )
    {
        $this->version = $value;
    }
    
    
    /**
     * @var string $versionDate.
     */
    private $versionDate = null;

    /**
     * the $versionDate getter
     *
     * @return the value of $versionDate
     */
    public function getVersionDate( )
    {
        return $this->versionDate;
    }

    /**
     * the $versionDate setter
     *
     * @param string $value the new value for $versionDate
     */
    public function setVersionDate( $value = null )
    {
        $this->versionDate = $value;
    }
    
    
    /**
     * @var string $author.
     */
    private $author = null;

    /**
     * the $author getter
     *
     * @return the value of $author
     */
    public function getAuthor( )
    {
        return $this->author;
    }

    /**
     * the $author setter
     *
     * @param string $value the new value for $author
     */
    public function setAuthor( $value = null )
    {
        $this->author = $value;
    }
    
    
    /**
     * @var string $sourceUrl.
     */
    private $sourceUrl = null;

    /**
     * the $sourceUrl getter
     *
     * @return the value of $sourceUrl
     */
    public function getSourceUrl( )
    {
        return $this->sourceUrl;
    }

    /**
     * the $sourceUrl setter
     *
     * @param string $value the new value for $sourceUrl
     */
    public function setSourceUrl( $value = null )
    {
        $this->sourceUrl = $value;
    }
    
    
    /**
     * @var string $updateUrl.
     */
    private $updateUrl = null;

    /**
     * the $updateUrl getter
     *
     * @return the value of $updateUrl
     */
    public function getUpdateUrl( )
    {
        return $this->updateUrl;
    }

    /**
     * the $updateUrl setter
     *
     * @param string $value the new value for $updateUrl
     */
    public function setUpdateUrl( $value = null )
    {
        $this->updateUrl = $value;
    }
    
    
    /**
     * @var string $requirements.
     */
    private $requirements = array();

    /**
     * the $requirements getter
     *
     * @return the value of $requirements
     */
    public function getRequirements( )
    {
        return $this->requirements;
    }

    /**
     * the $requirements setter
     *
     * @param string $value the new value for $requirements
     */
    public function setRequirements( $value = null )
    {
        $this->requirements = $value;
    }
    
    /**
     * Creates an Plugin object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $name The plugin name (unique).
     * @param string $version The version (e.g. 1.0).
     * @param string $versionDate The version date (e.g. 14.06.2014).
     * @param string $author The author or authors.
     * @param string $sourceUrl The source URL.
     * @param string $updateUrl The update URL.
     *
     * @return an plugin object
     */
    public static function createPlugin(
                                          $name,
                                          $version,
                                          $versionDate,
                                          $author,
                                          $sourceUrl,
                                          $updateUrl
                                          )
    {
        return new Form( array(
                                   'name' => $name,
                                   'version' => $version,
                                   'versionDate' => $versionDate,
                                   'author' => $author,
                                   'sourceUrl' => $sourceUrl,
                                   'updateUrl' => $updateUrl
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
        return array( );
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
                if ( $key == 'requirements' ){
                    $this->{
                        $key

                    } = Plugin::decodePlugin(
                                                     $value,
                                                     false
                                                     );

                } else {
                    $func = 'set' . strtoupper($key[0]).substr($key,1);
                    $methodVariable = array($this, $func);
                    if (is_callable($methodVariable)){
                        $this->$func($value);
                    } else
                        $this->{$key} = $value;
                }
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
    public static function encodePlugin( $data )
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
    public static function decodePlugin(
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
                $result[] = new Plugin( $value );
            }
            return $result;

        } else
            return new Plugin( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->name !== null )
            $list['name'] = $this->name;
        if ( $this->version !== null )
            $list['version'] = $this->version;
        if ( $this->versionDate !== null )
            $list['versionDate'] = $this->versionDate;
        if ( $this->author !== null )
            $list['author'] = $this->author;
        if ( $this->sourceUrl !== null )
            $list['sourceUrl'] = $this->sourceUrl;       
        if ( $this->updateUrl !== null )
            $list['updateUrl'] = $this->updateUrl;
        if ( $this->requirements !== array( ) &&
             $this->requirements !== null )
             $list['requirements'] = $this->requirements;

        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractPlugin(
                                           $data,
                                           $singleResult = false,
                                           $isResult = true
                                           )
    {
        return array();
    }
}
