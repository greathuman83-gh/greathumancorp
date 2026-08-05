<?php
// 통장거래내역 목록 — gh_bank_table, b_content JSON, 엑셀업로드·기간/형태/상대방 검색
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_bank_table';
$search_category = (string)($search_category ?? '');
if (!in_array($search_category, ['1', '2'], true)) {
	$search_category = '';
}
$start_date = trim((string)($start_date ?? ''));
$end_date = trim((string)($end_date ?? ''));
// 브라우저 date input — YYYY-MM-DD만 허용
if ($start_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
	$start_date = '';
}
if ($end_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
	$end_date = '';
}
$keyword = trim((string)($keyword ?? ''));
// 키워드 검색 대상 — counterparty|amount
$key_type = (string)($key_type ?? 'counterparty');
if (!in_array($key_type, ['counterparty', 'amount'], true)) {
	$key_type = 'counterparty';
}

// 현재총잔액·조회기준시 — 최근 등록건 b_content
$summary_balance = '';
$summary_inquiry = '';
$summary_stmt = $conn->query(
	'SELECT b_content FROM `' . $table_name . '` ORDER BY regdate DESC, idx DESC LIMIT 1'
);
if ($summary_stmt) {
	$summary_row = $summary_stmt->fetch(PDO::FETCH_ASSOC);
	if (is_array($summary_row)) {
		$summary_content = json_decode((string)($summary_row['b_content'] ?? ''), true);
		if (is_array($summary_content)) {
			$bal_num = preg_replace('/[^\d.-]/', '', (string)($summary_content['total_balance'] ?? ''));
			if ($bal_num !== '' && is_numeric($bal_num)) {
				$summary_balance = number_format((float)$bal_num);
			}
			$summary_inquiry = (string)($summary_content['inquiry_datetime'] ?? '');
		}
	}
}
if ($summary_balance === '') {
	$summary_balance = '0';
}
?>

