<?php
require_once ( dirname( __FILE__ ) . '/LExtension.php' );
include_once ( '../../Assistants/CConfig.php' );

/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LExtension::getPrefix());

/**
 * run a new instance of LExtension with the Config-Datas
 */
if (!$com->used())
    new LExtension($com->loadConfig());
?>