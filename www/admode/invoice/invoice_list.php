<?php
$ghPath = '../../';
include_once($ghPath . 'include/html/admin_top.php');

$categoryTable = 'gh_category_table';
$tableName = 'gh_invoice_table';
?>

<table width="100%" class="adminMenuTable">
	<form name="fsearch" method="get">
		<input type="hidden" name="menuCode" value="<?= $menuCode ?>">
		<tr>
			<!-- <td align="left"><button type="button" class="black_btn" onclick="window.location='./invoice_sort.php?<?= $funcLibrary->queryString() ?>'">순서관리</button></td> -->
			<td align="right">
				<select name="keyType" class="input_select">
					<option value="title" <?php if ($keyType == 'title') { ?>selected<?php } ?>>타이틀</option>
					<option value="i_company" <?php if ($keyType == 'i_company') { ?>selected<?php } ?>>회사명</option>
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
		<td><button type="button" class="red_btn" onclick="window.location='./invoice_form.php?<?= $funcLibrary->queryString('w') ?>w=a'">등록</button></td>
	</tr>
	<?php
	$bindParam = array();
	$where = " where category = :pageType ";
	$bindParam[] = array('pageType', $pageType);

	if ($keyType || $keyword) {
		$where .= " and $keyType like :keyword ";
		$bindParam[] = array('keyword', $keyword, 'like');
	}

	$orderby = "num asc|regdate desc|idx desc";
	$listResult = $queryLibrary->getList($where, $bindParam, $tableName, $orderby, $pg, 10);
	$number = $listResult['number'];
	foreach ($listResult['result'] as $d) {
		$regdate = substr($d['regdate'], 0, 10);

	?>
		<tr class="list col1 ht center">
			<td><?= $number ?></td>
			<td class="td2"><a href="./invoice_form.php?<?= $funcLibrary->queryString('pg,idx,w') ?>w=u&idx=<?= $d['idx'] ?>"><?= $d['title'] ?></a></td>
			<td><?= $d['i_company'] ?></td>
			<td>₩<?= number_format($d['i_price']) ?></td>
			<td><?= $d['i_date'] ?></td>
			<td><?= $regdate ?></td>
			<td>
				<button type="button" class="black_icon_btn" onclick="window.location='./invoice_form.php?<?= $funcLibrary->queryString() ?>w=u&idx=<?= $d['idx'] ?>'">수정</button>
				<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./invoice_ok.php?<?= $funcLibrary->queryString() ?>w=d&idx=<?= $d['idx'] ?>';">삭제</button>
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
			<?= $funcLibrary->getPaging($_config['pageListEa'], $pg, $listResult['totalPage'], $_SERVER['PHP_SELF'] . '?' . $funcLibrary->queryString('pg') . 'pg=') ?>
		</td>
	</tr>
</table>
<?php include_once $ghPath . 'include/html/admin_bottom.php'; ?>