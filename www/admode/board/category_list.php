<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_category_table';
?>
<style>
.depth1 .cname{text-align:left;}
.depth2 .cname{text-align:left;padding-left:30px;}
.depth3 .cname{text-align:left;padding-left:60px;}
</style>

<!-- <table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr>
	<td><a href="./category_sort.php?<?=$func_library->queryString('idx')?>"><button type="button" class="black_icon_btn">순서관리</button></td>
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
	<td><button type="button" class="red_btn" onclick="window.location='./category_form.php?<?=$func_library->queryString('w')?>w=a&depth=1'">등록</button></td>
</tr>
<?php
	$where = "where category = :cate and depth = 1";
	$bind_param[] = array('cate',$cate);
	$orderby = "num desc|c_code asc|idx desc";
	$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,99);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
?>
<tr class="depth2 ht center" id="<?=$d['c_code']?>">
	<td><?=$number?></td>
	<td class="td2">
		<a href="./category_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$d['c_name']?></a>
	</td>
	<td>
		<?php if($d['c_open'] == '1'){?>
			<button type="button" class="blue_icon_btn">사용</button>
		<?php }else{?>
			<button type="button" class="gray_icon_btn">미사용</button>
		<?php }?>
	</td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='./category_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./category_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center">
<tr>
	<td align="center">
		<?=$func_library->getPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php';?>