<?php
// 통장거래내역 저장·삭제·엑셀업로드 — b_content JSON, 계산서 대금처리 연동
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$table_name = 'gh_bank_table';
$invoice_table = 'gh_invoice_table';

/**
 * 셀 값 문자열화
 */
function bank_cell_str(mixed $val): string
{
	if ($val === null) {
		return '';
	}
	if ($val instanceof DateTimeInterface) {
		return $val->format('Y-m-d H:i:s');
	}
	if (is_float($val) || is_int($val)) {
		if ((float)$val == (int)$val) {
			return (string)(int)$val;
		}
		return (string)$val;
	}
	return trim((string)$val);
}

/**
 * 금액 — 콤마·원 제거 후 숫자만
 */
function bank_normalize_amount(string $value): string
{
	$digits = preg_replace('/[^\d.-]/', '', $value) ?? '';
	if ($digits === '' || !is_numeric($digits)) {
		return '';
	}
	// 정수 금액 저장
	return (string)(int)round((float)$digits);
}

/**
 * 거래일시 정규화 — YYYY-MM-DD HH:MM:SS (엑셀 시리얼·한 자리 월/일·T구분 포함)
 */
function bank_normalize_datetime(string $value): string
{
	$value = trim($value);
	if ($value === '') {
		return '';
	}

	// Excel 날짜 시리얼 — 업로드 시 PhpSpreadsheet가 로드된 경우에만 변환
	if (is_numeric($value) && (float)$value >= 20000 && (float)$value <= 90000
		&& class_exists(\PhpOffice\PhpSpreadsheet\Shared\Date::class)
	) {
		try {
			return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value)->format('Y-m-d H:i:s');
		} catch (Throwable $e) {
			// 문자열 파싱으로 진행
		}
	}

	$value = str_replace('T', ' ', $value);
	$value = preg_replace('/\s+/', ' ', $value) ?? $value;
	if (preg_match('/^(\d{4})[.\-\/](\d{1,2})[.\-\/](\d{1,2})(?:\s+(\d{1,2}:\d{2}(?::\d{2})?))?/', $value, $m)) {
		$date = sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
		$time = isset($m[4]) && $m[4] !== '' ? $m[4] : '00:00:00';
		$parts = explode(':', $time);
		$h = str_pad((string)(int)($parts[0] ?? 0), 2, '0', STR_PAD_LEFT);
		$i = str_pad((string)(int)($parts[1] ?? 0), 2, '0', STR_PAD_LEFT);
		$s = str_pad((string)(int)($parts[2] ?? 0), 2, '0', STR_PAD_LEFT);
		return $date . ' ' . $h . ':' . $i . ':' . $s;
	}
	$ts = strtotime($value);
	if ($ts === false) {
		return '';
	}
	return date('Y-m-d H:i:s', $ts);
}

/**
 * 보낸분/받는분 정규화 — 엑셀 NBSP·연속공백 제거 후 비교
 */
function bank_normalize_counterparty(string $name): string
{
	$name = preg_replace('/[\s\x{00A0}\x{3000}]+/u', ' ', $name) ?? $name;
	return trim($name);
}

/**
 * b_content 정규화 — 리스트·총잔액·조회기준시만 저장
 * @param array<string, mixed> $raw
 * @return array<string, string>
 */
function bank_normalize_content(array $raw): array
{
	$amount = bank_normalize_amount((string)($raw['amount'] ?? ''));
	$total_balance = bank_normalize_amount((string)($raw['total_balance'] ?? ''));
	$tx_datetime = bank_normalize_datetime((string)($raw['transaction_datetime'] ?? ''));

	return [
		'counterparty' => bank_normalize_counterparty((string)($raw['counterparty'] ?? '')),
		'amount' => $amount,
		'branch' => trim((string)($raw['branch'] ?? '')),
		'transaction_datetime' => $tx_datetime,
		'total_balance' => $total_balance,
		'inquiry_datetime' => trim((string)($raw['inquiry_datetime'] ?? '')),
	];
}

/**
 * 중복 판정 키 — 거래일시+보낸분/받는분+거래금액 (양쪽 동일 정규화)
 */
function bank_duplicate_key(string $transaction_datetime, string $counterparty, string $amount): string
{
	$tx = bank_normalize_datetime($transaction_datetime);
	$party = bank_normalize_counterparty($counterparty);
	$amt = bank_normalize_amount($amount);
	if ($tx === '' || $party === '' || $amt === '') {
		return '';
	}
	return $tx . "\0" . $party . "\0" . $amt;
}

