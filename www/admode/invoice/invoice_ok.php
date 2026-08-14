<?php
// 세금계산서 저장·삭제·엑셀업로드 — i_content JSON(상대방만), category=page_type
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_invoice_table';
$page_type = (string)($page_type ?? '1');
if ($page_type !== '1' && $page_type !== '2') {
	$page_type = '1';
}

// 공통 엑셀 헤더 → i_content 키 (공백 제거 후 비교)
$invoice_excel_headers_raw = [
	'작성일자' => 'write_date',
	'승인번호' => 'approval_no',
	'발급일자' => 'issue_date',
	'전송일자' => 'transmit_date',
	'합계금액' => 'total_amount',
	'공급가액' => 'supply_amount',
	'세액' => 'tax_amount',
	'품목명' => 'item_name',
];
$invoice_excel_headers = [];
foreach ($invoice_excel_headers_raw as $header_label => $content_key) {
	$invoice_excel_headers[preg_replace('/\s+/u', '', $header_label)] = $content_key;
}

// 금액 컬럼 — 콤마 제거 후 숫자만 저장
$invoice_amount_keys = [
	'total_amount',
	'supply_amount',
	'tax_amount',
];

/**
 * 폼/엑셀 공통 — 공통항목 + 상대방(company_*)만 정규화
 * 매출=공급받는자, 매입=공급자
 * @param array<string, mixed> $raw
 * @return array<string, string>
 */
function invoice_normalize_content(array $raw, array $amount_keys): array
{
	$content = [];
	$keys = [
		'write_date',
		'approval_no',
		'issue_date',
		'transmit_date',
		'company_biz_no',
		'company_sub_no',
		'company_name',
		'company_ceo',
		'company_address',
		'company_email',
		'total_amount',
		'supply_amount',
		'tax_amount',
		'item_name',
	];
	foreach ($keys as $key) {
		$val = trim((string)($raw[$key] ?? ''));
		if (in_array($key, $amount_keys, true)) {
			$val = preg_replace('/[^\d.-]/', '', $val) ?? '';
		}
		$content[$key] = $val;
	}
	return $content;
}

/**
 * 셀 값 문자열화
 */
function invoice_cell_str(mixed $val): string
{
	if ($val === null) {
		return '';
	}
	if (is_float($val) || is_int($val)) {
		// 엑셀 숫자 — 불필요 소수 제거
		if ((float)$val == (int)$val) {
			return (string)(int)$val;
		}
		return (string)$val;
	}
	return trim((string)$val);
}

/**
 * 승인번호+사업자등록번호 중복 여부 — 동일 category 내 i_content JSON 비교
 */
function invoice_is_duplicate(PDO $conn, string $table_name, string $category, string $approval_no, string $biz_no, int $exclude_idx = 0): bool
{
	if ($approval_no === '' || $biz_no === '') {
		return false;
	}

	$sql = 'SELECT idx FROM `' . $table_name . '` WHERE category = :category'
		. ' AND JSON_UNQUOTE(JSON_EXTRACT(i_content, \'$.approval_no\')) = :approval_no'
		. ' AND JSON_UNQUOTE(JSON_EXTRACT(i_content, \'$.company_biz_no\')) = :biz_no';
	if ($exclude_idx > 0) {
		$sql .= ' AND idx <> :exclude_idx';
	}
	$sql .= ' LIMIT 1';

	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':category', $category);
	$stmt->bindValue(':approval_no', $approval_no);
	$stmt->bindValue(':biz_no', $biz_no);
	if ($exclude_idx > 0) {
		$stmt->bindValue(':exclude_idx', $exclude_idx, PDO::PARAM_INT);
	}
	$stmt->execute();

	return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}

