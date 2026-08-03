<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_cert_table';
?>
<!--<button type="button" class="black_btn" onclick="window.location='./cert_sort.php?<?=$func_library->queryString()?>'" style="margin-bottom:5px;">순서관리</button> -->
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="80" align="center"></col>
<!-- <col width="100" align="center"></col> -->
<col width="250" align="center"></col>
<col></col>
<col width="100" align="center"></col>
<col width="110" align="center"></col>
<tr><td colspan="9" class="line1"></td></tr>
<tr class="bgcol1 bold col1 ht center">
	<td>번호</td>
	<!-- <td>순서</td> -->
	<td>이미지</td>
	<td>타이틀</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="window.location='./cert_form.php?<?=$func_library->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$where = "where 1=1";

	if($page_type){
		$where .= " and category = :pageType ";
		$bind_param[] = array('pageType', $page_type);
	}

	$orderby = "num asc|idx desc";
	$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,$pg,10);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
	$thumb_img = $d['file1'] ? '<img src="'.$gh_path.'data/cert/'.$d['file1'].'" width="100" style="vertical-align:middle">' : '';
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<!-- <td>
		<form action="./cert_ok.php?<?=$func_library->queryString()?>w=oe&idx=<?=$d['idx']?>" method="post">
			<input type="number" name="num" class="input_text" value="<?=$d['num']?>" style="width:50px;" min="1">
			<button type="submit" class="black_icon_btn">변경</button>
		</form>
	</td> -->
	<td class="td2"><?= $thumb_img ?></td>
	<td>
		<a href="./cert_form.php?<?=$func_library->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['title']?></a>
	</td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='./cert_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./cert_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
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