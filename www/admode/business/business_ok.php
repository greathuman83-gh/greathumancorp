<?php
// 사업건 저장·삭제 — 매출처/매입처·첨부 JSON, data/business 업로드
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_business_table';
$upload_directory = 'business';
$max_attach = 3;

// 금액 — 숫자만 저장
$sales_total = preg_replace('/\D/', '', (string)($sales_total_price ?? '')) ?? '';
$purchasing_total = preg_replace('/\D/', '', (string)($purchasing_total_price ?? '')) ?? '';

// 사업기간 — YYYY-MM만 허용
$start_date = trim((string)($b_start_date ?? ''));
$end_date = trim((string)($b_end_date ?? ''));
if (preg_match('/^(\d{4}-\d{2})/', $start_date, $m)) {
	$start_date = $m[1];
} else {
	$start_date = '';
}
if (preg_match('/^(\d{4}-\d{2})/', $end_date, $m)) {
	$end_date = $m[1];
} else {
	$end_date = '';
}

// 매출처/매입처 정보 — 회사명·금액·담당자·협력사idx JSON
$sales_info = [
	'partner_idx' => (int)($sales_partner_idx ?? 0),
	'company' => trim((string)($sales_company ?? '')),
	'total_price' => $sales_total,
	'manager' => [
		'name' => trim((string)($sales_manager_name ?? '')),
		'email' => trim((string)($sales_manager_email ?? '')),
		'phone' => trim((string)($sales_manager_phone ?? '')),
	],
];
$purchasing_info = [
	'partner_idx' => (int)($purchasing_partner_idx ?? 0),
	'company' => trim((string)($purchasing_company ?? '')),
	'total_price' => $purchasing_total,
	'manager' => [
		'name' => trim((string)($purchasing_manager_name ?? '')),
		'email' => trim((string)($purchasing_manager_email ?? '')),
		'phone' => trim((string)($purchasing_manager_phone ?? '')),
	],
];

$inputs['b_name'] = $b_name ?? null;
$inputs['b_start_date'] = $start_date;
$inputs['b_end_date'] = $end_date;
$inputs['b_sales_info'] = json_encode($sales_info, JSON_UNESCAPED_UNICODE);
$inputs['b_purchasing_info'] = json_encode($purchasing_info, JSON_UNESCAPED_UNICODE);
$inputs['b_sales_price'] = preg_replace('/\D/', '', (string)($b_sales_price ?? '')) ?? '';
$inputs['b_purchasing_price'] = preg_replace('/\D/', '', (string)($b_purchasing_price ?? '')) ?? '';

// 총마진 — 총매출-총매입 (서버 재계산)
$inputs['b_total_margin'] = (string)((float)$sales_total - (float)$purchasing_total);

