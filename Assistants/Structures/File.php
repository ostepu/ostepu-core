<?php 


/**
 * @file File.php contains the File class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the file structure
 *
 * @author Till Uhlig
 * @author Florian Lücke
 * @date 2013-2014
 */
class File extends Object implements JsonSerializable
{

    /**
     * @var string $fileId An id that identifies the file.
     */
    private $fileId = null;

    /**
     * the $fileId getter
     *
     * @return the value of $fileId
     */
    public function getFileId( )
    {
        return $this->fileId;
    }

    /**
     * the $fileId setter
     *
     * @param string $value the new value for $fileId
     */
    public function setFileId( $value = null )
    {
        $this->fileId = $value;
    }

    /**
     * @var string $displayName The name that should be displayed for the file.
     */
    private $displayName = null;

    /**
     * the $displayName getter
     *
     * @return the value of $displayName
     */
    public function getDisplayName( )
    {
        return $this->displayName;
    }

    /**
     * the $displayName setter
     *
     * @param string $value the new value for $displayName
     */
    public function setDisplayName( $value = null )
    {
        $this->displayName = $value;
    }

    /**
     * @var string $address The URL of the file
     */
    private $address = null;

    /**
     * the $address getter
     *
     * @return the value of $address
     */
    public function getAddress( )
    {
        return $this->address;
    }

    /**
     * the $address setter
     *
     * @param string $value the new value for $address
     */
    public function setAddress( $value = null )
    {
        $this->address = $value;
    }

    /**
     * @var date $timeStamp When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
     */
    private $timeStamp = null;

    /**
     * the $timeStamp getter
     *
     * @return the value of $timeStamp
     */
    public function getTimeStamp( )
    {
        return $this->timeStamp;
    }

    /**
     * the $timeStamp setter
     *
     * @param date $value the new value for $timeStamp
     */
    public function setTimeStamp( $value = null )
    {
        $this->timeStamp = $value;
    }

    /**
     * @var int $fileSize the size of the file.
     */
    private $fileSize = null;

    /**
     * the $fileSize getter
     *
     * @return the value of $fileSize
     */
    public function getFileSize( )
    {
        return $this->fileSize;
    }

    /**
     * the $fileSize setter
     *
     * @param int $value the new value for $fileSize
     */
    public function setFileSize( $value = null )
    {
        $this->fileSize = $value;
    }

    /**
     * @var string $hash hash of the file, ensures that the user has up-/downloaded the right
     * file.
     */
    private $hash = null;

    /**
     * the $hash getter
     *
     * @return the value of $hash
     */
    public function getHash( )
    {
        return $this->hash;
    }

    /**
     * the $hash setter
     *
     * @param string $value the new value for $hash
     */
    public function setHash( $value = null )
    {
        $this->hash = $value;
    }

    /**
     * @var string $body content (base64 encoded)
     */
    private $body = null;

    /**
     * the $body getter
     *
     * @return the value of $body
     */
    public function getBody( )
    {
        return $this->body;
    }

    /**
     * the $body setter
     *
     * @param string $value the new value for $body
     */
    public function setBody( $value = null )
    {
        $this->body = $value;
    }

    /**
     * @var string $comment a file comment
     */
    private $comment = null;

    /**
     * the $comment getter
     *
     * @return the value of $comment
     */
    public function getComment( )
    {
        return $this->comment;
    }

    /**
     * the $comment setter
     *
     * @param string $value the new value for $comment
     */
    public function setComment( $value = null )
    {
        $this->comment = $value;
    }
    
    
    /**
     * @var string $mimeType a file mime type
     */
    private $mimeType = null;

    /**
     * the $mimeType getter
     *
     * @return the value of $mimeType
     */
    public function getMimeType( )
    {
        return $this->mimeType;
    }

    /**
     * the $mimeType setter
     *
     * @param string $value the new value for $mimeType
     */
    public function setMimeType( $value = null )
    {
        $this->mimeType = $value;
    }
    
