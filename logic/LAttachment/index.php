<?php
require_once ( dirname( __FILE__ ) . '/LAttachment.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new config data from DB
$com = new CConfig(LAttachment::getPrefix());

// create a new instance of LAttachment class with the config data
if (!$com->used())
    new LAttachment($com->loadConfig());
?>