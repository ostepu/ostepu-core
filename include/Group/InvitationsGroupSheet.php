<?php
include_once 'include/Group/GroupSheet.php';

/**
 * 
 */
class InvitationsGroupSheet extends GroupSheet
{
    public function __construct()
    {

    }

    public function show()
    {
        $this->template = file_get_contents('include/Group/Invitations.template.html');

        parent::show();
    }
}
?>