if (($w ?? '') == 'eu') {
	if (!$admin_super) {
		$func_library->alert($_pageText['등록하실 권한이 없습니다.']);
	}
	// 엑셀 업로드 — 상단 요약/헤더 제외, 목록 행만 i_content JSON으로 다건 insert
	$file = $_FILES['excel_file'] ?? null;
	if (!is_array($file) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
		$func_library->alert('엑셀 파일을 선택해 주세요.');
	}

	$orig_name = (string)($file['name'] ?? '');
	$ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
	if (!in_array($ext, ['xls', 'xlsx'], true)) {
		$func_library->alert('xls, xlsx 파일만 업로드할 수 있습니다.');
	}

	$tmp_path = (string)($file['tmp_name'] ?? '');
	if ($tmp_path === '' || !is_uploaded_file($tmp_path)) {
		$func_library->alert('업로드 파일이 올바르지 않습니다.');
	}

	require __DIR__ . '/' . $gh_path . 'include/plugin/vendor/autoload.php';

	error_reporting(E_ALL & ~E_DEPRECATED);
	try {
		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmp_path);
	} catch (Throwable $e) {
		$func_library->alert('엑셀 파일을 읽을 수 없습니다.');
	}

	$sheet = $spreadsheet->getActiveSheet();
	$rows = $sheet->toArray(null, true, true, false);
	if ($rows === []) {
		$func_library->alert('엑셀에 데이터가 없습니다.');
	}

	// 헤더 행 탐지 — '작성일자'+'품목명' 포함 행 (상단 기본정보·빈행 스킵)
	$header_idx = null;
	foreach ($rows as $ri => $row) {
		$labels = [];
		foreach ($row as $cell) {
			$labels[] = preg_replace('/\s+/u', '', invoice_cell_str($cell));
		}
		if (in_array('작성일자', $labels, true) && in_array('품목명', $labels, true)) {
			$header_idx = $ri;
			break;
		}
	}
	if ($header_idx === null) {
		$func_library->alert('엑셀 헤더(작성일자, 품목명)를 찾을 수 없습니다.');
	}

	$header_row = $rows[$header_idx];
	$col_map = []; // index => content key
	$seen_mutual = 0;
	$seen_ceo = 0;
	$seen_addr = 0;
	$seen_sub = 0;

	// 매출=공급받는자(2번째), 매입=공급자(1번째)만 company_* 로 매핑
	$target_side = $page_type === '1' ? 1 : 0;

	foreach ($header_row as $ci => $label_raw) {
		$label = preg_replace('/\s+/u', '', invoice_cell_str($label_raw));
		if ($label === '') {
			continue;
		}

		if (isset($invoice_excel_headers[$label])) {
			$col_map[$ci] = $invoice_excel_headers[$label];
			continue;
		}

		// 공급자(0)·공급받는자(1) 구간 — page_type에 맞는 쪽만 저장
		if ($label === '공급자사업자등록번호') {
			if ($target_side === 0) {
				$col_map[$ci] = 'company_biz_no';
			}
			continue;
		}
		if ($label === '공급받는자사업자등록번호') {
			if ($target_side === 1) {
				$col_map[$ci] = 'company_biz_no';
			}
			continue;
		}
		if ($label === '종사업장번호') {
			if ($seen_sub === $target_side) {
				$col_map[$ci] = 'company_sub_no';
			}
			$seen_sub++;
			continue;
		}
		if ($label === '상호') {
			if ($seen_mutual === $target_side) {
				$col_map[$ci] = 'company_name';
			}
			$seen_mutual++;
			continue;
		}
		if ($label === '대표자명') {
			if ($seen_ceo === $target_side) {
				$col_map[$ci] = 'company_ceo';
			}
			$seen_ceo++;
			continue;
		}
		if ($label === '주소') {
			if ($seen_addr === $target_side) {
				$col_map[$ci] = 'company_address';
			}
			$seen_addr++;
			continue;
		}
		if ($label === '공급자이메일') {
			if ($target_side === 0) {
				$col_map[$ci] = 'company_email';
			}
			continue;
		}
		if ($label === '공급받는자이메일1') {
			if ($target_side === 1) {
				$col_map[$ci] = 'company_email';
			}
			continue;
		}
	}

	$insert_count = 0;
	$skip_count = 0;
	$now = date('Y-m-d H:i:s');
	$data_rows = array_slice($rows, $header_idx + 1);
	// 동일 엑셀 내 중복 키 추적
	$seen_keys = [];

	foreach ($data_rows as $row) {
		$raw = [];
		foreach ($col_map as $ci => $key) {
			$raw[$key] = invoice_cell_str($row[$ci] ?? '');
		}
		$content = invoice_normalize_content($raw, $invoice_amount_keys);

		// 빈 행 스킵 — 승인번호·품목명 모두 없으면 무시
		if ($content['approval_no'] === '' && $content['item_name'] === '') {
			continue;
		}

		// 승인번호+사업자등록번호 중복 — DB·동일 파일 내 스킵
		$dup_key = $content['approval_no'] . "\0" . $content['company_biz_no'];
		if ($content['approval_no'] !== '' && $content['company_biz_no'] !== '') {
			if (isset($seen_keys[$dup_key]) || invoice_is_duplicate($conn, $table_name, $page_type, $content['approval_no'], $content['company_biz_no'])) {
				$skip_count++;
				$seen_keys[$dup_key] = true;
				continue;
			}
			$seen_keys[$dup_key] = true;
		}

		$inputs = [
			'category' => $page_type,
			'i_content' => json_encode($content, JSON_UNESCAPED_UNICODE),
			'i_payment_status' => '1',
			'i_part_payment' => '',
			'regdate' => $now,
		];
		if (!$DB->insertInto($table_name, $inputs)) {
			$func_library->alert('엑셀 저장 중 문제가 발생하였습니다. (저장 ' . $insert_count . '건)');
		}
		$insert_count++;
	}

	if ($insert_count === 0 && $skip_count === 0) {
		$func_library->alert('저장할 목록 데이터가 없습니다.');
	}
	if ($insert_count === 0 && $skip_count > 0) {
		$func_library->alert('모두 중복되어 등록되지 않았습니다. (중복 ' . $skip_count . '건)', './invoice_list.php?' . $func_library->queryString('idx,w'));
	}

	$msg = $insert_count . '건이 등록되었습니다.';
	if ($skip_count > 0) {
		$msg .= ' (중복 ' . $skip_count . '건 제외)';
	}
	$func_library->alert($msg, './invoice_list.php?' . $func_library->queryString('idx,w'));
}

