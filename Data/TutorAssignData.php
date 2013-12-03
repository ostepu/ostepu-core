<?php

$tutorAssignment = array(
    “tutor“ => array("userID"=>"ivhoj",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Rae",
                     "lastName"=>"Wooten",
                     "title"=>"Prof."
                    ),
    “submissionIDs“ => array(1, 2, 3, 4)
);

$tutorAssignment = array("tutorAssignment" => $tutorAssignment);

print json_encode($tutorAssignment);

?>