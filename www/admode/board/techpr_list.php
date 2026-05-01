<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_techpr_table';
?>
<!--<button type="button" class="black_btn" onclick="window.location='./techpr_sort.php?<?=$funcLibrary->queryString()?>'" style="margin-bottom:5px;">순서관리</button> -->
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
	<td><button type="button" class="red_btn" onclick="window.location='./techpr_form.php?<?=$funcLibrary->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$where = "where 1=1";

	$orderby = "t_main desc|idx desc";
	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,$pg,10);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
	$thumbImg = $d['file1'] ? '<img src="'.$ghPath.'data/techpr/'.$d['file1'].'" width="100" style="vertical-align:middle">' : '';
	$mainIcon = $d['t_main'] == '1' ? '<button type="button" class="blue_icon_btn">메인</button> ' : '';
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<!-- <td>
		<form action="./techpr_ok.php?<?=$funcLibrary->queryString()?>w=oe&idx=<?=$d['idx']?>" method="post">
			<input type="number" name="num" class="input_text" value="<?=$d['num']?>" style="width:50px;" min="1">
			<button type="submit" class="black_icon_btn">변경</button>
		</form>
	</td> -->
	<td class="td2"><?= $thumbImg ?></td>
	<td>
		<a href="./techpr_form.php?<?=$funcLibrary->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>"><?= $mainIcon ?>><?=$d['title']?></a>
	</td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='./techpr_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./techpr_ok.php?<?=$funcLibrary->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
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
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>