/**
 * 기존 거래내역 중복 키 집합 — JSON 원문 차이를 PHP 정규화로 흡수
 * @return array<string, true>
 */
function bank_existing_duplicate_keys(PDO $conn, string $table_name, int $exclude_idx = 0): array
{
	$sql = 'SELECT idx, b_content FROM `' . $table_name . '`';
	if ($exclude_idx > 0) {
		$sql .= ' WHERE idx <> :exclude_idx';
	}
	$stmt = $conn->prepare($sql);
	if ($exclude_idx > 0) {
		$stmt->bindValue(':exclude_idx', $exclude_idx, PDO::PARAM_INT);
	}
	$stmt->execute();

	$keys = [];
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$content = json_decode((string)($row['b_content'] ?? ''), true);
		if (!is_array($content)) {
			continue;
		}
		$key = bank_duplicate_key(
			(string)($content['transaction_datetime'] ?? ''),
			(string)($content['counterparty'] ?? ''),
			(string)($content['amount'] ?? '')
		);
		if ($key !== '') {
			$keys[$key] = true;
		}
	}
	return $keys;
}

/**
 * 거래일시+보낸분/받는분+거래금액 중복 여부
 */
function bank_is_duplicate(
	PDO $conn,
	string $table_name,
	string $transaction_datetime,
	string $counterparty,
	string $amount,
	int $exclude_idx = 0
): bool {
	$key = bank_duplicate_key($transaction_datetime, $counterparty, $amount);
	if ($key === '') {
		return false;
	}
	$keys = bank_existing_duplicate_keys($conn, $table_name, $exclude_idx);
	return isset($keys[$key]);
}

/**
 * 상호명 비교용 정규화 — (주)·주식회사·공백 제거, 전각→반각 숫자
 */
function bank_normalize_company_name(string $name): string
{
	$name = trim($name);
	if ($name === '') {
		return '';
	}
	// 전각 숫자·영문 → 반각
	if (function_exists('mb_convert_kana')) {
		$name = mb_convert_kana($name, 'as', 'UTF-8');
	}
	$name = str_replace(['주식회사', '(주)', '㈜', '（주）'], '', $name);
	$name = preg_replace('/\s+/u', '', $name) ?? $name;
	return mb_strtolower($name, 'UTF-8');
}

/**
 * 보낸분/받는분 ↔ 계산서 상호 예외 매핑 — 통장표기와 세금계산서 상호가 다른 경우
 * @return array<string, list<string>>
 */
function bank_company_name_aliases(): array
{
	return [
		// 거래내역 세무사김희선·김희선(가온택스) ↔ 계산서 가온택스
		'세무사김희선' => ['가온택스'],
		'김희선(가온택스)' => ['가온택스'],
		// 거래내역 최용석(지에이치소프(상호 잘림) ↔ 계산서 지에이치소프트
		'최용석(지에이치소프' => ['지에이치소프트'],
	];
}

/**
 * 보낸분/받는분 ↔ 상호 매칭 — 완전일치·접두·예외 별칭 허용
 */
function bank_company_name_match(string $bank_name, string $invoice_name): bool
{
	$a = bank_normalize_company_name($bank_name);
	$b = bank_normalize_company_name($invoice_name);
	if ($a === '' || $b === '') {
		return false;
	}
	if ($a === $b) {
		return true;
	}

	// 예외 별칭 — 통장 상대방명과 계산서 상호가 다른 케이스
	foreach (bank_company_name_aliases() as $bank_alias => $invoice_aliases) {
		$alias_bank = bank_normalize_company_name($bank_alias);
		if ($a !== $alias_bank && !str_starts_with($a, $alias_bank) && !str_starts_with($alias_bank, $a)) {
			continue;
		}
		foreach ($invoice_aliases as $invoice_alias) {
			$alias_invoice = bank_normalize_company_name($invoice_alias);
			if ($b === $alias_invoice || str_starts_with($b, $alias_invoice) || str_starts_with($alias_invoice, $b)) {
				return true;
			}
		}
	}

	// KB 엑셀 상호 잘림 대비 — 짧은 쪽이 긴 쪽 접두이면 매칭
	$min_len = 4;
	if (mb_strlen($a, 'UTF-8') >= $min_len && mb_strlen($b, 'UTF-8') >= $min_len) {
		if (str_starts_with($b, $a) || str_starts_with($a, $b)) {
			return true;
		}
	}
	return false;
}

