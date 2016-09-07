<?php
/**
 * @file Redirect.php contains the Redirect class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the Redirect structure
 */
class Redirect extends Object implements JsonSerializable
{

    /**
     * @var string $name db name of the Redirect
     */
    private $title = null;

    /**
     * the $name getter
     *
     * @return the value of $title
     */
    public function getTitle( )
    {
        return $this->title;
    }

    /**
     * the $title setter
     *
     * @param string $value the new value for $title
     */
    public function setTitle( $value = null )
    {
        $this->title = $value;
    }

    /**
     * @var string $url the redirect path
     */
    private $url = null;

    /**
     * the $url getter
     *
     * @return the value of $url
     */
    public function getUrl( )
    {
        return $this->url;
    }

    /**
     * the $url setter
     *
     * @param string $value the new value for $url
     */
    public function setUrl( $value = null )
    {
        $this->url = $value;
    }

    /**
     * @var string $id db id of the Setting
     */
    private $id = null;

    /**
     * the $id getter
     *
     * @return the value of $id
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */
    public function setId( $value = null )
    {
        $this->id = $value;
    }
    
    public static function getCourseFromRedirectId($Id)
    {
        $arr = explode('_',$Id);
        if (count($arr)==2){
            return $arr[0];
        }
        else
        return '';
    }

    public static function getIdFromRedirectId($Id)
    {
        $arr = explode('_',$Id);
        if (count($arr)==2){
            return $arr[1];
        }
        else
        return $Id;
    }

    public function getObjectCourseFromRedirectId()
    {
        return Setting::getCourseFromRedirectId($this->id);
    }

    public function getObjectIdFromRedirectId()
    {
        return Setting::getIdFromRedirectId($this->id);
    }

    /**

     * @var string $location The place where the link should be displayed.
     */
    private $location = null;

    /**
     * the $location getter
     *
     * @return the value of $location
     */
    public function getLocation( )
    {
        return $this->location;
    }

    /**
     * the $location setter
     *
     * @param string $value the new value for $location
     */
    public function setLocation( $value = null )
    {
        $this->location = $value;
    }

    /**

     * @var string $sublocation The place where the link should be displayed (additional condition).
     */
    private $sublocation = null;

    /**
     * the $sublocation getter
     *
     * @return the value of $sublocation
     */
    public function getSublocation( )
    {
        return $this->sublocation;
    }

    /**
     * the $sublocation setter
     *
     * @param string $value the new value for $sublocation
     */
    public function setSublocation( $value = null )
    {
        $this->sublocation = $value;
    }

    /**

     * @var string $condition A condition like 'expiredSheet', 'students' etc
     */
    private $condition = null;

    /**
     * the $condition getter
     *
     * @return the value of $condition
     */
    public function getCondition( )
    {
        return $this->condition;
    }

    /**
     * the $condition setter
     *
     * @param string $value the new value for $condition
     */
    public function setCondition( $value = null )
    {
        $this->condition = $value;
    }

    /**

     * @var string $style A style-attribute (css classes etc, not in use)
     */
    private $style    = null;

    /**
     * the $style getter
     *
     * @return the value of $style
     */
    public function getStyle( )
    {
        return $this->style;
    }

    /**
     * the $style setter
     *
     * @param string $value the new value for $style
     */
    public function setStyle( $value = null )
    {
        $this->style = $value;
    }

    /**
     * @var string $authentication db authentication-type of the Setting
     */
    private $authentication = null;

    /**
     * the $authentication getter
     *
     * @return the value of $authentication
     */
    public function getAuthentication( )
    {
        return $this->authentication;
    }

    /**
     * the $authentication setter
     *
     * @param string $value the new value for $authentication
     */
    public function setAuthentication( $value = null )
    {
        $this->authentication = $value;
    }

    /**
     * Creates an Redirect object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @return an Redirect object.
     */
    public static function createRedirect(
                                            $redirectId,
                                            $title,
                                            $url,
                                            $location=null,
                                            $authentication=null,
                                            $sublocation=null,
                                            $condition=null,
                                            $style=null
                                            )
    {
        return new Redirect( array(
                                     'id' => $redirectId,
                                     'title' => $title,
                                     'location' => $location,
                                     'url' => $url,
                                     'authentication' => $authentication,
                                     'sublocation' => $sublocation,
                                     'condition' => $condition,
                                     'style' => $style
                                     ) );
    }

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array(
                     'RED_id' => 'id',
                     'RED_title' => 'title',
                     'RED_location' => 'location',
                     'RED_url' => 'url',
                     'RED_authentication' => 'authentication',
                     'RED_sublocation' => 'sublocation',
                     'RED_condition' => 'condition',
                     'RED_style' => 'style'
                     );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData( $doubleEscaped=false )
    {
        $values = '';

        if ( $this->id !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_id',
                                 DBJson::mysql_real_escape_string( self::getIdFromSettingId($this->id) )
                                 );
        if ( $this->title !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_title',
                                 DBJson::mysql_real_escape_string( $this->title )
                                 );
        if ( $this->url !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_url',
                                 DBJson::mysql_real_escape_string( $this->url )
                                 );
        if ( $this->location !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_location',
                                 DBJson::mysql_real_escape_string( $this->location )
                                 );
        if ( $this->authentication !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_authentication',
                                 DBJson::mysql_real_escape_string( $this->authentication )
                                 );
        if ( $this->sublocation !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_sublocation',
                                 DBJson::mysql_real_escape_string( $this->sublocation )
                                 );
        if ( $this->condition !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_condition',
                                 DBJson::mysql_real_escape_string( $this->condition )
                                 );
        if ( $this->style !== null )
            $this->addInsertData(
                                 $values,
                                 'RED_style',
                                 DBJson::mysql_real_escape_string( $this->style )
                                 );

        if ( $values != '' ){
            $values = substr(
                             $values,
                             1
                             );
        }
        return ($doubleEscaped ? DBJson::mysql_real_escape_string($values) : $values);
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return 'RED_id';
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
    public static function encodeRedirect( $data )
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
    public static function decodeRedirect(
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
                $result[] = new Redirect( $value );
            }
            return $result;

        } else
            return new Redirect( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->title !== null )
            $list['title'] = $this->title;
        if ( $this->url !== null )
            $list['url'] = $this->url;
        if ( $this->location !== null )
            $list['location'] = $this->location;
        if ( $this->authentication !== null )
            $list['authentication'] = $this->authentication;
        if ( $this->sublocation !== null )
            $list['sublocation'] = $this->sublocation;
        if ( $this->condition !== null )
            $list['condition'] = $this->condition;
        if ( $this->style !== null )
            $list['style'] = $this->style;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractRedirect(
                                             $data,
                                             $singleResult = false,
                                             $RedirectExtension = '',
                                             $isResult = true
                                             )
    {
        // generates an assoc array of Settings by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      Redirect::getDBPrimaryKey( ),
                                                      Redirect::getDBConvert( ),
                                                      $RedirectExtension
                                                      );

        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Redirect::decodeRedirect($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 