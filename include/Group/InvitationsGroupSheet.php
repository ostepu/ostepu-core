<?php
include 'include/Group/GroupSheet.php';

/**
 * 
 */
class InvitationsGroupSheet extends GroupSheet
{
    public function __construct($invitations)
    {
        $this->template = file_get_contents('include/Group/Invitations.template.html');

        $content = file_get_contents('include/Group/Invitation.template.html');

        foreach ($invitations as $invitation) {
            $groupLeader = $invitation['leader']['firstName'];
            $groupLeader .= " ";
            $groupLeader .= $invitation['leader']['lastName'];

            $this->content .= str_replace('%leader%', 
                                          $groupLeader, 
                                          $content);
        }

        $this->template = str_replace('%invitations%', 
                                      $this->content, 
                                      $this->template);
    }
}
?>