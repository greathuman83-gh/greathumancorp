<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_category_table';
if($w == 'u'){//수정
	$d = $queryLibrary->getData($idx,$tableName);
	$depth = $d['depth'];
}else{
	$d = $queryLibrary->getColumn($tableName);
}
$size = '';
?>
<form name="fwrite" method="post" action="./category_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
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
	<td class="td1">분류명</td>
	<td class="td2">
		<input type="text" id="title" class="input_text1" name="c_name" value="<?=$d['c_name']?>" style="width:300px;" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">추가텍스트</td>
	<td class="td2">
		<input type="text" id="title" class="input_text1" name="c_text1" value="<?=$d['c_text1']?>" style="width:300px;">
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
<?php if($cate == 'product'){?>
	<?php if($depth == '1'){?>
		<tr><td colspan="2" class="line2"></td></tr>
		<tr class="ht">
			<td class="td1">이미지</td>
			<td class="td2">
				<div class="file_list">
					<input type="file" class="input_text" name="file1"> <?=$size?>
					<?php if($d['file1']){?>
						<input type="hidden" name="old_file1" value="<?=$d['file1']?>">
						<br><img src="<?=$ghPath?>data/category/<?=$d['file1']?>" width="150" style="margin-top:10px;">
						<input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
					<?php }?>
				</div>
			</td>
		</tr>
		<tr><td colspan="2" class="line2"></td></tr>
		<tr class="ht">
			<td class="td1">이미지(클릭시)</td>
			<td class="td2">
				<div class="file_list">
					<input type="file" class="input_text" name="file2"> <?=$size?>
					<?php if($d['file2']){?>
						<input type="hidden" name="old_file2" value="<?=$d['file2']?>">
						<br><img src="<?=$ghPath?>data/category/<?=$d['file2']?>" width="150" style="margin-top:10px;">
						<input type="checkbox" name="del_file2" value="<?=$d['file2']?>"> 삭제
					<?php }?>
				</div>
			</td>
		</tr>
	<?php }else if($depth == '3'){?>
		<tr><td colspan="2" class="line2"></td></tr>
		<tr class="ht">
			<td class="td1">이미지</td>
			<td class="td2">
				<div class="file_list">
					<input type="file" class="input_text" name="file1"> <?=$size?>
					<?php if($d['file1']){ ?>
					<input type="hidden" name="oldFile1" value="<?=$d['file1']?>">
					<br><br><img src="<?=$ghPath?>data/category/<?=$d['file1']?>" width="150">
					<input type="checkbox" name="delFile1" value="<?=$d['file1']?>"> 삭제
					<?php }?>
				</div>
			</td>
		</tr>
	<?php }?>
<?php }?>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='category_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
function fwrite_submit(f){
	return true;
}
</script>
<?php include $ghPath.'include/html/admin_bottom.php';?>