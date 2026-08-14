<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_admin';
$upload_directory = 'manager';

if (isset($del_file1)) {
	@unlink($gh_path . "data/manager/" . $del_file1);
	$inputs['file1'] = '';
}

if (isset($_FILES['file1'])) {
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if ($file && $file_size > 0) {
		$mfile = $func_library->uploadFile('file1', '', $gh_path . 'data/' . $upload_directory);
		$inputs['file1'] = $mfile['filename'];
	}
}

if ($admin_super && isset($a_authority)) { //메인관리자만 페이지 접근 권한 설정 가능
	$a_authority_array = '';
	for ($i = 0; $i < count((array)$a_authority); $i++) {
		$a_authority_array .= $a_authority[$i] . '|';
	}
	$a_authority_array = substr($a_authority_array, 0, -1);
	$inputs['a_authority'] = $a_authority_array;
}

$inputs['a_name'] = $a_name ?? null;
$inputs['a_tel'] = $a_tel ?? null;
$inputs['a_hp'] = $a_hp ?? null;
$inputs['a_email'] = $a_email ?? null;
$inputs['a_data1'] = $a_data1 ?? null;
$inputs['a_data2'] = $a_data2 ?? null;
$inputs['a_data3'] = $a_data3 ?? null;
$inputs['a_data4'] = $a_data4 ?? null;

if (isset($a_level)) {
	$inputs['a_level'] = $a_level;
}

if ($admin_super && isset($a_status) && in_array($a_status, ['Y', 'N'], true)) {
	$inputs['a_status'] = $a_status;
}

if ($a_pwd ??= null) {
	$inputs['a_pwd'] = hash('sha256', $a_pwd);
	// 비밀번호 변경 시 자동로그인 토큰 무효화
	$inputs['a_auto_login_token'] = '';
}

if ($w == 'a') {

	if (!$admin_super) {
		$func_library->alert($_pageText['등록하실 권한이 없습니다.']);
	}

	$inputs['language'] = $_SESSION['language'];
	$inputs['a_id'] = $a_id;
	$inputs['regdate'] = date("Y-m-d H:i:s");
	$inputs['a_status'] = $inputs['a_status'] ?? 'Y';

	$where = "where a_id = :a_id";
	$bind_param[] = array('a_id', $a_id, 'and');
	$total = $query_library->dataTotal($where, $bind_param, $table_name);

	if ($total > 0) {
		$func_library->alert($_pageText['이미 존재하는 아이디 입니다.']);
	}

	if (!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert($_pageText['문제가 발생했습니다.']);
	} else {
		$func_library->alert($_pageText['등록 되었습니다.'], './manager_list.php?' . $func_library->queryString('idx,w'));
	}
} else if ($w == 'u') {

	$d = $query_library->getData($idx, $table_name);
	if (!$admin_super && $admin_id != $d['a_id']) {
		$func_library->alert($_pageText['수정하실 권한이 없습니다.']);
	}

	$where = [];
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert($_pageText['문제가 발생했습니다.']);
	} else {
		$func_library->alert($_pageText['수정 되었습니다.'], './manager_list.php?' . $func_library->queryString('idx,w'));
	}
} else if ($w == 'd') {
	$d = $query_library->getData($idx, $table_name);
	if ($d['super'] == '1') {
		$func_library->alert($_pageText['슈퍼관리자는 삭제하실 수 없습니다.']);
	}

	if (!$admin_super && $admin_id != $d['a_id']) {
		$func_library->alert($_pageText['삭제하실 권한이 없습니다.']);
	}

	$where = [];
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert($_pageText['문제가 발생했습니다.']);
	} else {
		@unlink($gh_path . "data/manager/" . $d['file1']);
		$func_library->alert($_pageText['삭제 되었습니다.'], './manager_list.php?' . $func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
