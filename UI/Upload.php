<?php
include_once 'include/Header/Header.php';
include_once 'include/HTMLWrapper.php';
include_once 'include/Template.php';

// construct a new header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492");
$h->setBackURL('index.php');

/*
 * if (is_student($user))
 */
$h->setPoints(75);

$sheetData = array('sheetID' => 110,
                   'exercises' => array(array('exerciseID' => 1
                                              ),
                                        array('exerciseID' => 1
                                              )
                                        )
                   );

$t = Template::WithTemplateFile('include/Upload/Upload.template.html');
$t->bind($sheetData);

$w = new HTMLWrapper($h, $t);
$w->set_config_file('include/configs/config_upload_exercise.json');
$w->show();
?>
