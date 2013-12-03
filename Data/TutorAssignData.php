<?php

$tutorAssignment = array(
    array("tutor" => array("userID"=>"ivhoj",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Rae",
                     "lastName"=>"Wooten",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(1, 2, 3, 4)
          ),
    array("tutor" => array("userID"=>"ivhoj",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Rae",
                     "lastName"=>"Wooten",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(5, 6, 7, 8)
          )
);

$tutorAssignment = array("tutorAssignments" => $tutorAssignment);

print json_encode($tutorAssignment);

?>