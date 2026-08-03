<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB=new DBManager($conn);
$table_name = 'gh_popup_table';
$upload_directory = 'popup';

if($w != 'd'){
	if($del_file1 ??= null){
		@unlink($gh_path."data/$upload_directory/".$del_file1);
		$inputs['file1'] = '';
	}

	if($_FILES['file1']){
		$file = $_FILES['file1']['tmp_name'];
		$file_size = $_FILES['file1']['size'];
		if($file && $file_size>0){
			@unlink($gh_path."data/$upload_directory/$old_file1");
			$mfile = $func_library->uploadFile('file1','',$gh_path."data/$upload_directory");
			$inputs['file1'] = $mfile['filename'];
		}
	}

	$max_width = 1100;
	if($_FILES['file1']['tmp_name']){
		$temp = GetImageSize($gh_path."data/$upload_directory/".$mfile['filename']); // 화면에 표시할 그림파일 크기 정보 얻고
	}
	if($_FILES['file1']['tmp_name']){

		if($temp[0] > $max_width){
			$temp[0] = $max_width;
		}

		$inputs['pop_size_w'] = $img_width = $temp[0];
		$inputs['pop_size_h'] = $img_height = $temp[1];

	}else{
		$inputs['pop_size_w'] = $pop_size_w == '' ? null : $pop_size_w;
		$inputs['pop_size_h'] = $pop_size_h == '' ? null : $pop_size_h;

	}

	if($pop_size_d == 1) {
		$inputs['pop_size_w']=300;
		$inputs['pop_size_h']=350;
	}else if($pop_size_d == 2) {
		$inputs['pop_size_w']=350;
		$inputs['pop_size_h']=300;
	}

	if (!isset($pop_location_left)) { $pop_location_left = 10; }
	if (!isset($pop_location_top)) { $pop_location_top = 10; }



	$inputs['pop_subject'] = $pop_subject;
	$inputs['pop_size_d'] = $pop_size_d;
	$inputs['pop_link_url'] = $pop_link_url;
	$inputs['pop_location_left'] = $pop_location_left;
	$inputs['pop_location_top'] = $pop_location_top;
	$inputs['pop_content'] = $content;
	$inputs['pop_view'] = $pop_view;
	$inputs['pop_target'] = $pop_target;
	$inputs['start_date'] = $start_date;
	$inputs['end_date'] = $end_date;
	$inputs['always'] = $always;
}

if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s');
	$inputs['category'] = $page_type;

	if(!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('등록되었습니다.','./popup_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./popup_list.php?'.$func_library->queryString('idx,w'));
	}
}else if ($w == 'd'){
	$d = $query_library->getData($idx,$table_name);
	$where[] = array('idx', $idx);

	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		@unlink($gh_path."data/$upload_directory/".$d['file1']);
		$func_library->alert('삭제 되었습니다.','./popup_list.php?'.$func_library->queryString('idx,w'));
	}
}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>