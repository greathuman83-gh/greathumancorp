<?php
// 협력사 저장·삭제 — 첨부·담당자 JSON, data/partner 업로드
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_partner_table';
$upload_directory = 'partner';
$max_attach = 5;
$max_manager = 5;

// 담당자정보 — 최대 5명, 전부 빈 행은 제외 후 JSON
$manager_names = $manager_name ?? [];
$manager_emails = $manager_email ?? [];
$manager_phones = $manager_phone ?? [];
$manager_roles = $manager_role ?? [];
if (!is_array($manager_names)) {
	$manager_names = [];
}

$manager_list = [];
$count_manager = min(count($manager_names), $max_manager);
for ($i = 0; $i < $count_manager; $i++) {
	$row = [
		'name' => trim((string)($manager_names[$i] ?? '')),
		'email' => trim((string)($manager_emails[$i] ?? '')),
		'phone' => trim((string)($manager_phones[$i] ?? '')),
		'role' => trim((string)($manager_roles[$i] ?? '')),
	];
	if ($row['name'] === '' && $row['email'] === '' && $row['phone'] === '' && $row['role'] === '') {
		continue;
	}
	$manager_list[] = $row;
}
$inputs['p_manager'] = $manager_list !== [] ? json_encode($manager_list, JSON_UNESCAPED_UNICODE) : '';

// 다중 첨부 — 최대 5개, 시스템명·원본명 각각 JSON 배열
$attach_files_01 = [];
$attach_files_01_name = [];
$old_file = $old_file ?? [];
$old_file_name = $old_file_name ?? [];
if (!is_array($old_file)) {
	$old_file = [];
}
if (!is_array($old_file_name)) {
	$old_file_name = [];
}

$file_names = $_FILES['attach_files']['name'] ?? [];
if (!is_array($file_names)) {
	$file_names = [];
}
$file_count = min(count($file_names), $max_attach);

for ($i = 0; $i < $file_count; $i++) {
	$attach_name = (string)($file_names[$i] ?? '');
	$attach_size = (int)($_FILES['attach_files']['size'][$i] ?? 0);

	if ($attach_name === '') {
		if (${'delete_files' . $i} ??= null) {
			$del_base = $func_library->safeBoardUploadBasename((string)($old_file[$i] ??= ''));
			if ($del_base !== '') {
				@unlink($gh_path . "data/$upload_directory/" . $del_base);
			}
			continue;
		}
		if (($w ?? '') == 'u') {
			$keep = $func_library->safeBoardUploadBasename((string)($old_file[$i] ??= ''));
			if ($keep !== '') {
				$attach_files_01[] = $keep;
				$attach_files_01_name[] = (string)($old_file_name[$i] ??= $keep);
			}
		}
		continue;
	}

	if ($attach_size > 0) {
		$mfile = $func_library->uploadMultiFiles('attach_files', '', $gh_path . "data/$upload_directory", $i);
		$prev = $func_library->safeBoardUploadBasename((string)($old_file[$i] ??= ''));
		if ($prev !== '') {
			@unlink($gh_path . "data/$upload_directory/" . $prev);
		}
		if (!empty($mfile['filename'])) {
			$attach_files_01[] = $mfile['filename'];
			$attach_files_01_name[] = $mfile['original_file_name'];
		}
	}
}

$inputs['attach_files'] = $attach_files_01 !== [] ? json_encode($attach_files_01, JSON_UNESCAPED_UNICODE) : '';
$inputs['attach_files_name'] = $attach_files_01_name !== [] ? json_encode($attach_files_01_name, JSON_UNESCAPED_UNICODE) : '';

// 사업자등록번호 — 숫자 10자리만 추출 후 3-2-5 하이픈 형식 저장
$p_number_digits = preg_replace('/\D/', '', (string)($p_number ?? ''));
$p_number_digits = substr($p_number_digits, 0, 10);
if ($p_number_digits === '') {
	$inputs['p_number'] = '';
} else if (strlen($p_number_digits) === 10) {
	$inputs['p_number'] = substr($p_number_digits, 0, 3) . '-' . substr($p_number_digits, 3, 2) . '-' . substr($p_number_digits, 5, 5);
} else {
	// 미완성 입력도 입력단과 동일 패턴으로 저장
	if (strlen($p_number_digits) <= 3) {
		$inputs['p_number'] = $p_number_digits;
	} else if (strlen($p_number_digits) <= 5) {
		$inputs['p_number'] = substr($p_number_digits, 0, 3) . '-' . substr($p_number_digits, 3);
	} else {
		$inputs['p_number'] = substr($p_number_digits, 0, 3) . '-' . substr($p_number_digits, 3, 2) . '-' . substr($p_number_digits, 5);
	}
}
$inputs['p_name'] = $p_name ?? null;
$inputs['p_ceo_name'] = $p_ceo_name ?? null;

if (($w ?? '') == 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if (!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('등록되었습니다.', './partner_list.php?' . $func_library->queryString('idx,w'));
	}
} else if (($w ?? '') == 'u') {
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('수정되었습니다.', './partner_form.php?' . $func_library->queryString());
	}
} else if (($w ?? '') == 'd') {
	$d = $query_library->getData($idx, $table_name);

	$where[] = ['idx', $idx];
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		// 삭제 시 첨부 실파일 제거 — attach_files JSON 배열
		$files = json_decode((string)($d['attach_files'] ?? '[]'), true);
		if (is_array($files)) {
			foreach ($files as $file_key) {
				$file_base = $func_library->safeBoardUploadBasename((string)$file_key);
				if ($file_base !== '') {
					@unlink($gh_path . "data/$upload_directory/" . $file_base);
				}
			}
		}

		$func_library->alert('삭제 되었습니다.', './partner_list.php?' . $func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
