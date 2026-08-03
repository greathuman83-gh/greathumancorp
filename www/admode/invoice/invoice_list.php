<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$category_table = 'gh_category_table';
$table_name = 'gh_invoice_table';
?>

<table width="100%" class="adminMenuTable">
	<form name="fsearch" method="get">
		<input type="hidden" name="menu_code" value="<?= $menu_code ?>">
		<tr>
			<!-- <td align="left"><button type="button" class="black_btn" onclick="window.location='./invoice_sort.php?<?= $func_library->queryString() ?>'">순서관리</button></td> -->
			<td align="right">
				<select name="key_type" class="input_select">
					<option value="title" <?php if ($key_type == 'title') { ?>selected<?php } ?>>타이틀</option>
					<option value="i_company" <?php if ($key_type == 'i_company') { ?>selected<?php } ?>>회사명</option>
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
	<col>
	</col>
	<col width="250" align="center">
	</col>
	<col width="110" align="center">
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
		<td>타이틀</td>
		<td>회사명</td>
		<td>금액</td>
		<td>발행일</td>
		<td>등록일</td>
		<td><button type="button" class="red_btn" onclick="window.location='./invoice_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bind_param = array();
	$where = " where category = :pageType ";
	$bind_param[] = array('pageType', $page_type);

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
	$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'title');

	$orderby = "num asc|regdate desc|idx desc";
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, $pg, 10);
	$number = $list_result['number'];
	foreach ($list_result['result'] as $d) {
		$regdate = substr($d['regdate'], 0, 10);

	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td class="td2"><a href="./invoice_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= $d['idx'] ?>"><?= $d['title'] ?></a></td>
			<td><?= $d['i_company'] ?></td>
			<td>₩<?= number_format($d['i_price']) ?></td>
			<td><?= $d['i_date'] ?></td>
			<td><?= $regdate ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./invoice_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./invoice_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= $d['idx'] ?>';">삭제</button>
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