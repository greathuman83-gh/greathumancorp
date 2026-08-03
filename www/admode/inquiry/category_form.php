<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_category_table';
if($w == 'u'){//수정
	$d = $query_library->getData($idx,$table_name);
	$depth = $d['depth'];
}else{
	$d = $query_library->getColumn($table_name);
	$depth = 1;
}
?>
<form name="fwrite" method="post" action="./category_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<input type="hidden" name="depth" value="<?=$depth?>">

<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">사용유무</td>
	<td class="td2">
		<input type="checkbox" name="c_open" value="1" <?php if($d['c_open'] == '1' || $w == 'a'){?>checked<?php }?>> 사용
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">순서</td>
	<td class="td2">
		<input type="text" class="input_text1" name="num" value="<?=$d['num']?>" style="width:80px;"> (숫자가 높을수록 상위에 노출됩니다.)
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">상담분야명</td>
	<td class="td2">
		<input type="text" id="title" class="input_text1" name="c_name" value="<?=$d['c_name']?>" style="width:300px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">받는메일</td>
	<td class="td2">
		(여러개의 메일 발송시 ',' 로 구분해 주세요)<br>
		<input type="text" class="input_text1" name="c_text1" value="<?=$d['c_text1']?>" style="width:700px;">
	</td>
</tr>
<?php if($w == 'u'){?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">코드명</td>
	<td class="td2">
		<?=$d['c_code']?>
	</td>
</tr>
<?php }?>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='category_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
// 분류 폼 검증 — 타이틀 필수 검사는 비활성 유지
function fwrite_submit(f){
	/*
	var titleEl = document.getElementById('title');
	if(titleEl.value == ""){
		alert("분류명을입력해 주세요.");
		titleEl.focus();
		return false;
	}
	*/

	return true;
}
</script>
<?php include __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php';?>