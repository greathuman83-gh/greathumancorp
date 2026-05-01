<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_category_table';
?>
<style>
.depth1 .cname{text-align:left;}
.depth2 .cname{text-align:left;padding-left:30px;}
.depth3 .cname{text-align:left;padding-left:60px;}
</style>

<!-- <table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr>
	<td><a href="./category_sort.php?<?=$funcLibrary->queryString('idx')?>"><button type="button" class="black_icon_btn">순서관리</button></td>
</tr>
</table> -->
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="80" align="center"></col>
<col></col>
<col width="150" align="center"></col>
<col width="180" align="center"></col>
<tr><td colspan='9' class='line1'></td></tr>
<tr class='bgcol1 bold col1 ht center'>
	<td>번호</td>
	<td>분류명</td>
	<td>사용유무</td>
	<td><button type="button" class="red_btn" onclick="window.location='./category_form.php?<?=$funcLibrary->queryString('w')?>w=a&depth=1'">등록</button></td>
</tr>
<?php
	$where = "where category = :cate and depth = 1";
	$bindParam[] = array('cate',$cate);
	$orderby = "num desc|c_code asc|idx desc";
	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,1,99);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
?>
<tr class="depth2 ht center" id="<?=$d['c_code']?>">
	<td><?=$number?></td>
	<td class="td2">
		<a href="./category_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$d['c_name']?></a>
	</td>
	<td>
		<?php if($d['c_open'] == '1'){?>
			<button type="button" class="blue_icon_btn">사용</button>
		<?php }else{?>
			<button type="button" class="gray_icon_btn">미사용</button>
		<?php }?>
	</td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='./category_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./category_ok.php?<?=$funcLibrary->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center">
<tr>
	<td align="center">
		<?=$funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include $ghPath.'include/html/admin_bottom.php';?>