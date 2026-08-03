<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';


$DB = new DBManager($conn);

$table_name = 'gh_worker_table';
$upload_directory = 'worker';



//================= 다중 파일 첨부 시작 =========================
$insert_files = '';
$insert_files_name = '';

for ($i = 0; $i < count($_FILES['attach_files']['name'] ??= array()); $i++) {
	$attach_files_name = $_FILES['attach_files']['name'][$i];
	$attach_files_name_size = $_FILES['attach_files']['size'][$i];
	if ($attach_files_name == '') {
		if (${'delete_files' . $i} ??= null) {
			@unlink($gh_path . "data/$upload_directory/" . $old_file[$i] ??= '');
			$attach_files_01[$i] = '';
			$attach_files_01_name[$i] = '';
		} else {
			if ($w == 'u') {
				$attach_files_01[$i] = $old_file[$i] ??= '';
				$attach_files_01_name[$i] = $old_file_name[$i] ??= '';
			} else {
				$attach_files_01[$i] = '';
				$attach_files_01_name[$i] = '';
			}
		}
	} else {
		if ($_FILES['attach_files']['size'][$i] > 0) {
			$mfile = $func_library->uploadMultiFiles('attach_files', '', $gh_path . "data/$upload_directory", $i);
			@unlink($gh_path . "data/$upload_directory/" . $old_file[$i]);
			if ($mfile['filename']) {
				$attach_files_01[$i] = $mfile['filename'];
				$attach_files_01_name[$i] = $mfile['original_file_name'];
			}
		}
	}

	if ($attach_files_01[$i]) {
		$insert_files .= $attach_files_01[$i] . '|';
		$insert_files_name .= $attach_files_01_name[$i] . '|';
	}
}

$insert_files = substr($insert_files, 0, -1);
$insert_files_name = substr($insert_files_name, 0, -1);

$inputs['attach_files'] = $insert_files;
$inputs['attach_files_name'] = $insert_files_name;
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

	if (!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('등록되었습니다.', './worker_list.php?' . $func_library->queryString('idx,w'));
	}
} else if ($w == 'u') {

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('수정되었습니다.', './worker_form.php?' . $func_library->queryString());
	}
} else if ($w == 'd') {
	$d = $query_library->getData($idx, $table_name);

	$where[] = array('idx', $idx);
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$attach_files = explode('|', $d['attach_files'] ?? '');
		foreach ($attach_files as $file_name => $file_key) {
			@unlink($gh_path . "data/$upload_directory/" . $file_name);
		}

		$func_library->alert('삭제 되었습니다.', './worker_list.php?' . $func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
