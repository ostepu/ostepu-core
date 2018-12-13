<?php
/**
 * @file Group.php contains the Group class
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
 * the group structure
 */
class Group extends StructureObject implements JsonSerializable
{

    /**
     * @var User[] $members all members of the group of the current users
     */
    private $members = array( );

    /**
     * the $members getter
     *
     * @return the value of $members
     */
    public function getMembers( )
    {
        return $this->members;
    }

    /**
     * the $members setter
     *
     * @param User[] $value the new value for $members
     */
    public function setMembers( $value = array( ) )
    {
        $this->members = $value;
    }

    /**
     * @var User $leader the id of the user that is the leader of the group
     */
    private $leader = null;

    /**
     * the $leader getter
     *
     * @return the value of $leader
     */
    public function getLeader( )
    {
        return $this->leader;
    }

    /**
     * the $leader setter
     *
     * @param User $value the new value for $leader
     */
    public function setLeader( $value = null )
    {
        $this->leader = $value;
    }

    /**
     * @var string $sheetId the id of the sheet for which this group exists
     */
    private $sheetId = null;

    /**
     * the $sheetId getter
     *
     * @return the value of $sheetId
     */
    public function getSheetId( )
    {
        return $this->sheetId;
    }

    /**
     * the $sheetId setter
     *
     * @param string $value the new value for $sheetId
     */
    public function setSheetId( $value = null )
    {
        $this->sheetId = $value;
    }

    /**
     * Creates an Group object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $leaderId The id of the leader.
     * @param string $memberId The id of a member.
     * @param string $sheetId The id of the exercise sheet.
     *
     * @return an group object
     */
    public static function createGroup(
                                       $leaderId,
                                       $memberId,
                                       $sheetId
                                       )
    {
        return new Group( array(
                                'sheetId' => $sheetId,
                                'leader' => new User( array( 'id' => $leaderId ) ),
                                'members' => array( new User( array( 'id' => $memberId ) ) )
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
                     'U_member' => 'members',
                     'U_leader' => 'leader',
                     'ES_id' => 'sheetId'
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

        if ( $this->sheetId != null )
            $this->addInsertData(
                                 $values,
                                 'ES_id',
                                 DBJson::mysql_real_escape_string( $this->sheetId )
                                 );
        if ( $this->members != null &&
             $this->members != array( ) &&
             $this->members[0] != null )
            $this->addInsertData(
                                 $values,
                                 'U_id_leader',
                                 DBJson::mysql_real_escape_string( $this->members[0]->getId( ) )
                                 );
        if ( $this->leader != null &&
             $this->leader->getId( ) != null )
            $this->addInsertData(
                                 $values,
                                 'U_id_member',
                                 DBJson::mysql_real_escape_string( $this->leader->getId( ) )
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
        return array(
                     'U_id',
                     'ES_id'
                     );
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
                if ( $key == 'leader' ||
                     $key == 'members' ){
                    $this->{
                        $key

                    } = User::decodeUser(
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
    public static function encodeGroup( $data )
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
    public static function decodeGroup(
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
                $result[] = new Group( $value );
            }
            return $result;

        } else
            return new Group( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->members !== array( ) )
            $list['members'] = $this->members;
        if ( $this->leader !== null )
            $list['leader'] = $this->leader;
        if ( $this->sheetId !== null )
            $list['sheetId'] = $this->sheetId;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractGroup(
                                        $data,
                                        $singleResult = false,
                                        $LeaderExtension = '',
                                        $MemberExtension = '',
                                        $GroupExtension = '',
                                        $isResult = true
                                        )
    {

        // generates an assoc array of an user by using a defined list of
        // its attributes
        $leader = DBJson::getObjectsByAttributes(
                                                 $data,
                                                 User::getDBPrimaryKey( ),
                                                 User::getDBConvert( ),
                                                 $LeaderExtension
                                                 );

        // generates an assoc array of users by using a defined list of
        // its attributes
        $member = DBJson::getObjectsByAttributes(
                                                 $data,
                                                 User::getDBPrimaryKey( ),
                                                 User::getDBConvert( ),
                                                 $MemberExtension.'2'
                                                 );

        // generates an assoc array of groups by using a defined list of
        // its attributes
        $groups = DBJson::getObjectsByAttributes(
                                                 $data,
                                                 Group::getDBPrimaryKey( ),
                                                 Group::getDBConvert( ),
                                                 $GroupExtension
                                                 );

        // concatenates the groups and the associated group leader
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $groups,
                                                     Group::getDBPrimaryKey( ),
                                                     Group::getDBConvert( )['U_leader'],
                                                     $leader,
                                                     User::getDBPrimaryKey( ),
                                                     $LeaderExtension,
                                                     $GroupExtension
                                                     );

        // concatenates the groups and the associated group member
        $res = DBJson::concatResultObjectLists(
                                               $data,
                                               $res,
                                               Group::getDBPrimaryKey( ),
                                               Group::getDBConvert( )['U_member'],
                                               $member,
                                               User::getDBPrimaryKey( ),
                                               $MemberExtension.'2',
                                               $GroupExtension
                                               );
        if ($isResult){
            // to reindex
            $res = array_merge( $res );
            $res = Group::decodeGroup($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 