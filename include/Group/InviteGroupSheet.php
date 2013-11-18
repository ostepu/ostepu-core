<?php
include_once 'include/Group/GroupSheet.php';

/**
* 
*/
class InviteGroupSheet extends GroupSheet
{

    public function __construct()
    {

    }

    public function show()
    {
        $this->template = file_get_contents('include/Group/InviteGroup.template.html');

        parent::show();  
    }
}
?>