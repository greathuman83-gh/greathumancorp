<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
session_destroy();
$func_library->gotoUrl(ADMIN_DIR.'/index.php');
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>