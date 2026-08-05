<?php
// 세금계산서 목록 — gh_invoice_table, page_type(1:매출 2:매입), i_content JSON 표시
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_invoice_table';
$page_type = (string)($page_type ?? '1');
if ($page_type !== '1' && $page_type !== '2') {
	$page_type = '1';
}
$excel_label = $page_type === '1' ? '매출 엑셀 업로드' : '매입 엑셀 업로드';
$key_type = (string)($key_type ?? 'item_name');
$current_year = (int)date('Y');
$search_year = (string)($search_year ?? '');
if ($search_year !== '' && (!ctype_digit($search_year) || (int)$search_year < 2024 || (int)$search_year > $current_year)) {
	$search_year = '';
}
// 대금처리상태 검색 — 1:미처리 2:부분처리 3:처리완료
$search_payment_status = (string)($search_payment_status ?? '');
if (!in_array($search_payment_status, ['1', '2', '3'], true)) {
	$search_payment_status = '';
}
$payment_status_labels = [
	'1' => '미처리',
	'2' => '부분처리',
	'3' => '처리완료',
];
$payment_status_colors = [
	'1' => 'blue',
	'2' => 'orange',
	'3' => 'red',
];
// 합계금액 → 통장거래내역 거래금액 검색 링크용 menu_code
$bank_menu_row = $query_library->getData2(
	' where m_link like :mLink and language = :language ',
	[['mLink', 'bank_list.php', 'like'], ['language', LANGUAGE]],
	'gh_admin_menu_table'
);
$bank_menu_code = is_array($bank_menu_row) ? (string)($bank_menu_row['m_code'] ?? '') : '';
?>

