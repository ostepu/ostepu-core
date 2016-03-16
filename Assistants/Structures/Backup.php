<?php
/**
 * @file Backup.php contains the Backup class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the backup structure
 */
class Backup extends Object implements JsonSerializable
{

    /**
     * @var string $id a unique identifier for a backup
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

    /**
     * @var date $date the date on which the backup was created
     */
    private $date = null;

    /**
     * the $date getter
     *
     * @return the value of $date
     */
    public function getDate( )
    {
        return $this->date;
    }

    /**
     * the $date setter
     *
     * @param date $value the new value for $date
     */
    public function setDate( $value = null )
    {
        $this->date = $value;
    }

    /**
     * @var file $file a file where the backup is stored
     */
    private $file = null;

    /**
     * the $file getter
     *
     * @return the value of $file
     */
    public function getFile( )
    {
        return $this->file;
    }

    /**
     * the $file setter
     *
     * @param file $value the new value for $file
     */
    public function setFile( $value = null )
    {
        $this->file = $value;
    }

    /**
     * Creates an Backup object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $backupId The id of the backup.
     * @param string $date The date.
     * @param string $fileId The id of the backup file.
     *
     * @return an backup object
     */
    public static function createBackup(
                                        $backupId,
                                        $date,
                                        $fileId
                                        )
    {
        return new Backup( array(
                                 'id' => $backupId,
                                 'date' => $date,
                                 'file' => array( 'fileId' => $fileId )
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
                     'B_id' => 'id',
                     'B_date' => 'date',
                     'F_id_file' => 'file',

                     );
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return'B_id';
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
    public static function encodeBackup( $data )
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

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeBackup(
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
                $result[] = new Backup( $value );
            }
            return $result;

        } else
            return new Backup( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->date !== null )
            $list['date'] = $this->date;
        if ( $this->file !== null )
            $list['file'] = $this->file;
        return array_merge($list,parent::jsonSerialize( ));
    }
}

 