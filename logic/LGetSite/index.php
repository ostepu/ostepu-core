<?php
require_once ( dirname( __FILE__ ) . '/LGetSite.php' );
include_once ( '../../Assistants/CConfig.php' );

// get new componenent configuartion from the database
$com = new CConfig(LGetSite::getPrefix());

// start the component with the newly received configuration
if (!$com->used())
    new LGetSite($com->loadConfig());
?>