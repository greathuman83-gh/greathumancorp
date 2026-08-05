<?php
// 협력사 검색 API — 사업건 폼 레이어팝업용 JSON (회사명·첫 담당자)
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';

header('Content-Type: application/json; charset=utf-8');

$table_name = 'gh_partner_table';
$keyword = trim((string)($keyword ?? ''));
$bind_param = [];
$where = ' where 1=1 ';

// 검색 — 회사명·사업자번호·대표자 (바인딩 키 분리, PDO 재사용 이슈 방지)
if ($keyword !== '') {
	$where .= ' and (p_name like :kw_name or p_number like :kw_number or p_ceo_name like :kw_ceo)';
	$bind_param[] = ['kw_name', $keyword, 'like'];
	$bind_param[] = ['kw_number', $keyword, 'like'];
	$bind_param[] = ['kw_ceo', $keyword, 'like'];
}

$orderby = 'idx desc';
$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, 1, 50);

$list = [];
foreach ($list_result['result'] as $row) {
	$managers = json_decode((string)($row['p_manager'] ?? '[]'), true);
	if (!is_array($managers)) {
		$managers = [];
	}
	$first = $managers[0] ?? [];
	if (!is_array($first)) {
		$first = [];
	}
	$list[] = [
		'idx' => (int)($row['idx'] ?? 0),
		'p_name' => (string)($row['p_name'] ?? ''),
		'p_number' => (string)($row['p_number'] ?? ''),
		'p_ceo_name' => (string)($row['p_ceo_name'] ?? ''),
		'manager' => [
			'name' => (string)($first['name'] ?? ''),
			'email' => (string)($first['email'] ?? ''),
			'phone' => (string)($first['phone'] ?? ''),
		],
	];
}

echo json_encode(['ok' => true, 'list' => $list], JSON_UNESCAPED_UNICODE);
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
