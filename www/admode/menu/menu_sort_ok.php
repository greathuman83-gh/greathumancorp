<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB =new DBManager($conn);
$tableName = 'gh_admin_menu_table';

for($i=0;$i<count((array)$sort_num);$i++){
	$inputs = array();
	$where = array();
	$inputs['num'] = $i;
	$where[] = array('idx', $sort_num[$i],'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}
}

if(isset($sort_num2)){
	if(count((array)$sort_num2) > 1){
		for($i=0;$i<count((array)$sort_num2);$i++){
			$inputs = array();
			$where = array();
			$inputs['num'] = $i;
			$where[] = array('idx', $sort_num2[$i],'and');
			if(!$DB->updateSet($tableName, $inputs, $where)) {
				$funcLibrary->alert('문제가 발생하였습니다.');
			}
		}
	}
}

if(isset($sort_num3)){
	if(count((array)$sort_num3) > 1){
		for($i=0;$i<count((array)$sort_num3);$i++){
			$inputs = array();
			$where = array();
			$inputs['num'] = $i;
			$where[] = array('idx', $sort_num3[$i],'and');
			if(!$DB->updateSet($tableName, $inputs, $where)){
				$funcLibrary->alert('문제가 발생하였습니다.');
			}
		}
	}
}


$funcLibrary->alert('수정되었습니다.','./menu_sort.php?'.$funcLibrary->queryString('idx,w'));

include_once($ghPath.'include/common/dbclose.php');
?>