<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);
$table_name = 'gh_board';

$inputs['b_name'] = $b_name;
$inputs['bbsid'] = $board_id;
$inputs['b_skin'] = $b_skin;
$inputs['b_cate'] = $b_cate;
$inputs['b_link'] = $b_link;
$inputs['b_comment'] = $b_comment;
$inputs['b_level'] = $b_level;
$inputs['b_write'] = $b_write;
$inputs['b_read'] = $b_read;
$inputs['b_secret'] = $b_secret;
$inputs['b_notice'] = $b_notice;
$inputs['b_file'] = $b_file;
$inputs['b_file_text'] = $b_file_text;
$inputs['b_type'] = $b_type;
$inputs['b_content_type'] = $b_content_type;
$inputs['b_thumb_text'] = $b_thumb_text;
$inputs['b_reply'] = $b_reply;

if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./board_info_list.php?'.$func_library->queryString('idx,w'));
	}
}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./board_info_list.php?'.$func_library->queryString('idx,w'));
	}
}else if($w == 'd'){
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('삭제 되었습니다.','./board_info_list.php?'.$func_library->queryString('idx,w'));
	}
}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>