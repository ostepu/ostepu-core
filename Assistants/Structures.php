<?php 


/**
 * @file Structures.php contains the Object class and includes a lot of existing api structures
 *
 * @author Till Uhlig
 */

include_once ( dirname( __FILE__ ) . '/Structures/ApprovalCondition.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Attachment.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Backup.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Choice.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Component.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Course.php' );
include_once ( dirname( __FILE__ ) . '/Structures/CourseStatus.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Exercise.php' );
include_once ( dirname( __FILE__ ) . '/Structures/ExerciseFileType.php' );
include_once ( dirname( __FILE__ ) . '/Structures/ExerciseSheet.php' );
include_once ( dirname( __FILE__ ) . '/Structures/ExerciseType.php' );
include_once ( dirname( __FILE__ ) . '/Structures/ExternalId.php' );
include_once ( dirname( __FILE__ ) . '/Structures/File.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Form.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Group.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Invitation.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Link.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Marking.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Pdf.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Process.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Query.php' );
include_once ( dirname( __FILE__ ) . '/Structures/SelectedSubmission.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Session.php' );
include_once ( dirname( __FILE__ ) . '/Structures/Submission.php' );
include_once ( dirname( __FILE__ ) . '/Structures/TutorAssignment.php' );
include_once ( dirname( __FILE__ ) . '/Structures/User.php' );

/**
 * the Object class is the parent class of all api structures
 */
abstract class Object
{

    /**
     * Possibly unnecessary
     * @var string $sender a string that identifies who sent the object
     * @todo what do we do with the "sender" attribute
     *
     * type: string
     */
    private $sender;

    /**
     * the $sender getter
     *
     * @return the value of $sender
     */
    public function getSender( )
    {
        return $this->sender;
    }

    /**
     * the $sender setter
     *
     * @param string $value the new value for $sender
     */
    public function setSender( $_value )
    {
        $this->sender = $_value;
    }
    
    private $status;
    public function getStatus( )
    {
        return $this->status;
    }
    public function setStatus( $_value )
    {
        $this->status = $_value;
    }
    
    private $statusText;
    public function getStatusText( )
    {
        return $this->statusText;
    }
    public function setStatusText( $_value )
    {
        $this->statusText = $_value;
    }
    
    private $structure;
    public function getStructure( )
    {
        return $this->structure;
    }
    public function setStructure( $_value )
    {
        $this->structure = $_value;
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize( )
    {
        $list = array( );
        if ( $this->sender !== null )
            $list['sender'] = $this->sender;
        if ( $this->status !== null )
            $list['status'] = $this->status;
        if ( $this->statusText !== null )
            $list['statusText'] = $this->statusText;
        if ( $this->structure !== null )
            $list['structure'] = $this->structure;
        return $list;
    }

    /**
     * adds a string comma seperated to another
     *
     * @param string &$a the referenced var, where we have to add the assignment,
     * e.g. addInsertData($a,'a','1') -> $a = ',a=1'
     * @param string $b left part of assignment
     * @param string $c right part of assignment
     */
    protected function addInsertData( 
                                      & $a,
                                     $b,
                                     $c
                                     )
    {
        $a .= ',' . $b . '=\'' . $c . '\'';
    }
}

 
?>

