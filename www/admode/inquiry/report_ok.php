<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_report_table';
$upload_directory = 'report';


$inputs['r_name'] = $r_name ?? null;
$inputs['r_mobile'] = $r_mobile ?? null;
$inputs['r_email'] = $r_email ?? null;
$inputs['r_type'] = $r_type ?? null;
$inputs['title'] = $title ?? null;
$inputs['content'] = $content ?? null;
$inputs['r_reply'] = $r_reply ?? null;
$inputs['status'] = $status ?? null;


if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s') ?? null;

	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./report_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name, $inputs, $where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./report_form.php?'.$func_library->queryString());
	}
}else if($w == 'd'){
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('삭제되었습니다.','./report_list.php?'.$func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>