/**
 * 거래내역 ↔ 세금계산서 매칭 후 대금처리상태를 처리완료(3)로 갱신
 * 매출↔입금 / 매입↔출금, 상호·합계금액 일치, 거래일이 발급일~+30일
 * @param array{category: string, counterparty: string, amount: string, transaction_datetime: string} $bank
 */
function bank_sync_invoice_payment(PDO $conn, DBManager $DB, string $invoice_table, array $bank): int
{
	$bank_category = (string)($bank['category'] ?? '');
	$counterparty = (string)($bank['counterparty'] ?? '');
	$amount = (string)($bank['amount'] ?? '');
	$tx_datetime = (string)($bank['transaction_datetime'] ?? '');

	if ($counterparty === '' || $amount === '' || $tx_datetime === '') {
		return 0;
	}
	if ($bank_category !== '1' && $bank_category !== '2') {
		return 0;
	}

	// 출금(1)→매입(2), 입금(2)→매출(1)
	$invoice_category = $bank_category === '1' ? '2' : '1';
	$tx_date = substr($tx_datetime, 0, 10);
	$tx_ts = strtotime($tx_date);
	if ($tx_ts === false) {
		return 0;
	}

	// 미완료 계산서만 — 금액·분류로 후보 축소 후 PHP에서 상호·일자 판정
	$sql = 'SELECT idx, i_content FROM `' . $invoice_table . '`'
		. ' WHERE category = :category'
		. ' AND i_payment_status <> \'3\''
		. ' AND JSON_UNQUOTE(JSON_EXTRACT(i_content, \'$.total_amount\')) = :amount';
	$stmt = $conn->prepare($sql);
	$stmt->bindValue(':category', $invoice_category);
	$stmt->bindValue(':amount', $amount);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$updated = 0;
	foreach ($rows as $row) {
		$content = json_decode((string)($row['i_content'] ?? ''), true);
		if (!is_array($content)) {
			continue;
		}
		$company = (string)($content['company_name'] ?? '');
		if ($company === '') {
			$company = $invoice_category === '1'
				? (string)($content['buyer_name'] ?? '')
				: (string)($content['supplier_name'] ?? '');
		}
		if (!bank_company_name_match($counterparty, $company)) {
			continue;
		}

		$issue_date = str_replace('.', '-', trim((string)($content['issue_date'] ?? '')));
		if ($issue_date === '') {
			continue;
		}
		$issue_ts = strtotime(substr($issue_date, 0, 10));
		if ($issue_ts === false) {
			continue;
		}
		// 발급일 당일 ~ 발급일+30일 이내 거래만 매칭
		$diff_days = (int)floor(($tx_ts - $issue_ts) / 86400);
		if ($diff_days < 0 || $diff_days > 30) {
			continue;
		}

		$idx = (int)($row['idx'] ?? 0);
		if ($idx <= 0) {
			continue;
		}
		$where = [];
		$where[] = ['idx', $idx, 'and'];
		if ($DB->updateSet($invoice_table, ['i_payment_status' => '3'], $where)) {
			$updated++;
		}
	}

	return $updated;
}

