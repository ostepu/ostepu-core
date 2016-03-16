<?php
/**
 * @file Notification.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

/**
 * @file Notification.php contains the Notification class
 *

 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the Notification structure
 */
class Notification extends Object implements JsonSerializable
{

    /**
     * @var string $requiredStatus db requiredStatus of the Notification
     */
    private $requiredStatus = null;

    /**
     * the $requiredStatus getter
     *
     * @return the value of $requiredStatus
     */
    public function getRequiredStatus( )
    {
        return $this->requiredStatus;
    }

    /**
     * the $requiredStatus setter
     *
     * @param string $value the new value for $requiredStatus
     */
    public function setRequiredStatus( $value = null )
    {
        $this->requiredStatus = $value;
    }

    /**
     * @var string $begin db begin of the Notification
     */
    private $begin = null;

    /**
     * the $begin getter
     *
     * @return the value of $begin
     */
    public function getBegin( )
    {
        return $this->begin;
    }

    /**
     * the $begin setter
     *
     * @param string $value the new value for $begin
     */
    public function setBegin( $value = null )
    {
        $this->begin = $value;
    }

    /**
     * @var string $end db end of the Notification
     */
    private $end = null;

    /**
     * the $end getter
     *
     * @return the value of $end
     */
    public function getEnd( )
    {
        return $this->end;
    }

    /**
     * the $end setter
     *
     * @param string $value the new value for $end
     */
    public function setEnd( $value = null )
    {
        $this->end = $value;
    }
    
    /**
     * @var string $text db text of the Notification
     */
    private $text = null;

    /**
     * the $text getter
     *
     * @return the value of $text
     */
    public function getText( )
    {
        return $this->text;
    }

    /**
     * the $text setter
     *
     * @param string $value the new value for $text
     */
    public function setText( $value = null )
    {
        $this->text = $value;
    }

    /**
     * @var string $id db id of the Notification
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
    
    public static function getCourseFromNotificationId($Id)
    {
        $arr = explode('_',$Id);
        if (count($arr)==2){
            return $arr[0];
        }
        else
        return '';
    }

    public static function getIdFromNotificationId($Id)
    {
        $arr = explode('_',$Id);
        if (count($arr)==2){
            return $arr[1];
        }
        else
        return $Id;
    }

    public function getObjectCourseFromNotificationId()
    {
        return Notification::getCourseFromNotificationId($this->id);
    }

    public function getObjectIdFromNotificationId()
    {
        return Notification::getIdFromNotificationId($this->id);
    }

    /**
     * Creates an Notification object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $notificationText The text of the Notification.
     * @param string $notificationExpiration The expiration date
     *
     * @return an Notification object.
     */
    public static function createNotification(
                                            $notificationId,
                                            $notificationText,
                                            $notificationBegin,
                                            $notificationEnd,
                                            $notificationRequiredStatus
                                            )
    {
        return new Notification( array(
                                     'id' => $notificationId,
                                     'text' => $notificationText,
                                     'begin' => $notificationBegin,
                                     'end' => $notificationEnd,
                                     'requiredStatus' => $notificationRequiredStatus
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
                     'NOT_id' => 'id',
                     'NOT_text' => 'text',
                     'NOT_begin' => 'begin',
                     'NOT_end' => 'end',
                     'NOT_requiredStatus' => 'requiredStatus'
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
                                 'NOT_id',
                                 DBJson::mysql_real_escape_string( self::getIdFromNotificationId($this->id) )
                                 );
        if ( $this->text !== null )
            $this->addInsertData(
                                 $values,
                                 'NOT_text',
                                 DBJson::mysql_real_escape_string( $this->text )
                                 );
        if ( $this->begin !== null )
            $this->addInsertData(
                                 $values,
                                 'NOT_begin',
                                 DBJson::mysql_real_escape_string( $this->begin )
                                 );
        if ( $this->end !== null )
            $this->addInsertData(
                                 $values,
                                 'NOT_end',
                                 DBJson::mysql_real_escape_string( $this->end )
                                 );
        if ( $this->requiredStatus !== null )
            $this->addInsertData(
                                 $values,
                                 'NOT_requiredStatus',
                                 DBJson::mysql_real_escape_string( $this->requiredStatus )
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
        return 'NOT_id';
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
    public static function encodeNotification( $data )
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
    public static function decodeNotification(
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
                $result[] = new Notification( $value );
            }
            return $result;

        } else
            return new Notification( $data );
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
        if ( $this->text !== null )
            $list['text'] = $this->text;
        if ( $this->begin !== null )
            $list['begin'] = $this->begin;
        if ( $this->end !== null )
            $list['end'] = $this->end;
        if ( $this->requiredStatus !== null )
            $list['requiredStatus'] = $this->requiredStatus;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractNotification(
                                             $data,
                                             $singleResult = false,
                                             $NotificationExtension = '',
                                             $isResult = true
                                             )
    {
        // generates an assoc array of Notifications by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes(
                                              $data,
                                              Notification::getDBPrimaryKey( ),
                                              Notification::getDBConvert( ),
                                              $NotificationExtension
                                              );

        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Notification::decodeNotification($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 