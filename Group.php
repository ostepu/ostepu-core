<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';
include 'include/Group/InvitationsGroupSheet.php';
include 'include/Group/InviteGroupSheet.php';
include_once 'include/Template.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492", 
                "75%");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$invitations = array(array( 
                     "user" => array("userID"=>"rvjbr",
                                     "email"=>"id.erat@mauris.co.uk",
                                     "firstName"=>"Colton",
                                     "lastName"=>"Gordon",
                                     "title"=>"Dr."), 
                     "leader" => array("userID"=>"tfead",
                                       "email"=>"libero@antebladitviverra.net",
                                       "firstName"=>"Yuli",
                                       "lastName"=>"Burris",
                                       "title"=>"Dr."), 
                     "sheetID" => ""));

$group = array("members" => array(
               array("userID"=>"ychwa",
                     "email"=>"malesuada.fames@IntegerurnaVivamus.net",
                     "firstName"=>"Walter",
                     "lastName"=>"Hampton",
                     "title"=>"Prof. Dr."),
               array("userID"=>"cqadv",
                     "email"=>"quam.Curabitur.vel@arcu.ca",
                     "firstName"=>"Tarik",
                     "lastName"=>"Harris",
                     "title"=>"PD"),
               array("userID"=>"mdgmt",
                     "email"=>"lorem@et.com",
                     "firstName"=>"Kathleen",
                     "lastName"=>"Ayers",
                     "title"=>""),
               array("userID"=>"tdspc",
                     "email"=>"tortor.at@mifelisadipiscing.net",
                     "firstName"=>"Chaim",
                     "lastName"=>"Guy",
                     "title"=>"PD")),
               "leaderID" => "mdgmt",
               "sheetID" => "");

// construct a content element for managing groups
$manageGroup = Template::WithTemplateFile('include/Group/ManageGroup.template.json');
$manageGroup->bind($group);

// construct a content element for creating groups
$createGroup = new InviteGroupSheet();

// construct a content element for joining groups
$invitations = new InvitationsGroupSheet($invitations);

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->show();
?>