// 등록·수정 — 개별 입력값을 i_content JSON으로 저장
if (($w ?? '') == 'a' || ($w ?? '') == 'u') {
	if (!$admin_super) {
		$func_library->alert(($w ?? '') == 'a' ? $_pageText['등록하실 권한이 없습니다.'] : $_pageText['수정하실 권한이 없습니다.']);
	}
	$raw = [
		'write_date' => $write_date ?? '',
		'approval_no' => $approval_no ?? '',
		'issue_date' => $issue_date ?? '',
		'transmit_date' => $transmit_date ?? '',
		'company_biz_no' => $company_biz_no ?? '',
		'company_sub_no' => $company_sub_no ?? '',
		'company_name' => $company_name ?? '',
		'company_ceo' => $company_ceo ?? '',
		'company_address' => $company_address ?? '',
		'company_email' => $company_email ?? '',
		'total_amount' => $total_amount ?? '',
		'supply_amount' => $supply_amount ?? '',
		'tax_amount' => $tax_amount ?? '',
		'item_name' => $item_name ?? '',
	];
	$content = invoice_normalize_content($raw, $invoice_amount_keys);

	// 대금 수금/지급 상태 — 테이블 컬럼 (1~3만 허용)
	$i_payment_status = (string)($i_payment_status ?? '1');
	if (!in_array($i_payment_status, ['1', '2', '3'], true)) {
		$i_payment_status = '1';
	}
	$i_part_payment = preg_replace('/[^\d.-]/', '', (string)($i_part_payment ?? '')) ?? '';

	$inputs = [
		'i_content' => json_encode($content, JSON_UNESCAPED_UNICODE),
		'i_payment_status' => $i_payment_status,
		'i_part_payment' => $i_part_payment,
	];

	// 승인번호+사업자등록번호 중복 검사 (수정 시 본인 제외)
	$exclude_idx = (($w ?? '') == 'u') ? (int)($idx ?? 0) : 0;
	if (invoice_is_duplicate($conn, $table_name, $page_type, $content['approval_no'], $content['company_biz_no'], $exclude_idx)) {
		$func_library->alert('이미 등록된 세금계산서입니다. (승인번호·사업자등록번호 일치)');
	}

	if (($w ?? '') == 'a') {
		$inputs['regdate'] = date('Y-m-d H:i:s');
		$inputs['category'] = $page_type;

		if (!$DB->insertInto($table_name, $inputs)) {
			$func_library->alert('문제가 발생하였습니다.');
		}
		$func_library->alert('등록되었습니다.', './invoice_list.php?' . $func_library->queryString('idx,w'));
	}

	$where = [];
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	}
	$func_library->alert('수정되었습니다.', './invoice_form.php?' . $func_library->queryString());
}

if (($w ?? '') == 'd') {
	if (!$admin_super) {
		$func_library->alert($_pageText['삭제하실 권한이 없습니다.']);
	}
	$idx = (int)($idx ?? 0);
	$where = [];
	$where[] = ['idx', $idx];
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	}
	$func_library->alert('삭제 되었습니다.', './invoice_list.php?' . $func_library->queryString('idx,w'));
}

$func_library->alert('잘못된 접근입니다.');
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
