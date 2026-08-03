<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_main_table';
$upload_directory = 'main';

if($del_file1 ??= null){
	@unlink($gh_path."data/$upload_directory/$old_file1");
	$inputs['file1'] = '';
}

if($del_file2 ??= null){
	@unlink($gh_path."data/$upload_directory/$del_file2");
	$inputs['file2'] = '';
}

if($del_file3 ??= null){
	@unlink($gh_path."data/$upload_directory/$del_file3");
	$inputs['file3'] = '';
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

if($_FILES['file2'] ??= null){
	$file = $_FILES['file2']['tmp_name'];
	$file_size = $_FILES['file2']['size'];
	if($file && $file_size>0){
		@unlink($gh_path."data/$upload_directory/$old_file2");
		$mfile = $func_library->uploadFile('file2','',$gh_path."data/$upload_directory");
		$inputs['file2'] = $mfile['filename'];
	}
}

if($_FILES['file3'] ??= null){
	$file = $_FILES['file3']['tmp_name'];
	$file_size = $_FILES['file3']['size'];
	if($file && $file_size>0){
		@unlink($gh_path."data/$upload_directory/$old_file3");
		$mfile = $func_library->uploadFile('file3','',$gh_path."data/$upload_directory");
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
	$bind_param[] = array('pageType', $page_type);
	$orderby = "num desc| idx desc";
	$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,1);

	$inputs['num'] = (int)($list_result['result'][0]['num'] ?? 0)+1;
	$inputs['page_type'] = $page_type;
	$inputs['regdate'] = date('Y-m-d H:i:s');
	
	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./main_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./main_form.php?'.$func_library->queryString());
	}

}else if($w == 'd'){
	$d = $query_library->getData($idx,$table_name);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		@unlink($gh_path."data/$upload_directory/".$d['file1']);
		@unlink($gh_path."data/$upload_directory/".$d['file2']);
		@unlink($gh_path."data/$upload_directory/".$d['file3']);

		$func_library->alert('삭제 되었습니다.','./main_list.php?'.$func_library->queryString('idx,w'));
	}
}else if($w == 'oe'){//순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./main_list.php?'.$func_library->queryString('idx,w'));
	}

}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>