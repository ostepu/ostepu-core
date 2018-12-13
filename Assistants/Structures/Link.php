<?php
/**
 * @file Link.php contains the Link class
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
 * the link structure
 */
class Link extends StructureObject implements JsonSerializable
{

    /**
     * @var string $id the db id of the link
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
     * @var string $target the target component id
     */
    private $target = null;

    /**
     * the $target getter
     *
     * @return the value of $target
     */
    public function getTarget( )
    {
        return $this->target;
    }

    /**
     * the $target setter
     *
     * @param string $value the new value for $target
     */
    public function setTarget( $value = null )
    {
        $this->target = $value;
    }

    /**
     * @var string $id the link owner component id
     */
    private $owner = null;

    /**
     * the $owner getter
     *
     * @return the value of $owner
     */
    public function getOwner( )
    {
        return $this->owner;
    }

    /**
     * the $owner setter
     *
     * @param string $value the new value for $owner
     */
    public function setOwner( $value = null )
    {
        $this->owner = $value;
    }

    /**
     * @var string $name the link name
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
     * (description)
     *
     * @param $conf (description)
     */
    public function setName( $value = null )
    {
        $this->name = $value;
    }

    /**
     * @var string $address the URL/address of the target component
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
     * @var string $relevanz an optional attribute for components who want to differentiate their links
     */
    private $relevanz = null;

    /**
     * the $relevanz getter
     *
     * @return the value of $relevanz
     */
    public function getRelevanz( )
    {
        return $this->relevanz;
    }

    /**
     * the $relevanz setter
     *
     * @param string $value the new value for $relevanz
     */
    public function setRelevanz( $value = null )
    {
        $this->relevanz = $value;
    }

    /**
     *  @var string $prefix the prefix with which the component operates
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


    private $targetName = null;
    public function getTargetName( )
    {
        return $this->targetName;
    }
    public function setTargetName( $value = null )
    {
        $this->targetName = $value;
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

    // default: 100, low value = high priority, high value = low priority
    private $priority = null;
    public function getPriority( )
    {
        return $this->priority;
    }
    public function setPriority( $value = null )
    {
        $this->priority = $value;
    }

    private $path = null;
    public function getPath( )
    {
        return $this->path;
    }
    public function setPath( $value = null )
    {
        $this->path = $value;
    }

    /**
     * Creates an Link object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $id The id of the link.
     * @param string $owner The id of the owner.
     * @param string $target The id of the target.
     * @param string $name The link name.
     * @param string $relevanz The relevanz.
     *
     * @return an link object
     */
    public static function createLink(
                                      $id,
                                      $owner,
                                      $target,
                                      $name,
                                      $relevanz,
                                      $priority = 100,
                                      $path = null
                                      )
    {
        return new Link( array(
                               'id' => $id,
                               'owner' => $owner,
                               'target' => $target,
                               'name' => $name,
                               'relevanz' => $relevanz,
                               'priority' => $priority,
                               'path' => $path
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
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert( )
    {
        return array(
                     'CL_id' => 'id',
                     'CL_name' => 'name',
                     'CL_address' => 'address',
                     'CL_prefix' => 'prefix',
                     'CO_id_owner' => 'owner',
                     'CO_id_target' => 'target',
                     'CL_relevanz' => 'relevanz',
                     'CL_targetName' => 'targetName',
                     'CL_priority' => 'priority',
                     'CL_path' => 'path'
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
                                 'CL_id',
                                 DBJson::mysql_real_escape_string( $this->id )
                                 );
        if ( $this->name != null )
            $this->addInsertData(
                                 $values,
                                 'CL_name',
                                 DBJson::mysql_real_escape_string( $this->name )
                                 );
        if ( $this->owner != null )
            $this->addInsertData(
                                 $values,
                                 'CO_id_owner',
                                 DBJson::mysql_real_escape_string( $this->owner )
                                 );
        if ( $this->target != null )
            $this->addInsertData(
                                 $values,
                                 'CO_id_target',
                                 DBJson::mysql_real_escape_string( $this->target )
                                 );
        if ( $this->relevanz != null )
            $this->addInsertData(
                                 $values,
                                 'CL_relevanz',
                                 DBJson::mysql_real_escape_string( $this->relevanz )
                                 );
        if ( $this->priority != null )
            $this->addInsertData(
                                 $values,
                                 'CL_priority',
                                 DBJson::mysql_real_escape_string( $this->priority )
                                 );
        if ( $this->path != null )
            $this->addInsertData(
                                 $values,
                                 'CL_path',
                                 DBJson::mysql_real_escape_string( $this->path )
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
        return'CL_id';
    }

    /**
     * encodes an object to json
     *
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeLink( $data )
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
    public static function decodeLink(
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
                $result[] = new Link( $value );
            }
            return $result;

        } else
            return new Link( $data );
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
        if ( $this->target !== null )
            $list['target'] = $this->target;
        if ( $this->prefix !== null )
            $list['prefix'] = $this->prefix;
        if ( $this->priority !== null )
            $list['priority'] = $this->priority;
        if ( $this->owner !== null )
            $list['owner'] = $this->owner;
        if ( $this->relevanz !== null )
            $list['relevanz'] = $this->relevanz;
        if ( $this->targetName !== null )
            $list['targetName'] = $this->targetName;
        if ( $this->classFile !== null )
            $list['classFile'] = $this->classFile;
        if ( $this->className !== null )
            $list['className'] = $this->className;
        if ( $this->localPath !== null )
            $list['localPath'] = $this->localPath;
        if ( $this->path !== null )
            $list['path'] = $this->path;

        return array_merge($list,parent::jsonSerialize( ));
    }
}

 