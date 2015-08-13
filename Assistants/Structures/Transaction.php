<?php 


/**
 * @file Transaction.php contains the Transaction class
 */
 
include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the transaction structure
 *
 * @author Till Uhlig
 * @date 2014
 */
class Transaction extends Object implements JsonSerializable
{

    /**
     * @var string $transactionId db id of the transaction
     */
    private $transactionId = null;

    /**
     * the $transactionId getter
     *
     * @return the value of $transactionId
     */
    public function getTransactionId( )
    {
        return $this->transactionId;
    }

    /**
     * the $transactionId setter
     *
     * @param string $value the new value for $transactionId
     */
    public function setTransactionId( $value = null )
    {
        $this->transactionId = $value;
    }
    
        public static function getRandomFromTransactionId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==3){
            return $arr[2];
        }
        else
        return '';
    }
    
    public static function getCourseFromTransactionId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==3){
            return $arr[0];
        }
        else
        return '';
    }
    
    public static function getIdFromTransactionId($id)
    {
        $arr = explode('_',$id);
        if (count($arr)==3){
            return $arr[1];
        }
        else
        return $id;
    }
    
    public function getObjectRandomFromTransactionId()
    {
        return Transaction::getRandomFromTransactionId($this->id);
    }
    
    public function getObjectCourseFromTransactionId()
    {
        return Transaction::getCourseFromTransactionId($this->id);
    }
    
    public function getObjectIdFromTransactionId()
    {
        return Transaction::getIdFromTransactionId($this->id);
    }

    /**
     
     * @var string $durability .
     */
    private $durability = null;

    /**
     * the $durability getter
     *
     * @return the value of $durability
     */
    public function getDurability( )
    {
        return $this->durability;
    }

    /**
     * the $durability setter
     *
     * @param string $value the new value for $durability
     */
    public function setDurability( $value = null )
    {
        $this->durability = $value;
    }

    /**
     * @var string $authentication.
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
     * @param authentication $value the new value for $authentication
     */
    public function setAuthentication( $value = null )
    {
        $this->authentication = $value;
    }
    
    private $content = null;
    public function getContent( )
    {
        return $this->content;
    }
    public function setContent( $value = null )
    {
        $this->content = $value;
    }
    /**
     * Creates an Transaction object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $transactionId The ....
     * @param string $durability The ....
     * @param string $authentication The ....
     * @param string $content The ....
     *
     * @return an attachment object.
     */
    public static function createTransaction(
                                            $transactionId,
                                            $durability,
                                            $authentication,
                                            $content
                                            )
    {
        return new Transaction( array(
                                     'transactionId' => $transactionId,
                                     'durability' => $durability,
                                     'authentication' => $authentication,
                                     'content' => $content
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
                     'T_id' => 'transactionId',
                     'T_durability' => 'durability',
                     'T_authentication' => 'authentication',
                     'T_content' => 'content'
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

        if ( $this->transactionId != null )
            $this->addInsertData( 
                                 $values,
                                 'T_id',
                                 DBJson::mysql_real_escape_string( $this->transactionId )
                                 );
        if ( $this->durability != null )
            $this->addInsertData( 
                                 $values,
                                 'T_durability',
                                 DBJson::mysql_real_escape_string( $this->durability )
                                 );
        if ( $this->authentication != null)
            $this->addInsertData( 
                                 $values,
                                 'T_authentication',
                                 DBJson::mysql_real_escape_string( $this->authentication )
                                 );
        if ( $this->content != null)
            $this->addInsertData( 
                                 $values,
                                 'T_content',
                                 DBJson::mysql_real_escape_string( $this->content )
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
        return'T_id';
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
    public static function encodeTransaction( $data )
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
    public static function decodeTransaction( 
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
            reset($data);
            if (current($data)!==false && !is_int(key($data))) {
                $isArray = false;
            }
        }
        
        if ( $isArray && is_array( $data ) ){
            $result = array( );
            foreach ( $data AS $key => $value ){
                $result[] = new Transaction( $value );
            }
            return $result;
            
        } else 
            return new Transaction( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->transactionId !== null )
            $list['transactionId'] = $this->transactionId;
        if ( $this->durability !== null )
            $list['durability'] = $this->durability;
        if ( $this->authentication !== null )
            $list['authentication'] = $this->authentication;
        if ( $this->content !== null )
            $list['content'] = $this->content;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractTransaction( 
                                             $data,
                                             $singleResult = false,
                                             $TransactionExtension = '',
                                             $isResult = true
                                             )
    {

        // generates an assoc array of transactions by using a defined list of
        // its attributes
        $res = DBJson::getObjectsByAttributes( 
                                              $data,
                                              Transaction::getDBPrimaryKey( ),
                                              Transaction::getDBConvert( ),
                                              $TransactionExtension
                                              );

        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Transaction::decodeTransaction($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 