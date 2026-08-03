<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';

if(!isset($a_id)){
	$result = 'nodata';
}

$where = "where a_id = :a_id";
$bind_param[] = array('a_id', $a_id);
$total = $query_library->dataTotal($where,$bind_param,'gh_admin');

if($total > 0){
	$result = 'N';
}else{
	$result = 'Y';
}

$_json_array = array(
	'result'		=> $result,
);
echo urldecode(json_encode($_json_array));
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>