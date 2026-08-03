<?php
$gh_path = '../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';

if($bbsid && $idx){
	$query_library->boardCountUp($idx,$bbsid);
	$result = 'Y';
}else{
	$result = 'N';
}

$_jsonArray = array(
	'result'		=> $result,
);
echo urldecode(json_encode($_jsonArray));
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>