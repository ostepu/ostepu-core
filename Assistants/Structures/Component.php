<?php 


/**
 * @file Component.php contains the Component class
 */

/**
 * the component structure
 *
 * @author Till Uhlig
 */
class Component extends Object implements JsonSerializable
{

    /**
     * @var string $id the db component identifier
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
     * @var string $name the component name
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
     * @var string $address the component URL/address
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
     * @var string $option component options
     */
    private $option = null;

    /**
     * the $option getter
     *
     * @return the value of $option
     */
    public function getOption( )
    {
        return $this->option;
    }

    /**
     * the $option setter
     *
     * @param string $value the new value for $option
     */
    public function setOption( $value = null )
    {
        $this->option = $value;
    }

    /**
     * @var string $prefix the prefix with which the component operates
     */
    private $prefix = null;

    /**
     * the $prefix getter
     *
     * @return the value of $prefix
     */
    public function getPrefix( )
    {
        return $this->prefix;
    }

    /**
     * the $prefix setter
     *
     * @param string $value the new value for $prefix
     */
    public function setPrefix( $value = null )
    {
        $this->prefix = $value;
    }

    /**
     * @var Link[] $links the component connections to other components
     */
    private $links = array( );

    /**
     * the $links getter
     *
     * @return the value of $links
     */
    public function getLinks( )
    {
        return $this->links;
    }

    /**
     * the $links setter
     *
     * @param Link[] $value the new value for $links
     */
    public function setLinks( $value = null )
    {
        $this->links = $value;
    }
    
    private $status = null;
    public function getStatus( )
    {
        return $this->status;
    }
    public function setStatus( $value = null )
    {
        $this->status = $value;
    }

    /**
     * Creates an Component object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $id The id of the component.
     * @param string $name The component name.
     * @param string $address The address.
     * @param string $option The options.
     *
     * @return an component object
     */
    public static function createComponent( 
                                           $id,
                                           $name,
                                           $address,
                                           $option
                                           )
    {
        return new Component( array( 
                                    'id' => $id,
                                    'name' => $name,
                                    'address' => $address,
                                    'option' => $option
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
                     'CO_id' => 'id',
                     'CO_name' => 'name',
                     'CO_address' => 'address',
                     'CO_option' => 'option',
                     'CO_prefix' => 'prefix',
                     'CO_links' => 'links'
                     );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData( )
    {
        $values = '';

        if ( $this->id != null )
            $this->addInsertData( 
                                 $values,
                                 'CO_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->name != null )
            $this->addInsertData( 
                                 $values,
                                 'CO_name',
                                 DBJson::mysql_real_escape_string( $this->name )
                                 );
        if ( $this->address != null )
            $this->addInsertData( 
                                 $values,
                                 'CO_address',
                                 DBJson::mysql_real_escape_string( $this->address )
                                 );
        if ( $this->option != null )
            $this->addInsertData( 
                                 $values,
                                 'CO_option',
                                 DBJson::mysql_real_escape_string( $this->option )
                                 );

        if ( $values != '' ){
            $values = substr( 
                             $values,
                             1
                             );
        }
        return $values;
    }

    /**
     * returns a sting/string[] of the database primary key/keys
     *
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey( )
    {
        return'CO_id';
    }

    /**
     * the constructor
     *
     * @param $data an assoc array with the object informations
     */
    public function __construct( $data = array( ) )
    {
        if ( $data == null )
            $data = array( );

        foreach ( $data AS $key => $value ){
            if ( isset( $key ) ){
                if ( $key == 'links' ){
                    $this->{
                        $key
                        
                    } = Link::decodeLink( 
                                         $value,
                                         false
                                         );
                    
                } else {
                    $key = strtoupper($key[0]).substr($key,1);
                    $func = "set".$key;
                    $this->$func($value);
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
    public static function encodeComponent( $data )
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
    public static function decodeComponent( 
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
                $result[] = new Component( $value = null );
            }
            return $result;
            
        } else 
            return new Component( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->id !== null )
            $list['id'] = $this->id;
        if ( $this->name !== null )
            $list['name'] = $this->name;
        if ( $this->address !== null )
            $list['address'] = $this->address;
        if ( $this->option !== null )
            $list['option'] = $this->option;
        if ( $this->prefix !== null )
            $list['prefix'] = $this->prefix;
        if ( $this->links !== null && $this->links !== array( ) )
            $list['links'] = $this->links;
         if ( $this->status !== null )
            $list['status'] = $this->status;
        return array_merge($list,parent::jsonSerialize( ));
    }
}

 
?>