if (($w ?? '') == 'a' || ($w ?? '') == 'u') {
	// 매출용 첨부 — 최대 3개
	$sales_files = [];
	$sales_names = [];
	$old_sales_file = $old_sales_file ?? [];
	$old_sales_file_name = $old_sales_file_name ?? [];
	if (!is_array($old_sales_file)) {
		$old_sales_file = [];
	}
	if (!is_array($old_sales_file_name)) {
		$old_sales_file_name = [];
	}
	$sales_file_names = $_FILES['sales_attach_files']['name'] ?? [];
	if (!is_array($sales_file_names)) {
		$sales_file_names = [];
	}
	$sales_count = min(max(count($sales_file_names), count($old_sales_file)), $max_attach);
	for ($i = 0; $i < $sales_count; $i++) {
		$attach_name = (string)($sales_file_names[$i] ?? '');
		$attach_size = (int)($_FILES['sales_attach_files']['size'][$i] ?? 0);

		if ($attach_name === '') {
			if (${'delete_sales_files' . $i} ??= null) {
				$del_base = $func_library->safeBoardUploadBasename((string)($old_sales_file[$i] ??= ''));
				if ($del_base !== '') {
					@unlink($gh_path . "data/$upload_directory/" . $del_base);
				}
				continue;
			}
			if (($w ?? '') == 'u') {
				$keep = $func_library->safeBoardUploadBasename((string)($old_sales_file[$i] ??= ''));
				if ($keep !== '') {
					$sales_files[] = $keep;
					$sales_names[] = (string)($old_sales_file_name[$i] ??= $keep);
				}
			}
			continue;
		}

		if ($attach_size > 0) {
			$mfile = $func_library->uploadMultiFiles('sales_attach_files', '', $gh_path . "data/$upload_directory", $i);
			$prev = $func_library->safeBoardUploadBasename((string)($old_sales_file[$i] ??= ''));
			if ($prev !== '') {
				@unlink($gh_path . "data/$upload_directory/" . $prev);
			}
			if (!empty($mfile['filename'])) {
				$sales_files[] = $mfile['filename'];
				$sales_names[] = $mfile['original_file_name'];
			}
		}
	}

	// 매입용 첨부 — 최대 3개
	$purchasing_files = [];
	$purchasing_names = [];
	$old_purchasing_file = $old_purchasing_file ?? [];
	$old_purchasing_file_name = $old_purchasing_file_name ?? [];
	if (!is_array($old_purchasing_file)) {
		$old_purchasing_file = [];
	}
	if (!is_array($old_purchasing_file_name)) {
		$old_purchasing_file_name = [];
	}
	$purchasing_file_names = $_FILES['purchasing_attach_files']['name'] ?? [];
	if (!is_array($purchasing_file_names)) {
		$purchasing_file_names = [];
	}
	$purchasing_count = min(max(count($purchasing_file_names), count($old_purchasing_file)), $max_attach);
	for ($i = 0; $i < $purchasing_count; $i++) {
		$attach_name = (string)($purchasing_file_names[$i] ?? '');
		$attach_size = (int)($_FILES['purchasing_attach_files']['size'][$i] ?? 0);

		if ($attach_name === '') {
			if (${'delete_purchasing_files' . $i} ??= null) {
				$del_base = $func_library->safeBoardUploadBasename((string)($old_purchasing_file[$i] ??= ''));
				if ($del_base !== '') {
					@unlink($gh_path . "data/$upload_directory/" . $del_base);
				}
				continue;
			}
			if (($w ?? '') == 'u') {
				$keep = $func_library->safeBoardUploadBasename((string)($old_purchasing_file[$i] ??= ''));
				if ($keep !== '') {
					$purchasing_files[] = $keep;
					$purchasing_names[] = (string)($old_purchasing_file_name[$i] ??= $keep);
				}
			}
			continue;
		}

		if ($attach_size > 0) {
			$mfile = $func_library->uploadMultiFiles('purchasing_attach_files', '', $gh_path . "data/$upload_directory", $i);
			$prev = $func_library->safeBoardUploadBasename((string)($old_purchasing_file[$i] ??= ''));
			if ($prev !== '') {
				@unlink($gh_path . "data/$upload_directory/" . $prev);
			}
			if (!empty($mfile['filename'])) {
				$purchasing_files[] = $mfile['filename'];
				$purchasing_names[] = $mfile['original_file_name'];
			}
		}
	}

	$attach_payload = [
		'sales' => $sales_files,
		'purchasing' => $purchasing_files,
	];
	$attach_name_payload = [
		'sales' => $sales_names,
		'purchasing' => $purchasing_names,
	];
	$inputs['attach_files'] = ($sales_files !== [] || $purchasing_files !== [])
		? json_encode($attach_payload, JSON_UNESCAPED_UNICODE)
		: '';
	$inputs['attach_files_name'] = ($sales_names !== [] || $purchasing_names !== [])
		? json_encode($attach_name_payload, JSON_UNESCAPED_UNICODE)
		: '';
}

if (($w ?? '') == 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if (!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('등록되었습니다.', './business_list.php?' . $func_library->queryString('idx,w'));
	}
} else if (($w ?? '') == 'u') {
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('수정되었습니다.', './business_form.php?' . $func_library->queryString());
	}
} else if (($w ?? '') == 'd') {
	$d = $query_library->getData($idx, $table_name);

	$where[] = ['idx', $idx];
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		// 삭제 시 첨부 실파일 제거 — sales/purchasing JSON
		$files = json_decode((string)($d['attach_files'] ?? ''), true);
		if (is_array($files)) {
			foreach (['sales', 'purchasing'] as $group) {
				$group_files = is_array($files[$group] ?? null) ? $files[$group] : [];
				foreach ($group_files as $file_key) {
					$file_base = $func_library->safeBoardUploadBasename((string)$file_key);
					if ($file_base !== '') {
						@unlink($gh_path . "data/$upload_directory/" . $file_base);
					}
				}
			}
		}

		$func_library->alert('삭제 되었습니다.', './business_list.php?' . $func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
