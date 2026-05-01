<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB=new DBManager($conn);
$tableName = 'gh_popup_table';
$uploadDirectory = 'popup';

if($w != 'd'){
	if($delFile1 ??= null){
		@unlink($ghPath."data/$uploadDirectory/".$delFile1);
		$inputs['file1'] = '';
	}

	if($_FILES['file1']){
		$file = $_FILES['file1']['tmp_name'];
		$file_size = $_FILES['file1']['size'];
		if($file && $file_size>0){
			@unlink($ghPath."data/$uploadDirectory/$oldFile1");
			$mfile = $funcLibrary->uploadFile('file1','',$ghPath."data/$uploadDirectory");
			$inputs['file1'] = $mfile['filename'];
		}
	}

	$maxWidth = 1100;
	if($_FILES['file1']['tmp_name']){
		$temp = GetImageSize($ghPath."data/$uploadDirectory/".$mfile['filename']); // 화면에 표시할 그림파일 크기 정보 얻고
	}
	if($_FILES['file1']['tmp_name']){

		if($temp[0] > $maxWidth){
			$temp[0] = $maxWidth;
		}

		$inputs['pop_size_w'] = $imgWidth = $temp[0];
		$inputs['pop_size_h'] = $imgHeight = $temp[1];

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
	$inputs['category'] = $pageType;

	if(!$DB->insertInto($tableName, $inputs)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./popup_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./popup_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if ($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);
	$where[] = array('idx', $idx);

	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		@unlink($ghPath."data/$uploadDirectory/".$d['file1']);
		$funcLibrary->alert('삭제 되었습니다.','./popup_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath.'include/common/dbclose.php');
?>