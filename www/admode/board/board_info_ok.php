<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);
$tableName = 'gh_board';

$inputs['b_name'] = $b_name;
$inputs['bbsid'] = $boardID;
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

	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./board_info_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./board_info_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'd'){
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제 되었습니다.','./board_info_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath.'include/common/dbclose.php');
?>