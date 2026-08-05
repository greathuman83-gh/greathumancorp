<?php
// 협력사 관리 목록 — gh_partner_table, 회사명·사업자번호·대표자 검색
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_partner_table';
?>

<table width="100%" class="adminMenuTable">
	<form name="fsearch" method="get">
		<input type="hidden" name="menu_code" value="<?= gh_h((string)($menu_code ?? '')) ?>">
		<tr>
			<td align="right">
				<select name="key_type" class="input_select">
					<option value="p_name" <?php if (($key_type ?? '') == 'p_name') { ?>selected<?php } ?>>회사명</option>
					<option value="p_number" <?php if (($key_type ?? '') == 'p_number') { ?>selected<?php } ?>>사업자등록번호</option>
					<option value="p_ceo_name" <?php if (($key_type ?? '') == 'p_ceo_name') { ?>selected<?php } ?>>대표자명</option>
				</select>
				<input type="text" name="keyword" value="<?= gh_h((string)($keyword ?? '')) ?>" class="input_text">
				<button type="submit" class="search_btn">검색</button>
			</td>
		</tr>
	</form>
</table>
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col width="100" align="center">
	</col>
	<col>
	</col>
	<col width="180" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<tr>
		<td colspan="9" class="line1"></td>
	</tr>
	<tr class="bgcol1 bold col1 ht center">
		<td>번호</td>
		<td>회사명</td>
		<td>사업자등록번호</td>
		<td>등록일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./partner_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = array();
	$where = ' where 1=1 ';

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE (기본 p_name)
	$func_library->appendWhereLike($where, $bind_param, $key_type ?? null, $keyword ?? null, 'p_name');

	$orderby = 'idx desc';
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, $pg, 10);
	$number = $list_result['number'];
	foreach ($list_result['result'] as $d) {
		$regdate = substr((string)($d['regdate'] ?? ''), 0, 10);
	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td class="td2"><a href="./partner_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= (int)$d['idx'] ?>"><?= gh_h($d['p_name'] ?? '') ?></a></td>
			<td><?= gh_h($d['p_number'] ?? '') ?></td>
			<td><?= gh_h($regdate) ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./partner_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= (int)$d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./partner_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= (int)$d['idx'] ?>';">삭제</button>
			</td>
		</tr>
		<tr>
			<td colspan="9" class="line2"></td>
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
