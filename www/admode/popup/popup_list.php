<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
?>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="50" align="center"></col>
<col></col>
<col width="150"></col>
<col width="110"></col>
<col width="110" align="center"></col>
<tr><td colspan="9" class="line1"></td></tr>
<tr class='bgcol1 bold col1 ht center'>
	<td>번호</td>
	<td>제목</td>
	<td>적용</td>
	<td>등록날짜</td>
	<td><button type="button" class="red_btn" onclick="window.location='./popup_form.php?<?=$func_library->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$where = "where 1=1";
	if($page_type){
		$where = $where." and category = :pageType ";
		$bind_param[] = array('pageType', $page_type);
	}
	$orderby = "num desc|idx desc";
	$list_result = $query_library->getList($where,$bind_param,'gh_popup_table',$orderby,$pg,10);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
?>
<tr class='list$list col1 ht center'>
	<td><?=$number?></td>
	<td><?=$d['pop_subject']?></td>
	<td><?php if ($d['pop_view'] == '1') echo "○"; else echo "X"; ?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='popup_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='popup_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center" class="adminMenuTable">
<tr>
	<td align="center">
		<?=$func_library->getPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>