<?php 


/**
 * @file Structures.php contains the Object class and includes a lot of existing api structures
 *
 * @author Till Uhlig
 * @date 2013-2014
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
    private $sender = null;

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
    public function setSender( $_value = null )
    {
        $this->sender = $_value;
    }
    
    private $status = null;
    public function getStatus( )
    {
        return $this->status;
    }
    public function setStatus( $_value = null )
    {
        $this->status = $_value;
    }
    
    private $messages = array();
    public function getMessages( )
    {
        return $this->messages;
    }
    public function setMessages( $_value = array() )
    {
        $this->messages = $_value;
    }
    public function addMessage($_value = null)
    {
        if (is_string($_value))
            $this->messages[] = $_value;
    }
    
    public function addMessages($_values = array())
    {
        foreach($_values as $val){
            $this->addMessage($val);
        }
    }
    
    private $structure = null;
    public function getStructure( )
    {
        return $this->structure;
    }
    public function setStructure( $_value = null )
    {
        $this->structure = $_value;
    }
    
    private $language = null;
    public function getLanguage( )
    {
        return $this->language;
    }
    public function setLanguage( $_value = null )
    {
        $this->language = $_value;
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
        if ( $this->messages !== null && $this->messages !== array())
            $list['messages'] = $this->messages;
        if ( $this->structure !== null )
            $list['structure'] = $this->structure;
         if ( $this->language !== null )
            $list['language'] = $this->language;
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

