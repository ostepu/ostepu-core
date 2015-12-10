<?php


/**
 * @file Invitation.php contains the Invitation class
 */

include_once ( dirname( __FILE__ ) . '/Object.php' );

/**
 * the invitation structure
 *
 * @author Till Uhlig
 * @author Florian Lücke
 * @date 2013-2014
 */
class Invitation extends Object implements JsonSerializable
{

    /**
     * @var User $member the user that was invited
     */
    private $member = null;

    /**
     * the $member getter
     *
     * @return the value of $user
     */
    public function getMember( )
    {
        return $this->member;
    }

    /**
     * the $member setter
     *
     * @param string $value the new value for $member
     */
    public function setMember( $value = null )
    {
        $this->member = $value;
    }

    /**
     * @var User $leader the user that created the group
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
     * @param string $value the new value for $leader
     */
    public function setLeader( $value = null )
    {
        $this->leader = $value;
    }

    /**
     * @var string $sheet the exercise sheet id
     */
    private $sheet = null;

    /**
     * the $sheet getter
     *
     * @return the value of $sheet
     */
    public function getSheet( )
    {
        return $this->sheet;
    }

    /**
     * the $sheet setter
     *
     * @param string $value the new value for $sheet
     */
    public function setSheet( $value = null )
    {
        $this->sheet = $value;
    }

    /**
     * Creates an Invitation object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $leaderId The id of the leader.
     * @param string $memberId The id of a member.
     * @param string $sheetId The id of the exercise sheet.
     *
     * @return an invitation object
     */
    public static function createInvitation(
                                            $leaderId,
                                            $memberId,
                                            $sheetId
                                            )
    {
        return new Invitation( array(
                                     'sheet' => $sheetId,
                                     'leader' => User::createUser(
                                                                  $leaderId,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null
                                                                  ),
                                     'member' => User::createUser(
                                                                  $memberId,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null,
                                                                  null
                                                                  )
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
                     'U_leader' => 'leader',
                     'U_member' => 'member',
                     'ES_id' => 'sheet'
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

        if ( $this->sheet != null )
            $this->addInsertData(
                                 $values,
                                 'ES_id',
                                 DBJson::mysql_real_escape_string( $this->sheet )
                                 );
        if ( $this->leader != null &&
             $this->leader->getId( ) != null )
            $this->addInsertData(
                                 $values,
                                 'U_id_member',
                                 DBJson::mysql_real_escape_string( $this->leader->getId( ) )
                                 );
        if ( $this->member != null &&
             $this->member->getId( ) != null )
            $this->addInsertData(
                                 $values,
                                 'U_id_leader',
                                 DBJson::mysql_real_escape_string( $this->member->getId( ) )
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
                     'ES_id',
                     'U_id2'
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
                if ( $key == 'member' ||
                     $key == 'leader' ){
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
    public static function encodeInvitation( $data )
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
    public static function decodeInvitation(
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
                $result[] = new Invitation( $value );
            }
            return $result;

        } else
            return new Invitation( $data );
    }

    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->member !== null )
            $list['member'] = $this->member;
        if ( $this->leader !== null )
            $list['leader'] = $this->leader;
        if ( $this->sheet !== null )
            $list['sheet'] = $this->sheet;
        return array_merge($list,parent::jsonSerialize( ));
    }

    public static function ExtractInvitation(
                                             $data,
                                             $singleResult = false,
                                             $LeaderExtension = '',
                                             $MemberExtension = '',
                                             $InvitationExtension = '',
                                             $isResult = true
                                             )
    {

        // generates an assoc array of users by using a defined list of
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

        // generates an assoc array of invitations by using a defined list of
        // its attributes
        $invitations = DBJson::getObjectsByAttributes(
                                                      $data,
                                                      Invitation::getDBPrimaryKey( ),
                                                      Invitation::getDBConvert( ),
                                                      $InvitationExtension
                                                      );

        // concatenates the invitations and the associated invitation leader
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $invitations,
                                                     Invitation::getDBPrimaryKey( ),
                                                     Invitation::getDBConvert( )['U_leader'],
                                                     $leader,
                                                     User::getDBPrimaryKey( ),
                                                     $InvitationExtension,
                                                     $LeaderExtension
                                                     );

        // concatenates the invitations and the associated invitation member
        $res = DBJson::concatObjectListsSingleResult(
                                                     $data,
                                                     $res,
                                                     Invitation::getDBPrimaryKey( ),
                                                     Invitation::getDBConvert( )['U_member'],
                                                     $member,
                                                     User::getDBPrimaryKey( ),
                                                     $MemberExtension.'2',
                                                     $InvitationExtension
                                                     );
        if ($isResult){
            // to reindex
            $res = array_values( $res );
            $res = Invitation::decodeInvitation($res,false);

            if ( $singleResult == true ){

                // only one object as result
                if ( count( $res ) > 0 )
                    $res = $res[0];
            }
        }

        return $res;
    }
}

 