if (($w ?? '') == 'eu') {
	if (!$admin_super) {
		$func_library->alert($_pageText['등록하실 권한이 없습니다.']);
	}
	// 엑셀 업로드 — KB 거래내역조회, 목록·총잔액·조회기준시만 저장
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

	$total_balance = '';
	$inquiry_datetime = '';

	// 상단 요약 — 총 잔액, 조회기준일시
	foreach ($rows as $row) {
		foreach ($row as $ci => $cell) {
			$label = preg_replace('/\s+/u', '', bank_cell_str($cell));
			if ($label === '총잔액') {
				// 병합 셀 대비 — 라벨 이후 첫 비어있지 않은 금액
				$next = '';
				for ($k = $ci + 1; $k <= $ci + 4; $k++) {
					$cand = bank_cell_str($row[$k] ?? '');
					if ($cand !== '') {
						$next = $cand;
						break;
					}
				}
				$total_balance = bank_normalize_amount($next);
			}
			if (str_contains($label, '조회기준일시') || str_contains(bank_cell_str($cell), '조 회 기 준 일 시')) {
				$raw_inq = bank_cell_str($cell);
				if (preg_match('/조\s*회\s*기\s*준\s*일\s*시\s*:\s*(.+)$/u', $raw_inq, $m)) {
					$inquiry_datetime = trim($m[1]);
				} else {
					$inquiry_datetime = trim(preg_replace('/^.*조\s*회\s*기\s*준\s*일\s*시\s*:?\s*/u', '', $raw_inq) ?? $raw_inq);
				}
			}
		}
	}

	// 헤더 행 — 거래일시 + 보낸분/받는분
	$header_idx = null;
	$col_map = [];
	foreach ($rows as $ri => $row) {
		$labels = [];
		foreach ($row as $ci => $cell) {
			$labels[$ci] = preg_replace('/\s+/u', '', bank_cell_str($cell));
		}
		$has_tx = in_array('거래일시', $labels, true);
		$has_party = false;
		foreach ($labels as $lb) {
			if ($lb === '보낸분/받는분' || $lb === '보낸분받는분') {
				$has_party = true;
				break;
			}
		}
		if ($has_tx && $has_party) {
			$header_idx = $ri;
			foreach ($labels as $ci => $label) {
				if ($label === '거래일시') {
					$col_map['transaction_datetime'] = $ci;
				} elseif ($label === '보낸분/받는분' || $label === '보낸분받는분') {
					$col_map['counterparty'] = $ci;
				} elseif ($label === '출금액') {
					$col_map['withdraw'] = $ci;
				} elseif ($label === '입금액') {
					$col_map['deposit'] = $ci;
				} elseif ($label === '거래점') {
					$col_map['branch'] = $ci;
				}
			}
			break;
		}
	}

	if (
		$header_idx === null
		|| !isset($col_map['transaction_datetime'], $col_map['counterparty'], $col_map['withdraw'], $col_map['deposit'])
	) {
		$func_library->alert('엑셀 헤더(거래일시, 보낸분/받는분, 출금액, 입금액)를 찾을 수 없습니다.');
	}

	$insert_count = 0;
	$skip_count = 0;
	$invoice_sync_count = 0;
	$now = date('Y-m-d H:i:s');
	$data_rows = array_slice($rows, $header_idx + 1);
	// 기존 DB + 동일 파일 내 중복 — 거래일시·상대방·금액 정규화 키
	$seen_keys = bank_existing_duplicate_keys($conn, $table_name);

	foreach ($data_rows as $row) {
		$tx_raw = bank_cell_str($row[$col_map['transaction_datetime']] ?? '');
		$party = bank_cell_str($row[$col_map['counterparty']] ?? '');
		$withdraw = bank_normalize_amount(bank_cell_str($row[$col_map['withdraw']] ?? ''));
		$deposit = bank_normalize_amount(bank_cell_str($row[$col_map['deposit']] ?? ''));
		$branch = isset($col_map['branch']) ? bank_cell_str($row[$col_map['branch']] ?? '') : '';

		// 페이지 표시·빈 행 스킵 (엑셀 날짜 시리얼은 숫자 범위로 허용)
		$tx_looks_date = preg_match('/^\d{4}/', str_replace(['.', '/'], '-', $tx_raw)) === 1;
		$tx_looks_serial = is_numeric($tx_raw) && (float)$tx_raw >= 20000 && (float)$tx_raw <= 90000;
		if ($tx_raw === '' || (!$tx_looks_date && !$tx_looks_serial)) {
			continue;
		}
		if ($party === '' && $withdraw === '' && $deposit === '') {
			continue;
		}

		$withdraw_num = ($withdraw !== '' && is_numeric($withdraw)) ? (float)$withdraw : 0.0;
		$deposit_num = ($deposit !== '' && is_numeric($deposit)) ? (float)$deposit : 0.0;

		if ($withdraw_num > 0) {
			$category = '1';
			$amount = (string)(int)$withdraw_num;
		} elseif ($deposit_num > 0) {
			$category = '2';
			$amount = (string)(int)$deposit_num;
		} else {
			continue;
		}

		$content = bank_normalize_content([
			'counterparty' => $party,
			'amount' => $amount,
			'branch' => $branch,
			'transaction_datetime' => $tx_raw,
			'total_balance' => $total_balance,
			'inquiry_datetime' => $inquiry_datetime,
		]);

		if ($content['transaction_datetime'] === '' || $content['counterparty'] === '' || $content['amount'] === '') {
			continue;
		}

		$dup_key = bank_duplicate_key(
			$content['transaction_datetime'],
			$content['counterparty'],
			$content['amount']
		);
		if ($dup_key === '' || isset($seen_keys[$dup_key])) {
			if ($dup_key !== '') {
				$skip_count++;
			}
			continue;
		}
		$seen_keys[$dup_key] = true;

		$inputs = [
			'category' => $category,
			'b_content' => json_encode($content, JSON_UNESCAPED_UNICODE),
			'regdate' => $now,
		];
		if (!$DB->insertInto($table_name, $inputs)) {
			$func_library->alert('엑셀 저장 중 문제가 발생하였습니다. (저장 ' . $insert_count . '건)');
		}
		$insert_count++;

		// 세금계산서 대금처리 — 신규 저장건만 연동
		$invoice_sync_count += bank_sync_invoice_payment($conn, $DB, $invoice_table, [
			'category' => $category,
			'counterparty' => $content['counterparty'],
			'amount' => $content['amount'],
			'transaction_datetime' => $content['transaction_datetime'],
		]);
	}

	if ($insert_count === 0 && $skip_count === 0) {
		$func_library->alert('저장할 목록 데이터가 없습니다.');
	}
	if ($insert_count === 0 && $skip_count > 0) {
		$func_library->alert('모두 중복되어 등록되지 않았습니다. (중복 ' . $skip_count . '건)', './bank_list.php?' . $func_library->queryString('idx,w'));
	}

	$msg = $insert_count . '건이 등록되었습니다.';
	if ($skip_count > 0) {
		$msg .= ' (중복 ' . $skip_count . '건 제외)';
	}
	if ($invoice_sync_count > 0) {
		$msg .= ' / 계산서 대금처리완료 ' . $invoice_sync_count . '건';
	}
	$func_library->alert($msg, './bank_list.php?' . $func_library->queryString('idx,w'));
}

