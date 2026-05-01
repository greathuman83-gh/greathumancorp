<?php
$ghPath = '../../';
include_once($ghPath . 'include/common/common.php');
include_once($ghPath . 'include/common/permission.php');
include_once($ghPath . 'include/common/db.class.php');


$DB = new DBManager($conn);

$tableName = 'gh_case_table';
$featureTable = 'gh_case_feature_table';
$uploadDirectory = 'case';

if ($delete_thumb_file ??= null) {
	@unlink($ghPath . "data/$uploadDirectory/$old_thumb_file");
	$inputs['thumb_file'] = '';
}

if ($_FILES['thumb_file'] ??= null) {
	$file = $_FILES['thumb_file']['tmp_name'];
	$file_size = $_FILES['thumb_file']['size'];
	if ($file && $file_size > 0) {
		@unlink($ghPath . "data/$uploadDirectory/$old_thumb_file");
		$mfile = $funcLibrary->uploadFile('thumb_file', '', $ghPath . "data/$uploadDirectory");
		$inputs['thumb_file'] = $mfile['filename'];
		//$resizeData = image_resize($ghPath.'data/board/$bbsid/',$mfile['filename'],397,235,$mfile['img_type'],1,95);
		//$inputs['thumb_file'] = $resizeData['fileName'];
	}
}

//================= 다중 파일 첨부 시작 =========================
$insertFiles = '';
$insertFilesName = '';

for ($i = 0; $i < count($_FILES['attachFiles']['name'] ??= array()); $i++) {
	$attachFilesName = $_FILES['attachFiles']['name'][$i];
	$attachFilesNameSize = $_FILES['attachFiles']['size'][$i];
	if ($attachFilesName == '') {
		if (${'deleteFiles' . $i} ??= null) {
			@unlink($ghPath . "data/$uploadDirectory/" . $oldFile[$i] ??= '');
			$attachFiles_01[$i] = '';
			$attachFiles_01_name[$i] = '';
		} else {
			if ($w == 'u') {
				$attachFiles_01[$i] = $oldFile[$i] ??= '';
				$attachFiles_01_name[$i] = $oldFileName[$i] ??= '';
			} else {
				$attachFiles_01[$i] = '';
				$attachFiles_01_name[$i] = '';
			}
		}
	} else {
		if ($_FILES['attachFiles']['size'][$i] > 0) {
			$mfile = $funcLibrary->uploadMultiFiles('attachFiles', '', $ghPath . "data/$uploadDirectory", $i);
			@unlink($ghPath . "data/$uploadDirectory/" . $oldFile[$i]);
			if ($mfile['filename']) {
				$attachFiles_01[$i] = $mfile['filename'];
				$attachFiles_01_name[$i] = $mfile['originalFileName'];
			}
		}
	}

	if ($attachFiles_01[$i]) {
		$insertFiles .= $attachFiles_01[$i] . '|';
		$insertFilesName .= $attachFiles_01_name[$i] . '|';
	}
}

$insertFiles = substr($insertFiles, 0, -1);
$insertFilesName = substr($insertFilesName, 0, -1);

$inputs['attach_files'] = $insertFiles;
//$inputs['attach_files_name'] = $insertFilesName;
//================= 다중 파일 첨부 끝 =========================



$inputs['c_open'] = $c_open ?? null;
$inputs['c_main'] = $c_main ?? null;
$inputs['c_code'] = $c_code ?? null;
$inputs['title'] = $title ?? null;
$inputs['c_company'] = $c_company ?? null;
$inputs['c_address'] = $c_address ?? null;
$inputs['c_address_detail'] = $c_address_detail ?? null;
$inputs['c_text1'] = $c_text1 ?? null;
$inputs['c_text2'] = $c_text2 ?? null;
$inputs['c_text3'] = $c_text3 ?? null;
$inputs['c_text4'] = $c_text4 ?? null;
$inputs['c_text5'] = $c_text5 ?? null;
$inputs['c_text6'] = $c_text6 ?? null;
$inputs['c_lat'] = $c_lat ?? null;
$inputs['c_lng'] = $c_lng ?? null;


if ($w == 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if (!$DB->insertInto($tableName, $inputs)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('등록되었습니다.', './case_list.php?' . $funcLibrary->queryString('idx,w'));
	}
} else if ($w == 'u') {

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('수정되었습니다.', './case_form.php?' . $funcLibrary->queryString());
	}
} else if ($w == 'd') {
	$d = $queryLibrary->getData($idx, $tableName);

	$where[] = array('idx', $idx);
	if (!$DB->delete_db($tableName, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$attachFiles = explode('|', $d['attach_files']);
		foreach ($attachFiles as $fileName => $fileKey) {
			@unlink($ghPath . "data/$uploadDirectory/" . $fileName);
		}

		@unlink($ghPath . "data/$uploadDirectory/" . $d['thumb_file']);

		$funcLibrary->alert('삭제 되었습니다.', './case_list.php?' . $funcLibrary->queryString('idx,w'));
	}
} else if ($w == 'oe') { //순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('수정되었습니다.', './case_list.php?' . $funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath . 'include/common/dbclose.php');
