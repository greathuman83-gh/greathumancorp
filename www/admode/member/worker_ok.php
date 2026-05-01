<?php
$ghPath = '../../';
include_once($ghPath . 'include/common/common.php');
include_once($ghPath . 'include/common/permission.php');
include_once($ghPath . 'include/common/db.class.php');


$DB = new DBManager($conn);

$tableName = 'gh_worker_table';
$uploadDirectory = 'worker';



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
$inputs['attach_files_name'] = $insertFilesName;
//================= 다중 파일 첨부 끝 =========================


$inputs['w_name'] = $w_name ?? null;
$inputs['w_type'] = $w_type ?? null;
$inputs['w_enterdate'] = $w_enterdate ?? null;
$inputs['w_leavedate'] = $w_leavedate ?? null;
$inputs['content'] = $content ?? null;
$inputs['w_bankname'] = $w_bankname ?? null;
$inputs['w_banknumber'] = $w_banknumber ?? null;


if ($w == 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if (!$DB->insertInto($tableName, $inputs)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('등록되었습니다.', './worker_list.php?' . $funcLibrary->queryString('idx,w'));
	}
} else if ($w == 'u') {

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('수정되었습니다.', './worker_form.php?' . $funcLibrary->queryString());
	}
} else if ($w == 'd') {
	$d = $queryLibrary->getData($idx, $tableName);

	$where[] = array('idx', $idx);
	if (!$DB->delete_db($tableName, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$attachFiles = explode('|', $d['attach_files'] ?? '');
		foreach ($attachFiles as $fileName => $fileKey) {
			@unlink($ghPath . "data/$uploadDirectory/" . $fileName);
		}

		$funcLibrary->alert('삭제 되었습니다.', './worker_list.php?' . $funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath . 'include/common/dbclose.php');
