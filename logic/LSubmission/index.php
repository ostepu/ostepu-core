<?php
require_once ( dirname( __FILE__ ) . '/LSubmission.php' );
include_once ( '../../Assistants/CConfig.php' );

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LSubmission::getPrefix());

/**
 * make a new instance of Submission-Class with the Config-Datas
 */
if (!$com->used())
    new LSubmission($com->loadConfig());
?>