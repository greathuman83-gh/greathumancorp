<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_techpr_table';
$upload_directory = 'techpr';

if($del_file1 ??= null){
	@unlink($gh_path."data/$upload_directory/$old_file1");
	$inputs['file1'] = '';
}


if($_FILES['file1'] ??= null){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($gh_path."data/$upload_directory/$old_file1");
		$mfile = $func_library->uploadFile('file1','',$gh_path."data/$upload_directory");
		$inputs['file1'] = $mfile['filename'];
	}
}


$inputs['title'] = $title ?? null;

if($w == 'a'){
	$inputs['category'] = $page_type;
	$inputs['regdate'] = date('Y-m-d H:i:s');
	
	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./techpr_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./techpr_form.php?'.$func_library->queryString());
	}

}else if($w == 'd'){
	$d = $query_library->getData($idx,$table_name);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		@unlink($gh_path."data/$upload_directory/".$d['file1']);

		$func_library->alert('삭제 되었습니다.','./techpr_list.php?'.$func_library->queryString('idx,w'));
	}
}else if($w == 'oe'){//순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./techpr_list.php?'.$func_library->queryString('idx,w'));
	}

}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>