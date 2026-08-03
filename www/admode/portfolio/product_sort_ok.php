<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);
$table_name = 'gh_product_table';

if($w == 'u') {//게시글 수정
	foreach($idx as $key=>$val){
		$where = array();
		$inputs = array();
		$inputs['num'] = $key;
		$where[] = array('idx', $val,'and');
		if(!$DB->updateSet($table_name, $inputs, $where)){
			$func_library->alert('문제가 발생하였습니다.');
		}
	}
	$func_library->alert('수정되었습니다.','./product_sort.php?'.$func_library->queryString('idx,w'));
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>