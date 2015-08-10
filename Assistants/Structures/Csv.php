<?php


/**
 * @file Csv.php contains the csv class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the csv structure
 *
 * @author Till Uhlig
 * @date 2014
 */
class Csv extends Object implements JsonSerializable
{

    private $rows = null;
    public function getRows( )
    {
        return $this->rows;
    }
    public function setRows( $value = null )
    {
        $this->rows = $value;
    }

    /**
     * Creates an Exercise object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param [][] $rows.
     *
     * @return an exercise object
     */
    public static function createCsv(
                                          $rows
                                          )
    {
        return new Csv( array(
                                   'rows' => $rows
                                   ) );
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
    public static function encodeCsv( $data )
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
    public static function decodeCsv(
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
                $result[] = new Csv( $value );
            }
            return $result;

        } else
            return new Csv( $data );
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->rows !== null )
            $list['rows'] = $this->rows;
        return array_merge($list,parent::jsonSerialize( ));
    }
}