<table width="100%" class="adminMenuTable">
	<tr>
		<td align="left">
			<form name="fexcel" method="post" action="./invoice_ok.php?<?= $func_library->queryString('w') ?>w=eu" enctype="multipart/form-data" style="margin:0;display:inline-block;">
				<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
				<input type="hidden" name="page_type" value="<?= gh_h($page_type) ?>">
				<input type="file" name="excel_file" class="input_text" accept=".xls,.xlsx" required>
				<button type="submit" class="black_btn"><?= gh_h($excel_label) ?></button>
			</form>
		</td>
		<td align="right">
			<form name="fsearch" method="get" style="margin:0;display:inline-block;">
				<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
				<input type="hidden" name="page_type" value="<?= gh_h($page_type) ?>">
				<select name="search_year" class="input_select">
					<option value="">연도 전체</option>
					<?php for ($y = $current_year; $y >= 2024; $y--) { ?>
						<option value="<?= $y ?>" <?php if ($search_year === (string)$y) { ?>selected<?php } ?>><?= $y ?></option>
					<?php } ?>
				</select>
				<select name="search_payment_status" class="input_select">
					<option value="">대금처리상태 전체</option>
					<option value="1" <?php if ($search_payment_status === '1') { ?>selected<?php } ?>>미처리</option>
					<option value="2" <?php if ($search_payment_status === '2') { ?>selected<?php } ?>>부분처리</option>
					<option value="3" <?php if ($search_payment_status === '3') { ?>selected<?php } ?>>처리완료</option>
				</select>
				<select name="key_type" class="input_select">
					<option value="company" <?php if ($key_type === 'company') { ?>selected<?php } ?>>회사명</option>
					<option value="item_name" <?php if ($key_type === 'item_name') { ?>selected<?php } ?>>품목명</option>
				</select>
				<input type="text" name="keyword" value="<?= gh_h((string)($keyword ?? '')) ?>" class="input_text">
				<button type="submit" class="search_btn">검색</button>
			</form>
		</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col width="70" align="center">
	</col>
	<col>
	</col>
	<col width="160" align="center">
	</col>
	<col width="120" align="center">
	</col>
	<col width="120" align="center">
	</col>
	<col width="110" align="center">
	</col>
	<col width="110" align="center">
	</col>
	<col width="110" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<tr>
		<td colspan="11" class="line1"></td>
	</tr>
	<tr class="bgcol1 bold col1 ht center">
		<td>번호</td>
		<td>품목명</td>
		<td>회사명</td>
		<td>공급가액</td>
		<td>합계금액</td>
		<td>대금처리상태</td>
		<td>발급일</td>
		<td>등록일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./invoice_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = [];
	$where = ' where category = :pageType ';
	$bind_param[] = ['pageType', $page_type];

	// JSON(i_content) 검색 — 컬럼 분리 없이 키워드 LIKE
	$keyword_val = trim((string)($keyword ?? ''));
	if ($keyword_val !== '') {
		$where .= ' and i_content like :keyword';
		$bind_param[] = ['keyword', $keyword_val, 'like'];
	}

	// 발급일 연도 필터
	if ($search_year !== '') {
		$where .= " and LEFT(JSON_UNQUOTE(JSON_EXTRACT(i_content, '$.issue_date')), 4) = :searchYear";
		$bind_param[] = ['searchYear', $search_year];
	}

	// 대금처리상태 필터
	if ($search_payment_status !== '') {
		$where .= ' and i_payment_status = :searchPaymentStatus';
		$bind_param[] = ['searchPaymentStatus', $search_payment_status];
	}

	// 발급일 내림차순 — i_content.issue_date (JSON), 동일 시 idx desc
	$list_count = 10;
	$pg = max(1, (int)($pg ?? 1));
	$list_total = (int)$query_library->dataTotal($where, $bind_param, $table_name);
	$total_page = $list_count > 0 ? (int)ceil($list_total / $list_count) : 0;
	$list_start = ($pg - 1) * $list_count;
	$number = $list_total - $list_start;

	$sql = 'SELECT * FROM `' . $table_name . '` ' . $where
		. " ORDER BY JSON_UNQUOTE(JSON_EXTRACT(i_content, '$.issue_date')) DESC, idx DESC"
		. ' LIMIT :listStart, :listCount';
	$stmt = $conn->prepare($sql);
	foreach ($bind_param as $param) {
		$key = ':' . $param[0];
		if (isset($param[2]) && $param[2] === 'like') {
			$stmt->bindValue($key, '%' . $param[1] . '%');
		} else {
			$stmt->bindValue($key, $param[1]);
		}
	}
	$stmt->bindValue(':listStart', $list_start, PDO::PARAM_INT);
	$stmt->bindValue(':listCount', $list_count, PDO::PARAM_INT);
	$stmt->execute();
	$list_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$list_result = ['total_page' => $total_page, 'list_total' => $list_total];

	foreach ($list_rows as $d) {
		$regdate = substr((string)($d['regdate'] ?? ''), 0, 10);
		$content = json_decode((string)($d['i_content'] ?? ''), true);
		if (!is_array($content)) {
			$content = [];
		}
		$item_name = (string)($content['item_name'] ?? '');
		// 상대방 상호 — company_name, 구키(buyer_/supplier_) 호환
		$company = (string)($content['company_name'] ?? '');
		if ($company === '') {
			$company = $page_type === '1'
				? (string)($content['buyer_name'] ?? '')
				: (string)($content['supplier_name'] ?? '');
		}
		// 공급가액 · 합계금액(VAT 포함)
		$supply_num = preg_replace('/[^\d.-]/', '', (string)($content['supply_amount'] ?? ''));
		$supply_text = ($supply_num !== '' && is_numeric($supply_num))
			? '₩' . number_format((float)$supply_num)
			: '';
		$total_num = preg_replace('/[^\d.-]/', '', (string)($content['total_amount'] ?? ''));
		$total_text = ($total_num !== '' && is_numeric($total_num))
			? '₩' . number_format((float)$total_num)
			: '';
		// 합계금액 — bank_list 거래금액(key_type=amount) 검색 링크
		$total_html = gh_h($total_text);
		if ($total_text !== '' && $total_num !== '') {
			$bank_qs = 'key_type=amount&keyword=' . rawurlencode($total_num);
			if ($bank_menu_code !== '') {
				$bank_qs = 'menu_code=' . rawurlencode($bank_menu_code) . '&' . $bank_qs;
			}
			$total_html = '<a href="./bank_list.php?' . $bank_qs . '">' . gh_h($total_text) . '</a>';
		}
		$issue_date = (string)($content['issue_date'] ?? '');
		// 대금처리상태 — 미처리(파랑) / 부분처리(오렌지) / 처리완료(빨강)
		$payment_status = (string)($d['i_payment_status'] ?? '1');
		if (!isset($payment_status_labels[$payment_status])) {
			$payment_status = '1';
		}
		$payment_status_html = '<span style="color:' . $payment_status_colors[$payment_status] . '">'
			. gh_h($payment_status_labels[$payment_status]) . '</span>';
	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td class="td2"><a href="./invoice_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= (int)$d['idx'] ?>"><?= gh_h($item_name) ?></a></td>
			<td><?= gh_h($company) ?></td>
			<td><?= gh_h($supply_text) ?></td>
			<td><?= $total_html ?></td>
			<td><?= $payment_status_html ?></td>
			<td><?= gh_h($issue_date) ?></td>
			<td><?= gh_h($regdate) ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./invoice_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= (int)$d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./invoice_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= (int)$d['idx'] ?>';">삭제</button>
			</td>
		</tr>
		<tr>
			<td colspan="11" class="line2"></td>
		</tr>
	<?php
		$number--;
	}
	?>
</table>
<br>
<table width="95%" align="center">
	<tr>
		<td align="center">
			<?= $func_library->getPaging($_config['page_list_ea'], $pg, $list_result['total_page'], $_SERVER['PHP_SELF'] . '?' . $func_library->queryString('pg') . 'pg=') ?>
		</td>
	</tr>
</table>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>