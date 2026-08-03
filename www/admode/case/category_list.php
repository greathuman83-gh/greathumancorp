<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_category_table';
?>
<style>
	.depth1 .cname {
		text-align: left;
	}

	.depth2 .cname {
		text-align: left;
		padding-left: 30px;
	}

	.depth3 .cname {
		text-align: left;
		padding-left: 60px;
	}
</style>

<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<tr>
		<td><a href="./category_sort.php?<?= $func_library->queryString() ?>cate=<?= $cate ?>"><button type="button" class="black_icon_btn">순서관리</button></td>
	</tr>
</table>
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col align="left">
	</col>
	<col width="150" align="center">
	</col>
	<col width="150" align="center">
	</col>
	<col width="180" align="center">
	</col>
	<tr>
		<td colspan='9' class='line1'></td>
	</tr>
	<tr class='bgcol1 bold col1 ht center'>
		<td>분류명</td>
		<td>분류코드</td>
		<td>사용유무</td>
		<td><button type="button" class="red_btn" onclick="window.location='./category_form.php?<?= $func_library->queryString('w') ?>w=a&depth=1'">등록</button></td>
	</tr>
	<?php
	//1차 분류
	$where = "where category = 'case' and depth = 1";
	$orderby = "num asc|c_code asc|idx desc";
	$list_result = $query_library->getList($where, $bind_param, $table_name, $orderby, 1, 99);
	foreach ($list_result['result'] as $d) {
		unset($bind_param);
	?>
		<tr class="depth1 ht center" id="<?= $d['c_code'] ?>">
			<td class="cname td2">
				<a href="category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d['idx'] ?>"><!-- <button type="button" class="depth1_icon_btn">1차</button>&nbsp;&nbsp; --><?= $d['c_name'] ?></a>
			</td>
			<td><?= $d['c_code'] ?></td>
			<td>
				<?php if ($d['c_open'] == '1') { ?>
					<button type="button" class="blue_icon_btn">사용</button>
				<?php } else { ?>
					<button type="button" class="gray_icon_btn">미사용</button>
				<?php } ?>
			</td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='category_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= $d['idx'] ?>';">삭제</button>
				<!-- <button type="button" class="blue_icon_btn" onclick="window.location='category_form.php?<?= $func_library->queryString() ?>w=a&parent=<?= $d['idx'] ?>&ccode=<?= $d['c_code'] ?>&depth=2'">2차 등록</button> -->
			</td>
		</tr>
		<tr>
			<td colspan="9" class="line2"></td>
		</tr>
		<?php
		//2차 분류
		$where2 = "where parent = :parent and substring(c_code,1,3) = :c_code and depth = 2";
		$bind_param[] = array('parent', $d['parent']);
		$bind_param[] = array('c_code', $d['c_code']);
		$orderby2 = "num asc|c_code asc|idx desc";
		$list_result2 = $query_library->getList($where2, $bind_param, $table_name, $orderby2, 1, 99);
		foreach ($list_result2['result'] as $d2) {
			unset($bind_param);
		?>
			<tr class="depth2 ht center" id="<?= $d2['c_code'] ?>">
				<td class="cname td2">
					<a href="category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d2['idx'] ?>"><button type="button" class="depth2_icon_btn">2차</button>&nbsp;&nbsp;<?= $d2['c_name'] ?></a>
				</td>
				<td><?= $d2['c_code'] ?></td>
				<td>
					<?php if ($d2['c_open'] == '1') { ?>
						<button type="button" class="blue_icon_btn">사용</button>
					<?php } else { ?>
						<button type="button" class="gray_icon_btn">미사용</button>
					<?php } ?>
				</td>
				<td>
					<button type="button" class="black_icon_btn" onclick="window.location='category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d2['idx'] ?>'">수정</button>
					<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='category_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= $d2['idx'] ?>';">삭제</button>
					<!-- <button type="button" class="green_icon_btn" onclick="window.location='category_form.php?<?= $func_library->queryString() ?>w=a&parent=<?= $d['idx'] ?>&ccode=<?= $d2['c_code'] ?>&depth=3'">3차 등록</button> -->
				</td>
			</tr>
			<tr>
				<td colspan="9" class="line2"></td>
			</tr>
			<?php
			//3차 분류
			$where3 = "where parent = :parent and substring(c_code,1,6) = :c_code  and depth = 3";
			$bind_param[] = array('parent', $d['parent']);
			$bind_param[] = array('c_code', $d2['c_code']);
			$orderby3 = "num asc|c_code asc|idx desc";
			$list_result3 = $query_library->getList($where3, $bind_param, $table_name, $orderby3, 1, 99);
			foreach ($list_result3['result'] as $d3) {
				unset($bind_param);
			?>
				<tr class="depth3 ht center" id="<?= $d3['c_code'] ?>">
					<td class="cname td2">
						<a href="category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d3['idx'] ?>"><button type="button" class="depth3_icon_btn">3차</button>&nbsp;&nbsp;<?= $d3['c_name'] ?></a>
					</td>
					<td><?= $d3['c_code'] ?></td>
					<td>
						<?php if ($d3['c_open'] == '1') { ?>
							<button type="button" class="blue_icon_btn">사용</button>
						<?php } else { ?>
							<button type="button" class="gray_icon_btn">미사용</button>
						<?php } ?>
					</td>
					<td>
						<button type="button" class="black_icon_btn" onclick="window.location='category_form.php?<?= $func_library->queryString() ?>w=u&idx=<?= $d3['idx'] ?>'">수정</button>
						<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./category_ok.php?<?= $func_library->queryString() ?>w=d&idx=<?= $d3['idx'] ?>';">삭제</button>
					</td>
				</tr>
				<tr>
					<td colspan="9" class="line2"></td>
				</tr>
			<?php } //3차 분류 끝
			?>
		<?php } //2차분류 끝
		?>
	<?php } //1차분류 끝
	?>
</table>
<?php include __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>