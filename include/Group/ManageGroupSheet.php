<?php

include_once 'include/Group/GroupSheet.php';

/**
* 
*/
class ManageGroupSheet extends GroupSheet
{

    function __construct($group)
    {
        $this->template = file_get_contents('include/Group/ManageGroup.template.html');

        $content = file_get_contents('include/Group/GroupMember.template.html');

        foreach ($group['members'] as $member) {
            $thisMemberName = $member['firstName'];
            $thisMemberName .= " " . $member['lastName'];
            $thisMember = str_replace('%name%', 
                                      $thisMemberName, 
                                      $content);

            $this->content .= $thisMember;
        }

        $this->template = str_replace('%members%',
                                      $this->content,
                                      $this->template);
    }
}
?>