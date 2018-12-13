<?php
/**
 * @file Component.php contains the Component class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */

include_once ( dirname( __FILE__ ) . '/StructureObject.php' );

/**
 * the component structure
 */
class Component extends StructureObject implements JsonSerializable
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
    public function setLinks( $value = array( ) )
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

    private $classFile = null;
    public function getClassFile( )
    {
        return $this->classFile;
    }
    public function setClassFile( $value = null )
    {
        $this->classFile = $value;
    }

    private $className = null;
    public function getClassName( )
    {
        return $this->className;
    }
    public function setClassName( $value = null )
    {
        $this->className = $value;
    }

    private $localPath = null;
    public function getLocalPath( )
    {
        return $this->localPath;
    }
    public function setLocalPath( $value = null )
    {
        $this->localPath = $value;
    }

    private $def = null;
    public function getDef( )
    {
        return $this->def;
    }
    public function setDef( $value = null )
    {
        $this->def = $value;
    }

    private $initialization = null;
    public function getInitialization( )
    {
        return $this->initialization;
    }
    public function setInitialization( $value = null )
    {
        $this->initialization = $value;
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
                                           $option,
                                           $def=null,
                                           $initialization=null
                                           )
    {
        return new Component( array(
                                    'id' => $id,
                                    'name' => $name,
                                    'address' => $address,
                                    'option' => $option,
                                    'def' => $def,
                                    'initialization' => $initialization
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
                     'CO_links' => 'links',
                     'CO_classFile' => 'classFile',
                     'CO_className' => 'className',
                     'CO_def' => 'def',
                     'CO_initialization' => 'initialization'
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
        if ( $this->def !== null && $this->def !== array() )
            $this->addInsertData(
                                 $values,
                                 'CO_def',
                                 DBJson::mysql_real_escape_string( $this->def )
                                 );
        if ( $this->initialization !== null && $this->initialization !== array() )
            $this->addInsertData(
                                 $values,
                                 'CO_initialization',
                                 DBJson::mysql_real_escape_string( $this->initialization )
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
        return 'CO_id';
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
                if ( $key == 'links' ){
                    $this->{
                        $key

                    } = Link::decodeLink(
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
    public static function encodeComponent( $data )
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
                $result[] = new Component( $value );
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
        if ( $this->classFile !== null )
            $list['classFile'] = $this->classFile;
        if ( $this->className !== null )
            $list['className'] = $this->className;
        if ( $this->localPath !== null )
            $list['localPath'] = $this->localPath;
        if ( $this->def !== null && $this->def !== array())
            $list['def'] = $this->def;
        if ( $this->initialization !== null && $this->initialization !== array())
            $list['initialization'] = $this->initialization;

        return array_merge($list,parent::jsonSerialize( ));
    }
}

 