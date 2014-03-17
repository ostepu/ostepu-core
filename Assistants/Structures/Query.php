<?php 


/**
 * @file Query.php contains the Query class
 */

/**
 * this class is for querying SQL statemets
 *
 * @author Till Uhlig
 */
class Query extends Object implements JsonSerializable
{

    /**
     * @var string $request the sql statement
     */
    private $request = null;

    /**
     * the $request getter
     *
     * @return the value of $request
     */
    public function getRequest( )
    {
        return $this->request;
    }

    /**
     * the $request setter
     *
     * @param string $value the new value for $idrequest
     */
    public function setRequest( $value )
    {
        $this->request = $value;
    }

    /**
     * @var String[] $response a response
     * - ['content'] = the content/table you received from database
     * - ['affectedRows'] = the affected rows
     * - ['insertId'] = on post/insert with auto-increment, the id of the inserted entry
     * - ['errno'] = the error number
     * - ['error'] = the error message
     * - ['numRows'] = on get, the received number of rows
     */
    private $response = array( );

    /**
     * the $response getter
     *
     * @return the value of $response
     */
    public function getResponse( )
    {
        return $this->response;
    }

    /**
     * the $response setter
     *
     * @param string[] $value the new value for $response
     */
    public function setResponse( $value )
    {
        $this->response = $value;
    }

    /**
     * @var int $errno a error number
     */
    private $errno = null;

    /**
     * the $errno getter
     *
     * @return the value of $errno
     */
    public function getErrno( )
    {
        return $this->errno;
    }

    /**
     * the $errno setter
     *
     * @param int $value the new value for $errno
     */
    public function setErrno( $value )
    {
        $this->errno = $value;
    }

    /**
     * @var int $insertId the insert id
     */
    private $insertId = null;

    /**
     * the $insertId getter
     *
     * @return the value of $insertId
     */
    public function getInsertId( )
    {
        return $this->insertId;
    }

    /**
     * the $insertId setter
     *
     * @param int $value the new value for $insertId
     */
    public function setInsertId( $value )
    {
        $this->insertId = $value;
    }

    /**
     * @var int $affectedRows the affected rows
     */
    private $affectedRows = null;

    /**
     * the $affectedRows getter
     *
     * @return the value of $affectedRows
     */
    public function getAffectedRows( )
    {
        return $this->affectedRows;
    }

    /**
     * the $affectedRows setter
     *
     * @param int $value the new value for $affectedRows
     */
    public function setAffectedRows( $value )
    {
        $this->affectedRows = $value;
    }

    /**
     * @var int $numRows the number of received rows
     */
    private $numRows = null;

    /**
     * the $numRows getter
     *
     * @return the value of $numRows
     */
    public function getNumRows( )
    {
        return $this->numRows;
    }

    /**
     * the $numRows setter
     *
     * @param int $numRows the new value for $numRows
     */
    public function setNumRows( $value )
    {
        $this->numRows = $value;
    }

    /**
     * @var bool $checkSession specifies whether the session needs to be checked/tested
     */
    private $checkSession = null;

    /**
     * the $checkSession getter
     *
     * @return the value of $checkSession
     */
    public function getCheckSession( )
    {
        return $this->checkSession;
    }

    /**
     * the $checkSession setter
     *
     * @param bool $value the new value for $checkSession
     */
    public function setCheckSession( $value )
    {
        $this->checkSession = $value;
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
                $this->{
                    $key
                    
                } = $value;
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
    public static function encodeQuery( $data )
    {
        return json_encode( $data );
    }

    /**
     * decodes $data to an object
     *
     * @param string $data json encoded data (decode=true)
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     * @todo support query arrays
     *
     * @return the object
     */
    public static function decodeQuery( 
                                       $data,
                                       $decode = true
                                       )
    {
        if ( $decode && 
             $data == null )
            $data = '{}';

        if ( $decode )
            $data = json_decode( 
                                $data,
                                true
                                );

        $obj = new Query( );
        if ( is_array( $data ) && 
             !isset( $data['response'] ) && 
             !isset( $data['request'] ) && 
             !isset( $data['insertId'] ) ){
            
        } else {
            $obj = new Query( );
            if ( isset( $data['request'] ) )
                $obj->setRequest( $data['request'] );
            if ( isset( $data['response'] ) )
                $obj->setResponse( $data['response'] );
            if ( isset( $data['affectedRows'] ) )
                $obj->setAffectedRows( $data['affectedRows'] );
            if ( isset( $data['insertId'] ) )
                $obj->setInsertId( $data['insertId'] );
            if ( isset( $data['errno'] ) )
                $obj->setErrno( $data['errno'] );
            if ( isset( $data['numRows'] ) )
                $obj->setNumRows( $data['numRows'] );
            if ( isset( $data['checkSession'] ) )
                $obj->setCheckSession( $data['checkSession'] );
        }
        return $obj;
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->request !== null )
            $list['request'] = $this->request;
        if ( $this->response !== array( ) )
            $list['response'] = $this->response;
        if ( $this->affectedRows !== null )
            $list['affectedRows'] = $this->affectedRows;
        if ( $this->insertId !== null )
            $list['insertId'] = $this->insertId;
        if ( $this->errno !== null )
            $list['errno'] = $this->errno;
        if ( $this->numRows !== null )
            $list['numRows'] = $this->numRows;
        if ( $this->checkSession !== null )
            $list['checkSession'] = $this->checkSession;
        return $list;
    }
}

 
?>

