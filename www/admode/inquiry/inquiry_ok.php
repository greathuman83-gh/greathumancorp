<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_inquiry_table';
$upload_directory = 'inquiry';

//================= 다중 파일 첨부 시작 =========================
$insert_files = '';
$insert_files_name = '';

for($i=0;$i<count($_FILES['attach_files']['name'] ??= array());$i++){
	$attach_files_name=$_FILES['attach_files']['name'][$i];
	$attach_files_name_size=$_FILES['attach_files']['size'][$i];
	if ($attach_files_name == ''){
		if(${'delete_files'.$i}){
			@unlink($gh_path."data/$upload_directory/".$old_file[$i]);
			$attach_files_01[$i] = '';
			$attach_files_01_name[$i] = '';
		}else{
			if($w == 'u'){
				$attach_files_01[$i] = $old_file[$i];
				$attach_files_01_name[$i] = $old_file_name[$i];
			}else{
				$attach_files_01[$i] = '';
				$attach_files_01_name[$i] = '';
			}
		}
	}else{
		if($_FILES['attach_files']['size'][$i] > 0){
			$mfile = $func_library->uploadMultiFiles('attach_files','',$gh_path."data/$upload_directory",$i);
			@unlink($gh_path."data/$upload_directory/".$old_file[$i]);
			if($mfile['filename']){
				$attach_files_01[$i] = $mfile['filename'];
				$attach_files_01_name[$i] = $mfile['original_file_name'];
			}
		}
	}

	if($attach_files_01[$i]){
		$insert_files .= $attach_files_01[$i].'|';
		$insert_files_name .= $attach_files_01_name[$i].'|';
	}

}

$insert_files = substr($insert_files,0,-1);
$insert_files_name = substr($insert_files_name,0,-1);

$inputs['attach_files'] = $insert_files;
$inputs['attach_files_name'] = $insert_files_name;
//================= 다중 파일 첨부 끝 =========================

if($del_file1 ??= ''){
	@unlink($gh_path."data/$upload_directory/$del_file1");
	$inputs['file1'] = '';
	$inputs['file1_name'] = '';
}

if($_FILES['file1'] ??= ''){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($gh_path."data/$upload_directory/$old_file1");
		$mfile = $func_library->uploadFile('file1','',$gh_path."data/$upload_directory");
		$inputs['file1'] = $mfile['filename'];
		$inputs['file1_name'] = $mfile['original_file_name'];
	}
}

/*
for($i=0;$i<count((array)$form_value);$i++){
	$form_value_array .= $form_value[$i].'|';
}
$form_value_array = substr($form_value_array,0,-1);
*/

$inputs['category'] = $category ?? null;
$inputs['r_name'] = $r_name ?? null;
$inputs['r_company'] = $r_company ?? null;
$inputs['r_tel'] = $r_tel ?? null;
$inputs['r_email'] = $r_email ?? null;

$product_array = '';
for($i=0;$i<count($r_product ?? array());$i++){
	$product_array .= $r_product[$i].'|';
}
$product_array = substr($product_array,0,-1);
$inputs['r_product'] = $product_array;

$etc_array = '';
for($i=0;$i<count($r_etc ?? array());$i++){
	$etc_array .= $r_etc[$i].'|';
}
$etc_array = substr($etc_array,0,-1);
$inputs['r_etc'] = $etc_array;

$referer_array = '';
for($i=0;$i<count($r_referer ?? array());$i++){
	$referer_array .= $r_referer[$i].'|';
}
$referer_array = substr($referer_array,0,-1);
$inputs['r_referer'] = $referer_array;
$inputs['r_etc_text'] = $r_etc_text ?? null;


$inputs['title'] = $title ?? null;
$inputs['r_content'] = $r_content ?? null;
$inputs['status'] = $status ?? null;


if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s') ?? null;
	$inputs['pageType'] = $page_type;

	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./inquiry_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name, $inputs, $where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./inquiry_form.php?'.$func_library->queryString());
	}
}else if($w == 'd'){
	$d = $query_library->getData($idx,$table_name);
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$attach_files = explode('|',$d['attach_files'] ?? '');
		foreach($attach_files as $file_name => $file_key){
			@unlink($gh_path."data/$upload_directory/".$file_name);
		}
		@unlink($gh_path."data/$upload_directory/".$d['file1']);

		$func_library->alert('삭제되었습니다.','./inquiry_list.php?'.$func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>