<?php
// 사업건 관리 목록 — gh_business_table, 사업명·매출처·매입처 검색
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_business_table';
?>

<form name="fsearch" method="get">
	<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
	<table width="100%" class="adminMenuTable">
		<tr>
			<td align="right">
				<select name="key_type" class="input_select">
					<option value="b_name" <?php if (($key_type ?? '') == 'b_name') { ?>selected<?php } ?>>사업명</option>
					<option value="b_sales_info" <?php if (($key_type ?? '') == 'b_sales_info') { ?>selected<?php } ?>>매출처</option>
					<option value="b_purchasing_info" <?php if (($key_type ?? '') == 'b_purchasing_info') { ?>selected<?php } ?>>매입처</option>
				</select>
				<input type="text" name="keyword" value="<?= gh_h((string)($keyword ?? '')) ?>" class="input_text">
				<button type="submit" class="search_btn">검색</button>
			</td>
		</tr>
	</table>
</form>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col width="70" align="center"></col>
	<col></col>
	<col width="150" align="center"></col>
	<col width="150" align="center"></col>
	<col width="160" align="center"></col>
	<col width="110" align="center"></col>
	<col width="150" align="center"></col>
	<tr><td colspan="9" class="line1"></td></tr>
	<tr class="bgcol1 bold col1 ht center">
		<td>번호</td>
		<td>사업명(프로젝트명)</td>
		<td>매출처</td>
		<td>매입처</td>
		<td>사업기간</td>
		<td>등록일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./business_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = array();
	$where = ' where 1=1 ';

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE (기본 b_name)
	$func_library->appendWhereLike($where, $bind_param, $key_type ?? null, $keyword ?? null, 'b_name');

	$orderby = 'idx desc';
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, $pg, 10);
	$number = $list_result['number'];
	foreach ($list_result['result'] as $d) {
		$regdate = substr((string)($d['regdate'] ?? ''), 0, 10);
		$sales_info = json_decode((string)($d['b_sales_info'] ?? ''), true);
		$purchasing_info = json_decode((string)($d['b_purchasing_info'] ?? ''), true);
		if (!is_array($sales_info)) {
			$sales_info = [];
		}
		if (!is_array($purchasing_info)) {
			$purchasing_info = [];
		}
		$sales_company = (string)($sales_info['company'] ?? '');
		$purchasing_company = (string)($purchasing_info['company'] ?? '');
		$start_date = (string)($d['b_start_date'] ?? '');
		$end_date = (string)($d['b_end_date'] ?? '');
		$period = '';
		if ($start_date !== '' || $end_date !== '') {
			$period = $start_date . ' ~ ' . $end_date;
		}
	?>
	<tr class="list col1 ht center">
		<td><?= $number ?></td>
		<td class="td2"><a href="./business_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= (int)$d['idx'] ?>"><?= gh_h($d['b_name'] ?? '') ?></a></td>
		<td><?= gh_h($sales_company) ?></td>
		<td><?= gh_h($purchasing_company) ?></td>
		<td><?= gh_h($period) ?></td>
		<td><?= gh_h($regdate) ?></td>
		<td>
			<button type="button" class="black_icon_btn" onclick="window.location='./business_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= (int)$d['idx'] ?>'">수정</button>
			<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./business_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= (int)$d['idx'] ?>';">삭제</button>
		</td>
	</tr>
	<tr><td colspan="9" class="line2"></td></tr>
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
