<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
session_destroy();
$funcLibrary->gotoUrl(ADMIN_DIR.'/index.php');
include_once($ghPath.'include/common/dbclose.php');
?>