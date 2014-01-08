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
    array("tutor" => array("userID"=>"accce",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Felix",
                     "lastName"=>"Schääd",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(5, 6, 7, 8, 9)
          ),
    array("tutor" => array("userID"=>"abcde",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Florian",
                     "lastName"=>"Lücke",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
          ),
    array("tutor" => array("userID"=>"fdsfs",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Nelle",
                     "lastName"=>"Ashley",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(1, 2, 3, 4)
          ),
    array("tutor" => array("userID"=>"sdfde",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Mollie",
                     "lastName"=>"Schultz",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(5, 6, 7, 8, 9)
          ),
    array("tutor" => array("userID"=>"hgfhf",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Angela",
                     "lastName"=>"Wood",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
          ),
    array("tutor" => array("userID"=>"lokij",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Ivory",
                     "lastName"=>"Alford",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(5, 6, 7, 8, 9)
          ),
    array("tutor" => array("userID"=>"kmasm",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Doris",
                     "lastName"=>"Pitts",
                     "title"=>"Prof."
                    ),
          "submissionIDs" => array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
          )
);

$tutorAssignmentCount = rand(1, 5);

for ($i=0; $i < $tutorAssignmentCount; $i++) {
    $tutorAssignmentElements[] = $tutorAssignment[rand(0, count($tutorAssignment) - 1)];
}

$tutorAssignment = array("tutorAssignments" => $tutorAssignmentElements);

print json_encode($tutorAssignment);

?>