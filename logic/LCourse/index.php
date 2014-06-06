<?php
require_once ( dirname( __FILE__ ) . '/LCourse.php' );
include_once ( '../../Assistants/CConfig.php' );

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LCourse::getPrefix());

/**
 * run a new instance of LCourse with the Config-Datas
 */
if (!$com->used())
    new LCourse($com->loadConfig());
?>