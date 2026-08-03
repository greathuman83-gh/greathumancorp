<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
?>
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="100" align="center"></col>
<col></col>
<col width="150" align="center"></col>
<col width="150" align="center"></col>
<col width="100" align="center"></col>
<col width="100" align="center"></col>
<col width="100" align="center"></col>
<col width="100" align="center"></col>
<col width="110" align="center"></col>
<tr><td colspan='9' class='line1'></td></tr>
<tr class="bgcol1 bold col1 ht center">
	<td>번호</td>
	<td>게시판이름</td>
	<td>게시판아이디</td>
	<td>스킨명</td>
	<td>접근허용권한</td>
	<td>쓰기권한</td>
	<td>읽기권한</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="window.location='./board_info_form.php?<?=$func_library->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$table_name = 'gh_board';
	$where = "where 1=1";
	$orderby = "idx desc";
	$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,100);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
	$regdate = substr($d['regdate'],0,10);
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<td class="td2"><a href="board_info_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$d['b_name']?></a></td>
	<td class="td2"><?=$d['bbsid']?></td>
	<td class="td2"><?=$d['b_skin']?></td>
	<td class="td2"><?=$d['b_level']?></td>
	<td class="td2"><?=$d['b_write']?></td>
	<td class="td2"><?=$d['b_read']?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='board_info_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='board_info_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
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
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>