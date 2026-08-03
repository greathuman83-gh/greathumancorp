<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_worker_table';
?>

<table width="100%" class="adminMenuTable">
	<form name="fsearch" method="get">
		<input type="hidden" name="menu_code" value="<?= $menu_code ?>">
		<tr>
			<!-- <td align="left"><button type="button" class="black_btn" onclick="window.location='./worker_sort.php?<?= $func_library->queryString() ?>'">순서관리</button></td> -->
			<td align="right">
				<select name="key_type" class="input_select">
					<option value="w_name" <?php if ($key_type == 'w_name') { ?>selected<?php } ?>>이름</option>
				</select>
				<input type="text" name="keyword" value="<?= $keyword ?>" class="input_text">
				<button type="submit" class="search_btn">검색</button>
			</td>
		</tr>
	</form>
</table>
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col width="100" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<col>
	</col>
	<col width="110" align="center">
	</col>
	<col width="110" align="center">
	</col>
	<col width="110" align="center">
	</col>
	<tr>
		<td colspan="9" class="line1"></td>
	</tr>
	<tr class="bgcol1 bold col1 ht center">
		<td>번호</td>
		<td>고용형태</td>
		<td>이름</td>
		<td>입사일</td>
		<td>등록일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./worker_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = array();
	$where = " where 1=1 ";

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE (기본 w_name)
	$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'w_name');

	$orderby = "idx desc";
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, $pg, 10);
	$number = $list_result['number'];
	foreach ($list_result['result'] as $d) {
		$regdate = substr((string)($d['regdate'] ?? ''), 0, 10);
		$worker_type_label = $_workerType[$d['w_type']] ?? '';

	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td><?= gh_h($worker_type_label) ?></td>
			<td class="td2"><a href="./worker_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= (int)$d['idx'] ?>"><?= gh_h($d['w_name'] ?? '') ?></a></td>
			<td><?= gh_h($d['w_enterdate'] ?? '') ?></td>
			<td><?= gh_h($regdate) ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./worker_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= (int)$d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./worker_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= (int)$d['idx'] ?>';">삭제</button>
			</td>
		</tr>
		<tr>
			<td colspan="9" class="line2"></td>
		</tr>
	<?php $number--;
	} ?>
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