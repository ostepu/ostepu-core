<?php

$user = array(
    array("user" => array("id"=>"ivhoj",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Rae",
                     "lastName"=>"Wooten"
                    ),
          "points" => array(
                    array("name"=>"Theoriepunkte",
                     "points"=>"10",
                     "maxpoints"=>"20",
                     "percentage"=>"50"
                    ),
                    array("name"=>"Praxispunkte",
                     "points"=>"20",
                     "maxpoints"=>"20",
                     "percentage"=>"100"
                    )
                    ),
          "approval" => "Ja"
          ),
    array("user" => array("id"=>"accce",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Felix",
                     "lastName"=>"Schääd"
                    ),
          "points" => array(
                    array("name"=>"Theoriepunkte",
                     "points"=>"10",
                     "maxpoints"=>"20",
                     "percentage"=>"50"
                    ),
                    array("name"=>"Praxispunkte",
                     "points"=>"20",
                     "maxpoints"=>"20",
                     "percentage"=>"100"
                    )
                    ),
          "approval" => "Ja"
          ),
    array("user" => array("id"=>"abcde",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Florian",
                     "lastName"=>"Lücke"
                    ),
          "points" => array(
                    array("name"=>"Theoriepunkte",
                     "points"=>"10",
                     "maxpoints"=>"20",
                     "percentage"=>"50"
                    ),
                    array("name"=>"Praxispunkte",
                     "points"=>"20",
                     "maxpoints"=>"20",
                     "percentage"=>"100"
                    )
                    ),
          "approval" => "Nein"
          ),
    array("user" => array("id"=>"fdsfs",
                     "email"=>"ac.tellus@actellusSuspendisse.co.uk",
                     "firstName"=>"Nelle",
                     "lastName"=>"Ashley"
                    ),
          "points" => array(
                    array("name"=>"Theoriepunkte",
                     "points"=>"10",
                     "maxpoints"=>"20",
                     "percentage"=>"50"
                    ),
                    array("name"=>"Praxispunkte",
                     "points"=>"20",
                     "maxpoints"=>"20",
                     "percentage"=>"100"
                    )
                    ),
          "approval" => "Nein"
          )
);

$userCount = rand(1, 5);

for ($i=0; $i < $userCount; $i++) { 
    $users[] = $user[rand(0, count($user) - 1)];
}

$user = array("users" => $users);

print json_encode($user);

?>