<table width="100%" class="adminMenuTable">
	<tr>
		<td align="left">
			<form name="fexcel" method="post" action="./bank_ok.php?<?= $func_library->queryString('w') ?>w=eu" enctype="multipart/form-data" style="margin:0;display:inline-block;">
				<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
				<input type="file" name="excel_file" class="input_text" accept=".xls,.xlsx" required>
				<button type="submit" class="black_btn">거래내역 업로드</button>
			</form>
		</td>
		<td align="right">
			<form name="fsearch" method="get" style="margin:0;display:inline-block;">
				<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
				<input type="date" name="start_date" class="input_text" value="<?= gh_h($start_date) ?>" style="width:140px;"> ~
				<input type="date" name="end_date" class="input_text" value="<?= gh_h($end_date) ?>" style="width:140px;">
				<select name="search_category" class="input_select">
					<option value="">거래형태 전체</option>
					<option value="2" <?php if ($search_category === '2') { ?>selected<?php } ?>>입금</option>
					<option value="1" <?php if ($search_category === '1') { ?>selected<?php } ?>>출금</option>
				</select>
				<select name="key_type" class="input_select">
					<option value="counterparty" <?php if ($key_type === 'counterparty') { ?>selected<?php } ?>>보낸분/받는분</option>
					<option value="amount" <?php if ($key_type === 'amount') { ?>selected<?php } ?>>거래금액</option>
				</select>
				<input type="text" name="keyword" value="<?= gh_h($keyword) ?>" class="input_text" style="width:160px;">
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
	<col width="180" align="center">
	</col>
	<col width="120" align="center">
	</col>
	<col width="120" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<tr>
		<td colspan="6" class="line1"></td>
	</tr>
	<tr class="bgcol1 bold col1 ht center">
		<td colspan="6" style="text-align:center;padding:12px 0;">
			현재총잔액 <?= gh_h($summary_balance) ?> 원 (조회기준시 : <?= gh_h($summary_inquiry) ?>)
		</td>
	</tr>
	<tr>
		<td colspan="6" class="line2"></td>
	</tr>
	<tr class="bgcol1 bold col1 ht center">
		<td>번호</td>
		<td>보낸분/받는분</td>
		<td>거래금액</td>
		<td>거래점</td>
		<td>거래일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./bank_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = [];
	$where = ' where 1=1 ';

	// 거래형태 — category(1:출금 2:입금)
	if ($search_category !== '') {
		$where .= ' and category = :searchCategory';
		$bind_param[] = ['searchCategory', $search_category];
	}

	// 키워드 — key_type별 b_content JSON LIKE (amount는 숫자만)
	if ($keyword !== '') {
		if ($key_type === 'amount') {
			$amount_keyword = preg_replace('/[^\d.-]/', '', $keyword);
			if ($amount_keyword !== '') {
				$where .= " and JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.amount')) like :keyword";
				$bind_param[] = ['keyword', $amount_keyword, 'like'];
			}
		} else {
			$where .= " and JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.counterparty')) like :keyword";
			$bind_param[] = ['keyword', $keyword, 'like'];
		}
	}

	// 거래일 기간 — b_content.transaction_datetime 앞 10자리(YYYY-MM-DD)
	if ($start_date !== '' && $end_date !== '') {
		$where .= " and LEFT(JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.transaction_datetime')), 10) BETWEEN :start_date AND :end_date";
		$bind_param[] = ['start_date', $start_date];
		$bind_param[] = ['end_date', $end_date];
	} elseif ($start_date !== '') {
		$where .= " and LEFT(JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.transaction_datetime')), 10) >= :start_date";
		$bind_param[] = ['start_date', $start_date];
	} elseif ($end_date !== '') {
		$where .= " and LEFT(JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.transaction_datetime')), 10) <= :end_date";
		$bind_param[] = ['end_date', $end_date];
	}

	$list_count = 10;
	$pg = max(1, (int)($pg ?? 1));
	$list_total = (int)$query_library->dataTotal($where, $bind_param, $table_name);
	$total_page = $list_count > 0 ? (int)ceil($list_total / $list_count) : 0;
	$list_start = ($pg - 1) * $list_count;
	$number = $list_total - $list_start;

	$sql = 'SELECT * FROM `' . $table_name . '` ' . $where
		. " ORDER BY JSON_UNQUOTE(JSON_EXTRACT(b_content, '$.transaction_datetime')) DESC, idx DESC"
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
		$content = json_decode((string)($d['b_content'] ?? ''), true);
		if (!is_array($content)) {
			$content = [];
		}
		$counterparty = (string)($content['counterparty'] ?? '');
		$branch = (string)($content['branch'] ?? '');
		$tx_datetime = (string)($content['transaction_datetime'] ?? '');
		$tx_date = $tx_datetime !== '' ? substr($tx_datetime, 0, 10) : '';

		$amount_num = preg_replace('/[^\d.-]/', '', (string)($content['amount'] ?? ''));
		$amount_text = ($amount_num !== '' && is_numeric($amount_num))
			? number_format((float)$amount_num)
			: '0';

		// 거래금액 표기 — 숫자만 출금 빨강 / 입금 파랑
		$category = (string)($d['category'] ?? '1');
		if ($category === '2') {
			$amount_html = '입금 <span style="color:blue"><strong>' . gh_h($amount_text) . '</strong></span> 원';
		} else {
			$amount_html = '출금 <span style="color:red"><strong>' . gh_h($amount_text) . '</strong></span> 원';
		}
	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td class="td2"><a href="./bank_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= (int)$d['idx'] ?>"><?= gh_h($counterparty) ?></a></td>
			<td><?= $amount_html ?></td>
			<td><?= gh_h($branch) ?></td>
			<td><?= gh_h($tx_date) ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./bank_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= (int)$d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./bank_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= (int)$d['idx'] ?>';">삭제</button>
			</td>
		</tr>
		<tr>
			<td colspan="6" class="line2"></td>
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