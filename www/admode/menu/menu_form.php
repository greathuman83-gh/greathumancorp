<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_admin_menu_table';
if($w == 'u'){//수정
	$d = $queryLibrary->getData($idx,$tableName);
	$depth = $d['depth'];
}else{
	$d = $queryLibrary->getColumn($tableName);
}
?>
<form name="fwrite" method="post" action="./menu_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<input type="hidden" name="depth" value="<?=$depth?>">

<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">사용유무</td>
	<td class="td2">
		<input type="hidden" name="m_open" value="">
		<input type="checkbox" name="m_open" value="1" <?php if($d['m_open'] == '1' || $w == 'a'){?>checked<?php }?>> 사용
	</td>
</tr>
<?php if($w == 'u'){?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">코드명</td>
	<td class="td2">
		<?=$d['m_code']?>
	</td>
</tr>
<?php }?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">메뉴명</td>
	<td class="td2">
		<input type="text" id="title" class="input_text" name="m_name" value="<?=$d['m_name']?>" style="width:300px;" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">메뉴코드명</td>
	<td class="td2">
		<input type="text" id="title" class="input_text" name="m_codeName" value="<?=$d['m_codeName']?>" style="width:300px;" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">메뉴링크</td>
	<td class="td2">
		<select name="m_link_target" class="input_select">
			<option value="_self" <?php if($d['m_link_target'] == '_self'){?>selected<?php }?>>현재창</option>
			<option value="_blank" <?php if($d['m_link_target'] == '_blank'){?>selected<?php }?>>새창</option>
		</select>
		<input type="text" class="input_text" name="m_link" value="<?=$d['m_link']?>" style="width:700px;">
		<input type="checkbox" name="m_link_type" value="1" <?php if($d['m_link_type'] == '1'){?>checked<?php }?>> 외부링크
	</td>
</tr>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='menu_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
function fwrite_submit(f){
	if($('#title').val() == ''){
		alert('메뉴명을 입력해 주세요.');
		$('#title').focus();
		return false;
	}
	return true;
}
</script>
<?php include $ghPath.'include/html/admin_bottom.php';?>