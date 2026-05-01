<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_inquiry_table';
$uploadDirectory = 'inquiry';

//================= 다중 파일 첨부 시작 =========================
$insertFiles = '';
$insertFilesName = '';

for($i=0;$i<count($_FILES['attachFiles']['name'] ??= array());$i++){
	$attachFilesName=$_FILES['attachFiles']['name'][$i];
	$attachFilesNameSize=$_FILES['attachFiles']['size'][$i];
	if ($attachFilesName == ''){
		if(${'deleteFiles'.$i}){
			@unlink($ghPath."data/$uploadDirectory/".$oldFile[$i]);
			$attachFiles_01[$i] = '';
			$attachFiles_01_name[$i] = '';
		}else{
			if($w == 'u'){
				$attachFiles_01[$i] = $oldFile[$i];
				$attachFiles_01_name[$i] = $oldFileName[$i];
			}else{
				$attachFiles_01[$i] = '';
				$attachFiles_01_name[$i] = '';
			}
		}
	}else{
		if($_FILES['attachFiles']['size'][$i] > 0){
			$mfile = $funcLibrary->uploadMultiFilss('attachFiles','',$ghPath."data/$uploadDirectory",$i);
			@unlink($ghPath."data/$uploadDirectory/".$oldFile[$i]);
			if($mfile['filename']){
				$attachFiles_01[$i] = $mfile['filename'];
				$attachFiles_01_name[$i] = $mfile['o_filename'];
			}
		}
	}

	if($attachFiles_01[$i]){
		$insertFiles .= $attachFiles_01[$i].'|';
		$insertFilesName .= $attachFiles_01_name[$i].'|';
	}

}

$insertFiles = substr($insertFiles,0,-1);
$insertFilesName = substr($insertFilesName,0,-1);

$inputs['attach_files'] = $insertFiles;
$inputs['attach_files_name'] = $insertFilesName;
//================= 다중 파일 첨부 끝 =========================

if($del_file1 ??= ''){
	@unlink($ghPath."data/$uploadDirectory/$del_file1");
	$inputs['file1'] = '';
	$inputs['file1_name'] = '';
}

if($_FILES['file1'] ??= ''){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file1");
		$mfile = $funcLibrary->uploadFile('file1','',$ghPath."data/$uploadDirectory");
		$inputs['file1'] = $mfile['filename'];
		$inputs['file1_name'] = $mfile['o_filename'];
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

$productArray = '';
for($i=0;$i<count($r_product ?? array());$i++){
	$productArray .= $r_product[$i].'|';
}
$productArray = substr($productArray,0,-1);
$inputs['r_product'] = $productArray;

$etcArray = '';
for($i=0;$i<count($r_etc ?? array());$i++){
	$etcArray .= $r_etc[$i].'|';
}
$etcArray = substr($etcArray,0,-1);
$inputs['r_etc'] = $etcArray;

$refererArray = '';
for($i=0;$i<count($r_referer ?? array());$i++){
	$refererArray .= $r_referer[$i].'|';
}
$refererArray = substr($refererArray,0,-1);
$inputs['r_referer'] = $refererArray;
$inputs['r_etc_text'] = $r_etc_text ?? null;


$inputs['title'] = $title ?? null;
$inputs['r_content'] = $r_content ?? null;
$inputs['status'] = $status ?? null;


if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s') ?? null;
	$inputs['pageType'] = $pageType;

	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./inquiry_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./inquiry_form.php?'.$funcLibrary->queryString());
	}
}else if($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);
	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$attachFiles = explode('|',$d['attach_files'] ?? '');
		foreach($attachFiles as $fileName => $fileKey){
			@unlink($ghPath."data/$uploadDirectory/".$fileName);
		}
		@unlink($ghPath."data/$uploadDirectory/".$d['file1']);

		$funcLibrary->alert('삭제되었습니다.','./inquiry_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>