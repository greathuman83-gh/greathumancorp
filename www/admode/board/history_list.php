<?php
$ghPath = '../../';
include_once($ghPath."include/html/admin_top.php");
?>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="80" align="center"></col>
<col></col>
<col width="110" align="center"></col>
<col width="110" align="center"></col>
<tr><td colspan="10" class="line1"></td></tr>
<tr class="bgcol1 bold col1 ht center">
	<td>번호</td>
	<td>연도</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="location.href='history_form.php?<?=$funcLibrary->queryString('w')?>w=a';">등록</button></td>
</tr>
<?php
	$tableName = 'gh_history_table';
	$where = "where 1=1";
	$orderby = "year asc|idx desc";
	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,$pg,10);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
	$regdate = substr($d['regdate'],0,10);

	if($d['file1']){
		$imageTag = '<img src="'.$ghPath.'data/history/'.$d['file1'].'" width="100">';
	}else{
		$imageTag = '';
	}
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<td><a href="history_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$d['year']?> 년</a></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="location.href='history_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>';">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='history_ok.php?w=d&idx=<?=$d['idx']?>&<?=$funcLibrary->queryString('w,idx');?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="10" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr>
	<td align="center">
		<?=$funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once $ghPath."include/html/admin_bottom.php";?>