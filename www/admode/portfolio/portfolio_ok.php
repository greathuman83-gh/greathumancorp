<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';


$DB = new DBManager($conn);

$table_name = 'gh_portfolio_table';
$feature_table = 'gh_portfolio_feature_table';
$upload_directory = 'portfolio';

if($delete_thumb_file ??= null){
	@unlink($gh_path."data/$upload_directory/$old_thumb_file");
	$inputs['thumb_file'] = '';
}

if($_FILES['thumb_file'] ??= null){
	$file = $_FILES['thumb_file']['tmp_name'];
	$file_size = $_FILES['thumb_file']['size'];
	if($file && $file_size>0){
		@unlink($gh_path."data/$upload_directory/$old_thumb_file");
		$mfile = $func_library->uploadFile('thumb_file','',$gh_path."data/$upload_directory");
		$inputs['thumb_file'] = $mfile['filename'];
		//$resize_data = image_resize($gh_path.'data/board/$bbsid/',$mfile['filename'],397,235,$mfile['img_type'],1,95);
		//$inputs['thumb_file'] = $resize_data['fileName'];
	}
}

//================= 다중 파일 첨부 시작 =========================
$insert_files = '';
$insert_files_name = '';

for($i=0;$i<count($_FILES['attach_files']['name']);$i++){
	$attach_files_name=$_FILES['attach_files']['name'][$i];
	$attach_files_name_size=$_FILES['attach_files']['size'][$i];
	if ($attach_files_name == ''){
		if(${'delete_files'.$i} ??= null){
			@unlink($gh_path."data/$upload_directory/".$old_file[$i] ??= '');
			$attach_files_01[$i] = '';
			$attach_files_01_name[$i] = '';
		}else{
			if($w == 'u'){
				$attach_files_01[$i] = $old_file[$i] ??= '';
				$attach_files_01_name[$i] = $old_file_name[$i] ??= '';
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
//$inputs['attach_files_name'] = $insert_files_name;
//================= 다중 파일 첨부 끝 =========================



//가능한 검사 옵션
if(count($spec ??= array()) > 0){
	$spec_array = array();
	for($i=0;$i<count($spec);$i++){
		$spec_array['spec'][$i]['specPart'] = $spec_part[$i];
		$spec_array['spec'][$i]['specContent'] = $spec_content[$i];
	}
	$spec_data = json_encode($spec_array,JSON_UNESCAPED_UNICODE);
}

$content2Array = '';
for($i=0;$i<count($content2 ??= array());$i++){
	$content2Array .= $content2[$i].'|';
}
$content2Array = substr($content2Array,0,-1);



$inputs['p_spec'] = $spec_data ?? null;
$inputs['p_open'] = $p_open ?? null;
$inputs['c_code'] = $c_code ?? null;
$inputs['title'] = $title ?? null;
$inputs['title2'] = $title2 ?? null;
$inputs['content'] = $content ?? null;
$inputs['content2'] = $content2Array ?? null;
$inputs['regdate'] = $regdate ?? date('Y-m-d H:i:s');

if($w == 'a'){

	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./portfolio_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./portfolio_form.php?'.$func_library->queryString());
	}

}else if($w == 'd'){
	$d = get_data($idx,$table_name);

	$where[] = array('idx', $idx);
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$attach_files = explode('|',$d['attach_files']);
		foreach($attach_files as $file_name => $file_key){
			@unlink($gh_path."data/$upload_directory/".$file_name);
		}

		@unlink($gh_path."data/$upload_directory/".$d['thumb_file']);
		
		$func_library->alert('삭제 되었습니다.','./portfolio_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'oe'){//순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name,$inputs,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./portfolio_list.php?'.$func_library->queryString('idx,w'));
	}
}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>