<?php
require_once ( dirname( __FILE__ ) . '/LTutor.php' );
include_once ( '../../Assistants/CConfig.php' );

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LTutor::getPrefix());

/**
 * make a new instance of Tutor-Class with the Config-Datas
 */
if (!$com->used())
    new LTutor($com->loadConfig());
?>