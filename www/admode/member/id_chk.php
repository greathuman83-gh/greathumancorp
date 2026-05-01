<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');

if(!isset($a_id)){
	$result = 'nodata';
}

$where = "where a_id = :a_id";
$bindParam[] = array('a_id', $a_id);
$total = $queryLibrary->dataTotal($where,$bindParam,'gh_admin');

if($total > 0){
	$result = 'N';
}else{
	$result = 'Y';
}

$_json_array = array(
	'result'		=> $result,
);
echo urldecode(json_encode($_json_array));
include_once($ghPath.'include/common/dbclose.php');
?>