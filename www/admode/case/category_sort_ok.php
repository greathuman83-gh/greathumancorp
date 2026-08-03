<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);
$table_name = 'gh_category_table';

for ($i = 0; $i < count((array)$sort_num); $i++) {
	$inputs = array();
	$where = array();
	$inputs['num'] = $i;
	$where[] = array('idx', $sort_num[$i], 'and');
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	}
}

if (count($sort_num2 ??= array()) > 1) {
	for ($i = 0; $i < count((array)$sort_num2); $i++) {
		$inputs = array();
		$where = array();
		$inputs['num'] = $i;
		$where[] = array('idx', $sort_num2[$i], 'and');
		if (!$DB->updateSet($table_name, $inputs, $where)) {
			$func_library->alert('문제가 발생하였습니다.');
		}
	}
}

if (count($sort_num3 ??= array()) > 1) {
	for ($i = 0; $i < count((array)$sort_num3); $i++) {
		$inputs = array();
		$where = array();
		$inputs['num'] = $i;
		$where[] = array('idx', $sort_num3[$i], 'and');
		if (!$DB->updateSet($table_name, $inputs, $where)) {
			$func_library->alert('문제가 발생하였습니다.');
		}
	}
}



$func_library->alert('수정되었습니다.', "category_sort.php?menu_code=$menu_code&cate=$cate");

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
