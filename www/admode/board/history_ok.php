<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_history_table';
$uploadDirectory = 'history';

$inputs['year'] = $year ?? null;

//====== 연혁 ==========
if(count($history ??= array()) > 0){
	$historyArray = array();
	$oldHistoryImage = $old_historyImage ?? array();
	for($i=0;$i<count($history);$i++){
		$historyArray['history'][$i]['historyTitle'] = $historyTitle[$i] ?? '';
		$historyArray['history'][$i]['historyContent'] = $historyContent[$i] ?? '';
		// 항목별 이미지: 새 파일 업로드 시 저장, 없으면 기존 파일명 유지
		$itemImage = '';
		if(!empty($_FILES['historyImage']['tmp_name'][$i]) && $_FILES['historyImage']['size'][$i] > 0){
			$mfile = $funcLibrary->uploadMultiFiles('historyImage','',$ghPath.'data/'.$uploadDirectory,$i);
			if(!empty($mfile['filename'])){
				$itemImage = $mfile['filename'];
				$oldFile = $oldHistoryImage[$i] ?? '';
				if($oldFile) @unlink($ghPath.'data/'.$uploadDirectory.'/'.$oldFile);
			}
		}else{
			$itemImage = $oldHistoryImage[$i] ?? '';
		}
		$historyArray['history'][$i]['historyImage'] = $itemImage;
	}
	$historyData = json_encode($historyArray,JSON_UNESCAPED_UNICODE);
}
$inputs['content'] = $historyData ?? null;

if($del_file1 ??= null){
	@unlink($ghPath."data/$uploadDirectory/$old_file1");
	$inputs['file1'] = '';
}

if($_FILES['file1'] ??= null){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file1");
		$mfile = $funcLibrary->uploadFile('file1','',$ghPath."data/$uploadDirectory");
		$inputs['file1'] = $mfile['filename'];
		//$resizeData = image_resize($ghPath.'data/board/$bbsid/',$mfile['filename'],397,235,$mfile['img_type'],1,95);
		//$inputs['file1'] = $resizeData['fileName'];
	}
}

if($w == 'a'){
	$inputs['regdate'] = date('Y-m-d H:i:s');
	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('등록되었습니다.','./history_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./history_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		@unlink("../../data/$uploadDirectory/".$d['file1']);
		$funcLibrary->alert('삭제 되었습니다.','./history_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath.'include/common/dbclose.php');
?>