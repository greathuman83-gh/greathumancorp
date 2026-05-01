<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_main_table';
$uploadDirectory = 'main';

if($del_file1 ??= null){
	@unlink($ghPath."data/$uploadDirectory/$old_file1");
	$inputs['file1'] = '';
}

if($del_file2 ??= null){
	@unlink($ghPath."data/$uploadDirectory/$del_file2");
	$inputs['file2'] = '';
}

if($del_file3 ??= null){
	@unlink($ghPath."data/$uploadDirectory/$del_file3");
	$inputs['file3'] = '';
}

if($_FILES['file1'] ??= null){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file1");
		$mfile = $funcLibrary->uploadFile('file1','',$ghPath."data/$uploadDirectory");
		$inputs['file1'] = $mfile['filename'];
	}
}

if($_FILES['file2'] ??= null){
	$file = $_FILES['file2']['tmp_name'];
	$file_size = $_FILES['file2']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file2");
		$mfile = $funcLibrary->uploadFile('file2','',$ghPath."data/$uploadDirectory");
		$inputs['file2'] = $mfile['filename'];
	}
}

if($_FILES['file3'] ??= null){
	$file = $_FILES['file3']['tmp_name'];
	$file_size = $_FILES['file3']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file3");
		$mfile = $funcLibrary->uploadFile('file3','',$ghPath."data/$uploadDirectory");
		$inputs['file3'] = $mfile['filename'];
	}
}

$inputs['title'] = $title ?? null;
$inputs['content'] = $content ?? null;
$inputs['c_code'] = $c_code ?? null;
$inputs['text1'] = $text1 ?? null;
$inputs['link_url'] = $link_url ?? null;
$inputs['link_target'] = $link_target ?? null;

if($w == 'a'){
	//순서 가져오기
	$where = " where page_type = :pageType ";
	$bindParam[] = array('pageType', $pageType);
	$orderby = "num desc| idx desc";
	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,1,1);

	$inputs['num'] = (int)($listResult['result'][0]['num'] ?? 0)+1;
	$inputs['page_type'] = $pageType;
	$inputs['regdate'] = date('Y-m-d H:i:s');
	
	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./main_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./main_form.php?'.$funcLibrary->queryString());
	}

}else if($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		@unlink($ghPath."data/$uploadDirectory/".$d['file1']);
		@unlink($ghPath."data/$uploadDirectory/".$d['file2']);
		@unlink($ghPath."data/$uploadDirectory/".$d['file3']);

		$funcLibrary->alert('삭제 되었습니다.','./main_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'oe'){//순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./main_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}
include_once($ghPath.'include/common/dbclose.php');
?>