<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_techpr_table';

if($w == 'u'){
	$d = $queryLibrary->getData($idx,$tableName);
}else{
	$d = $queryLibrary->getColumn($tableName);
}

$size = '(316x444)';
?>
<form name="fwrite" method="post" action="./techpr_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<!-- <tr class="ht">
	<td class="td1">분류</td>
	<td class="td2">
		<select name="c_code" class="input_select" required="required">
			<option value="">- 선택 -</option>
			<?php
				//1차 분류
				$categoryTable = 'gh_category_table';
				$where = "where depth = 1 and category = 'project'";
				$orderby = "num asc|c_code asc|idx desc";
				$listResult = $queryLibrary->getList($where,'',$categoryTable,$orderby,1,99);
				foreach($listResult['result'] as $cateData){
				unset($bindParam);
			?>
				<option value="<?=$cateData['c_code']?>" <?php if($d['c_code'] == $cateData['c_code']){?>selected<?php }?>><?=$cateData['c_name']?></option>
			<?php }//1차 분류?>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr> -->
<tr class="ht">
	<td class="td1">타이틀</td>
	<td class="td2">
		<input type="text" name="title" class="input_text" value="<?=$d['title']?>" style="width:700px;" required="required">
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">내용</td>
	<td class="td2">
		<input type="text" name="content" class="input_text" value="<?=$d['content']?>" style="width:700px;">
		<textarea class="input_textarea" name="content" style="width:700px;height:100px;"><?=$d['content']?></textarea>
	</td>
</tr> -->
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">이미지</td>
	<td class="td2">
		<div class="file_list">
			<input type="file" class="input_text" name="file1" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
			<?php if($d['file1']){?>
				<input type="hidden" name="old_file1" value="<?=$d['file1']?>">
				<br>
				<img src="<?=$ghPath?>data/techpr/<?=$d['file1']?>" width="150" style="margin-top:5px;">
				<input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
			<?php }?>
		</div>
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">링크</td>
	<td class="td2">
		<select name="link_target" class="input_select">
			<option value="_self" <?php if($d['link_target']=="_self"){?>selected<?php }?>>현재창</option>
			<option value="_blank" <?php if($d['link_target']=="_blank"){?>selected<?php }?>>새창</option>
		</select>
		<input type="text" name="link_url" class="input_text" style="width:700px;" value="<?=$d['link_url']?>">
	</td>
</tr> -->
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">서브 이미지</td>
	<td class="td2">
		<div class="file_list">
			<input type="file" class="input_text" name="file2" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size2?>
			<?php if($d['file2']){?>
				<input type="hidden" name="old_file2" value="<?=$d['file2']?>">
				<br>
				<img src="<?=$ghPath?>data/techpr/<?=$d['file2']?>" width="100" style="margin-top:5px;">
				<input type="checkbox" name="del_file2" value="<?=$d['file2']?>"> 삭제
			<?php }?>
		</div>
	</td>
</tr> -->
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='./techpr_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
function fwrite_submit(f){
	/*
	if($("#title").val() == ""){
		alert("제목을 입력해 주세요.");
		return false;
	}
	*/
	return true;
}
</script>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>