    /**
     * Creates an File object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $fileId The id of the file.
     * @param string $displayName The display name.
     * @param string $address The address.
     * @param string $timeStamp The time stamp.
     * @param string $fileSize The file size.
     * @param string $hash The hash.
     * @param string $comment The file comment.
     * @param string $mimeType The file mime type.
     *
     * @return an file object
     */
    public static function createFile( 
                                      $fileId,
                                      $displayName,
                                      $address,
                                      $timeStamp,
                                      $fileSize,
                                      $hash,
                                      $comment = null,
                                      $mimeType = null
                                      )
    {
        return new File( array( 
                               'fileId' => $fileId,
                               'displayName' => $displayName,
                               'address' => $address,
                               'timeStamp' => $timeStamp,
                               'fileSize' => $fileSize,
                               'hash' => $hash,
                               'comment' => $comment,
                               'mimeType' => $mimeType
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
                     'F_id' => 'fileId',
                     'F_displayName' => 'displayName',
                     'F_address' => 'address',
                     'F_timeStamp' => 'timeStamp',
                     'F_fileSize' => 'fileSize',
                     'F_hash' => 'hash',
                     'F_body' => 'body',
                     'F_comment' => 'comment',
                     'F_mimeType' => 'mimeType'
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

        if ( $this->fileId !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_id',
                                 DBJson::mysql_real_escape_string( $this->fileId )
                                 );
        if ( $this->displayName !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_displayName',
                                 DBJson::mysql_real_escape_string( $this->displayName )
                                 );
        if ( $this->address !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_address',
                                 DBJson::mysql_real_escape_string( $this->address )
                                 );
        if ( $this->timeStamp !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_timeStamp',
                                 DBJson::mysql_real_escape_string( $this->timeStamp )
                                 );
        if ( $this->fileSize !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_fileSize',
                                 DBJson::mysql_real_escape_string( $this->fileSize )
                                 );
        if ( $this->hash !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_hash',
                                 DBJson::mysql_real_escape_string( $this->hash )
                                 );
        if ( $this->comment !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_comment',
                                 DBJson::mysql_real_escape_string( $this->comment )
                                 );
        if ( $this->mimeType !== null )
            $this->addInsertData( 
                                 $values,
                                 'F_mimeType',
                                 DBJson::mysql_real_escape_string( $this->mimeType )
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
        return'F_id';
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
    public static function encodeFile( $data )
    {
        if (is_array($data))reset($data);
        if (gettype($data) !== 'object' && !(is_array($data) && (current($data)===false || gettype(current($data)) === 'object'))){
            $e = new Exception();
            error_log(__FILE__.':'.__LINE__.' no object, '.gettype($data)." given\n".$e->getTraceAsString());            
            return null;
        }
        if ((is_array($data) && (is_array(current($data)) || (current($data)!==false && get_class(current($data)) !== get_called_class()))) || (!is_array($data) && get_class($data) !== get_called_class())){
            $e = new Exception();
            $class = (is_array($data) && is_array(current($data)) ? 'array' : (is_array($data) ? (current($data)!==false ? get_class(current($data)) : 'array') : get_class($data)));
            error_log(__FILE__.':'.__LINE__.' wrong type, '.$class.' given, '.get_called_class()." expected\n".$e->getTraceAsString());
            return null;
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
    public static function decodeFile( 
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
                $result[] = new File( $value );
            }
            return $result;
            
        } else 
            return new File( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->fileId !== null )
            $list['fileId'] = $this->fileId;
        if ( $this->displayName !== null )
            $list['displayName'] = $this->displayName;
        if ( $this->address !== null )
            $list['address'] = $this->address;
        if ( $this->timeStamp !== null )
            $list['timeStamp'] = $this->timeStamp;
        if ( $this->fileSize !== null )
            $list['fileSize'] = $this->fileSize;
        if ( $this->hash !== null )
            $list['hash'] = $this->hash;
        if ( $this->body !== null )
            $list['body'] = $this->body;
        if ( $this->comment !== null )
            $list['comment'] = $this->comment;
        if ( $this->mimeType !== null )
            $list['mimeType'] = $this->mimeType;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractFile( 
                                       $data,
                                       $singleResult = false
                                       )
    {

        // generates an assoc array of files by using a defined list of
        // its attributes
        $res = DBJson::getResultObjectsByAttributes( 
                                                    $data,
                                                    File::getDBPrimaryKey( ),
                                                    File::getDBConvert( )
                                                    );

        $res = File::decodeFile($res,false);
        if ( $singleResult == true ){

            // only one object as result
            if ( count( $res ) > 0 )
                $res = $res[0];
        }

        return $res;
    }
}

