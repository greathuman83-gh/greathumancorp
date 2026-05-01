<?php
$ghPath = '../';
include_once($ghPath.'include/common/common.php');

if($bbsid && $idx){
	$queryLibrary->boardCountUp($idx,$bbsid);
	$result = 'Y';
}else{
	$result = 'N';
}

$_jsonArray = array(
	'result'		=> $result,
);
echo urldecode(json_encode($_jsonArray));
include_once($ghPath.'include/common/dbclose.php');
?>