// 등록·수정 — 개별 입력값을 b_content JSON으로 저장
if (($w ?? '') == 'a' || ($w ?? '') == 'u') {
	if (!$admin_super) {
		$func_library->alert(($w ?? '') == 'a' ? $_pageText['등록하실 권한이 없습니다.'] : $_pageText['수정하실 권한이 없습니다.']);
	}
	$category = (string)($category ?? '1');
	if ($category !== '1' && $category !== '2') {
		$category = '1';
	}

	$tx_datetime = trim((string)($transaction_datetime ?? ''));

	// 총잔액·조회기준시 — 폼에서 받지 않음, 수정 시 기존 JSON 유지 (엑셀 업로드값)
	$keep_total_balance = '';
	$keep_inquiry_datetime = '';
	if (($w ?? '') == 'u') {
		$exist = $query_library->getData((int)($idx ?? 0), $table_name);
		$exist_content = json_decode((string)($exist['b_content'] ?? ''), true);
		if (is_array($exist_content)) {
			$keep_total_balance = (string)($exist_content['total_balance'] ?? '');
			$keep_inquiry_datetime = (string)($exist_content['inquiry_datetime'] ?? '');
		}
	}

	$content = bank_normalize_content([
		'counterparty' => $counterparty ?? '',
		'amount' => $amount ?? '',
		'branch' => $branch ?? '',
		'transaction_datetime' => $tx_datetime,
		'total_balance' => $keep_total_balance,
		'inquiry_datetime' => $keep_inquiry_datetime,
	]);

	if ($content['counterparty'] === '' || $content['amount'] === '' || $content['transaction_datetime'] === '') {
		$func_library->alert('보낸분/받는분, 거래금액, 거래일시는 필수입니다.');
	}

	$exclude_idx = (($w ?? '') == 'u') ? (int)($idx ?? 0) : 0;
	if (bank_is_duplicate(
		$conn,
		$table_name,
		$content['transaction_datetime'],
		$content['counterparty'],
		$content['amount'],
		$exclude_idx
	)) {
		$func_library->alert('이미 등록된 거래내역입니다. (거래일시·보낸분/받는분·거래금액 일치)');
	}

	$inputs = [
		'category' => $category,
		'b_content' => json_encode($content, JSON_UNESCAPED_UNICODE),
	];

	if (($w ?? '') == 'a') {
		$inputs['regdate'] = date('Y-m-d H:i:s');
		if (!$DB->insertInto($table_name, $inputs)) {
			$func_library->alert('문제가 발생하였습니다.');
		}
		bank_sync_invoice_payment($conn, $DB, $invoice_table, [
			'category' => $category,
			'counterparty' => $content['counterparty'],
			'amount' => $content['amount'],
			'transaction_datetime' => $content['transaction_datetime'],
		]);
		$func_library->alert('등록되었습니다.', './bank_list.php?' . $func_library->queryString('idx,w'));
	}

	$where = [];
	$where[] = ['idx', $idx, 'and'];
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	}
	$func_library->alert('수정되었습니다.', './bank_form.php?' . $func_library->queryString());
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
	$func_library->alert('삭제 되었습니다.', './bank_list.php?' . $func_library->queryString('idx,w'));
}

$func_library->alert('잘못된 접근입니다.');
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
