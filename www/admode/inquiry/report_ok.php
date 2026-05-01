<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_report_table';
$uploadDirectory = 'report';


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

	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./report_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./report_form.php?'.$funcLibrary->queryString());
	}
}else if($w == 'd'){
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제되었습니다.','./report_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>