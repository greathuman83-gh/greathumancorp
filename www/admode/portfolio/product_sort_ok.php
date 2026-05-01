<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);
$tableName = 'gh_product_table';

if($w == 'u') {//게시글 수정
	foreach($idx as $key=>$val){
		$where = array();
		$inputs = array();
		$inputs['num'] = $key;
		$where[] = array('idx', $val,'and');
		if(!$DB->updateSet($tableName, $inputs, $where)){
			$funcLibrary->alert('문제가 발생하였습니다.');
		}
	}
	$funcLibrary->alert('수정되었습니다.','./product_sort.php?'.$funcLibrary->queryString('idx,w'));
}

include_once($ghPath.'include/common/dbclose.php');
?>