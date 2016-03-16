<?php
/**
 * @file Attachment.php contains the Attachment class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the attachment structure
 */
class Attachment extends Object implements JsonSerializable
{

    /**
     * @var string $id db id of the attachment
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

    public static function getCourseFromAttachmentId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[0];
        }
        else
        return '';
    }

    public static function getIdFromAttachmentId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==2){
            return $arr[1];
        }
        else
        return $id;
    }

    public function getObjectCourseFromAttachmentId()
    {
        return Attachment::getCourseFromAttachmentId($this->id);
    }

    public function getObjectIdFromAttachmentId()
    {
        return Attachment::getIdFromAttachmentId($this->id);
    }

    /**

     * @var string $exerciseId The id of the exercise this attachment belongs to.
     */
    private $exerciseId = null;

    /**
     * the $exerciseId getter
     *
     * @return the value of $exerciseId
     */
    public function getExerciseId( )
    {
        return $this->exerciseId;
    }

    /**
     * the $exerciseId setter
     *
     * @param string $value the new value for $exerciseId
     */
    public function setExerciseId( $value = null )
    {
        $this->exerciseId = $value;
    }

    /**
     * @var File $file The file of the attachment.
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

    private $processId = null;
    public function getProcessId( )
    {
        return $this->processId;
    }
    public function setProcessId( $value = null )
    {
        $this->processId = $value;
    }
    /**
     * Creates an Attachment object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $attachmentId The id of the attachment.
     * @param string $exerciseId The id of the exercise.
     * @param string $fileId The id of the fileId.
     *
     * @return an attachment object.
     */
    public static function createAttachment(
                                            $attachmentId,
                                            $exerciseId,
                                            $fileId,
                                            $processId
                                            )
    {
        return new Attachment( array(
                                     'id' => $attachmentId,
                                     'exerciseId' => $exerciseId,
                                     'file' => File::createFile(
                                                                $fileId,
                                                                null,
                                                                null,
                                                                null,
                                                                null,
                                                                null
                                                                ),
                                      'processId' => $processId
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
                     'A_id' => 'id',
                     'E_id' => 'exerciseId',
                     'F_file' => 'file',
                     'PRO_id' => 'processId'
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

        if ( $this->id != null )
            $this->addInsertData(
                                 $values,
                                 'A_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->exerciseId != null )
            $this->addInsertData(
                                 $values,
                                 'E_id',
                                 DBJson::mysql_real_escape_string( $this->exerciseId )
                                 );
        if ( $this->file != null &&
             $this->file->getFileId( ) != null )
            $this->addInsertData(
                                 $values,
                                 'F_id',
                                 DBJson::mysql_real_escape_string( $this->file->getFileId( ) )
                                 );
        if ( $this->processId != null)
            $this->addInsertData(
                                 $values,
                                 'PRO_id',
                                 DBJson::mysql_real_escape_string( Process::getIdFromProcessId($this->processId) )
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
        return'A_id';
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
                if ( $key == 'file' ){
                    $this->{
                        $key

                    } = File::decodeFile(
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
    public static function encodeAttachment( $data )
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
    public static function decodeAttachment(
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
                $result[] = new Attachment( $value );
            }
            return $result;

        } else
            return new Attachment( $data );
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
        if ( $this->exerciseId !== null )
            $list['exerciseId'] = $this->exerciseId;
        if ( $this->file !== null )
            $list['file'] = $this->file;
        if ( $this->processId !== null )
            $list['processId'] = $this->processId;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractAttachment(
                                             $data,
                                             $singleResult = false,
                                             $FileExtension = '',
                                             $AttachmentExtension = '',
                                             $isResult = true
                                             )
    {

        // generates an assoc array of files by using a defined list of
        // its attributes
        $files = DBJson::getObjectsByAttributes(
                                                $data,
                                                File::getDBPrimaryKey( ),
                                                File::getDBConvert( ),
                                                $FileExtension
                                                );

        // generates an assoc array of attachments by using a defined list of
        // its attributes
        $attachments = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      Attachment::getDBPrimaryKey( ),
                                                      Attachment::getDBConvert( ),
                                                      $AttachmentExtension
                                                      );

        // concatenates the attachments and the associated files
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $attachments,
                                                     Attachment::getDBPrimaryKey( ),
                                                     Attachment::getDBConvert( )['F_file'],
                                                     $files,
                                                     File::getDBPrimaryKey( ),
                                                     $FileExtension,
                                                     $AttachmentExtension
                                                     );

        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Attachment::decodeAttachment($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 