<?php
include 'include/Group/GroupSheet.php';

/**
 * 
 */
class InvitationsGroupSheet extends GroupSheet
{
    public function __construct(array $invitations)
    {
        $this->template = file_get_contents('include/Group/Invitations.template.html');

        $content = file_get_contents('include/Group/Invitation.template.html');

        foreach ($invitations as $invitation) {
            $this->content .= str_replace('%persons%', implode(", ", $invitation['persons']), $content);
        }

        $this->template = str_replace('%invitations%', $this->content, $this->template);
    }
}
?>