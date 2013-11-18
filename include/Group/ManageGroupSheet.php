<?php

include_once 'include/Group/GroupSheet.php';

/**
* 
*/
class ManageGroupSheet extends GroupSheet
{

    function __construct()
    {

    }

    public function show()
    {
        $this->template = file_get_contents('include/Group/ManageGroup.template.html');

        parent::show();
    }
}
?>