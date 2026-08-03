<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$category_table = 'gh_category_table';
$table_name = 'gh_case_table';
?>

<table width="100%" class="adminMenuTable">
	<form name="fsearch" method="get">
		<input type="hidden" name="menu_code" value="<?= $menu_code ?>">
		<tr>
			<!-- <td align="left"><button type="button" class="black_btn" onclick="window.location='./case_sort.php?<?= $func_library->queryString() ?>'">순서관리</button></td> -->
			<td align="right">
				<select name="ccode" class="input_select">
					<option value="">- 전체 -</option>
					<?php
					$bind_param = array();
					$where = "where category = 'case' and depth = '1' ";
					$orderby = "num asc|c_code asc|idx desc";
					$list_result = $query_library->getList($where, '', $category_table, $orderby, 1, 99);
					foreach ($list_result['result'] as $cate_data) {
					?>
						<option value="<?= $cate_data['c_code'] ?>" <?php if ($ccode == $cate_data['c_code']) { ?>selected<?php } ?>><?= $cate_data['c_name'] ?></option>
					<?php } ?>
				</select>
				<select name="key_type" class="input_select">
					<option value="title" <?php if ($key_type == 'title') { ?>selected<?php } ?>>타이틀</option>
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
	<!-- <col width="100" align="center"></col> -->
	<col width="100" align="center">
	</col>
	<col width="250" align="center">
	</col>
	<col width="250" align="center">
	</col>
	<col>
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
		<!-- <td>순서</td> -->
		<td>공개여부</td>
		<td>분류</td>
		<td>이미지</td>
		<td>타이틀</td>
		<td>등록일</td>
		<td><?php if (!isset($main)) { ?><button type="button" class="red_btn" onclick="window.location='./case_form.php?<?= $func_library->queryString('w') ?>w=a'">등록</button><?php } ?></td>
	</tr>
	<?php
	$bind_param = array();
	$where = " where 1=1 ";

	if ($main ?? '') {
		$where .= " and c_main = '1'";
	}

	if ($ccode) {
		$where .= " and c_code = :ccode";
		$bind_param[] = array('ccode', $ccode);
	}

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
	$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'title');

	$orderby = "num asc|regdate desc|idx desc";
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, $pg, 10);
	$number = $list_result['number'];
	foreach ($list_result['result'] as $d) {
		$regdate = substr($d['regdate'], 0, 10);
		$thumb_img = $d['thumb_file'] ? '<img src="' . $gh_path . 'data/case/' . $d['thumb_file'] . '" width="200" style="vertical-align:middle">' : '';

		$category_where = " where c_code = :c_code and category = 'case' and depth = '1' ";
		$category_bind_param = array();
		$category_bind_param[] = array('c_code', $d['c_code']);
		$category_data = $query_library->getData2($category_where, $category_bind_param, $category_table);
		$category_text = $category_data['c_name'];


	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<!-- <td>
		<form action="./case_ok.php?<?= $func_library->queryString() ?>w=oe&idx=<?= $d['idx'] ?>" method="post">
			<input type="number" name="num" class="input_text" value="<?= $d['num'] ?>" style="width:50px;" min="1">
			<button type="submit" class="black_icon_btn">변경</button>
		</form>
	</td> -->
			<td>
				<?php if ($d['c_open'] == '1') { ?>
					<button type="button" class="blue_icon_btn">공개</button>
				<?php } else { ?>
					<button type="button" class="gray_icon_btn">비공개</button>
				<?php } ?>
			</td>
			<td><?= $category_text ?></td>
			<td class="td2"><?= $thumb_img ?></td>
			<td class="td2"><a href="./case_form.php?<?= $func_library->queryString('pg,idx,w') ?>w=u&idx=<?= $d['idx'] ?>"><?= $d['title'] ?></a></td>
			<td><?= $regdate ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./case_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./case_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= $d['idx'] ?>';">삭제</button>
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