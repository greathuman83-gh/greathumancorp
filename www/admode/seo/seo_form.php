<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_seo_table';

if($w == 'u'){
	$d = $queryLibrary->getData($idx,$tableName);
}else{
	$d = $queryLibrary->getColumn($tableName);
}

if($idx == 1){//메타태그
	$imageText = '파비콘';
	$size = '(권장 16x16 or 196x196)';
}else{//og 태그
	$imageText = 'og:image';
	$size = '(권장 1200x630) 이미지 업로드시 og:image 설정';
}
?>
<form name="fwrite" method="post" action="./seo_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<?php if($idx == 1){//메타태그?>
	<tr class="ht">
		<td class="td1">Title</td>
		<td class="td2">
			<input type="text" name="title" class="input_text" value="<?=$d['title']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Keywords</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_keywords" style="width:700px;height:50px;"><?=$d['meta_keywords']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Description</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_description" style="width:700px;height:50px;"><?=$d['meta_description']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Title(영문)</td>
		<td class="td2">
			<input type="text" name="title_en" class="input_text" value="<?=$d['title_en']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Keywords(영문)</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_keywords_en" style="width:700px;height:50px;"><?=$d['meta_keywords_en']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Description(영문)</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_description_en" style="width:700px;height:50px;"><?=$d['meta_description_en']?></textarea>
		</td>
	</tr>
	<!-- <tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Title(일문)</td>
		<td class="td2">
			<input type="text" name="title_jp" class="input_text" value="<?=$d['title_jp']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Keywords(일문)</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_keywords_jp" style="width:700px;height:50px;"><?=$d['meta_keywords_jp']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">Description(일문)</td>
		<td class="td2">
			<textarea class="input_textarea" name="meta_description_jp" style="width:700px;height:50px;"><?=$d['meta_description_jp']?></textarea>
		</td>
	</tr> -->
<?php }else{//og 태그?>
	<tr class="ht">
		<td class="td1">og 태그 사용 여부</td>
		<td class="td2">
			<input type="radio" name="og_use" value="1" <?php if($d['og_use'] == '1'){?>checked<?php }?>> 사용
			<input type="radio" name="og_use" value="2" <?php if($d['og_use'] == '2'){?>checked<?php }?>> 미사용
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:title</td>
		<td class="td2">
			<input type="text" name="og_title" class="input_text" value="<?=$d['og_title']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:description</td>
		<td class="td2">
			<textarea class="input_textarea" name="og_description" style="width:700px;height:50px;"><?=$d['og_description']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:site_name</td>
		<td class="td2">
			<input type="text" name="og_sitename" class="input_text" value="<?=$d['og_sitename']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:locale</td>
		<td class="td2">
			<input type="text" name="og_locale" class="input_text" value="<?=$d['og_locale']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:title(영문)</td>
		<td class="td2">
			<input type="text" name="og_title_en" class="input_text" value="<?=$d['og_title_en']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:description(영문)</td>
		<td class="td2">
			<textarea name="og_description_en" class="input_textarea" style="width:700px;height:50px;"><?=$d['og_description_en']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:site_name(영문)</td>
		<td class="td2">
			<input type="text" name="og_sitename_en" class="input_text" value="<?=$d['og_sitename_en']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:locale(영문)</td>
		<td class="td2">
			<input type="text" name="og_locale_en" class="input_text" value="<?=$d['og_locale_en']?>" style="width:700px;">
		</td>
	</tr>
	<!-- <tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:title(일문)</td>
		<td class="td2">
			<input type="text" name="og_title_jp" class="input_text" value="<?=$d['og_title_jp']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:description(일문)</td>
		<td class="td2">
			<textarea name="og_description_jp" class="input_textarea" style="width:700px;height:50px;"><?=$d['og_description_jp']?></textarea>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:site_name(일문)</td>
		<td class="td2">
			<input type="text" name="og_sitename_jp" class="input_text" value="<?=$d['og_sitename_jp']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:locale(일문)</td>
		<td class="td2">
			<input type="text" name="og_locale_jp" class="input_text" value="<?=$d['og_locale_jp']?>" style="width:700px;">
		</td>
	</tr> -->
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:type</td>
		<td class="td2">
			<input type="text" name="og_type" class="input_text" value="<?=$d['og_type']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:image:width</td>
		<td class="td2">
			<input type="text" name="og_image_width" class="input_text" value="<?=$d['og_image_width']?>" style="width:700px;">
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">og:image:height</td>
		<td class="td2">
			<input type="text" name="og_image_height" class="input_text" value="<?=$d['og_image_height']?>" style="width:700px;">
		</td>
	</tr>
<?php }?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1"><?=$imageText?></td>
	<td class="td2">
		<div class="file_list">
			<input type="file" class="input_text" name="file1" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
			<?php if($d['file1']){?>
				<input type="hidden" name="old_file1" value="<?=$d['file1']?>">
				<br>
				<img src="<?=$ghPath?>data/seo/<?=$d['file1']?>" width="100" style="margin-top:5px;">
				<input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
			<?php }?>
		</div>
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">서브 이미지</td>
	<td class="td2">
		<div class="file_list">
			<input type="file" class="input_text" name="file2" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size2?>
			<?php if($d['file2']){?>
				<input type="hidden" name="old_file2" value="<?=$d['file2']?>">
				<br>
				<img src="<?=$ghPath?>data/seo/<?=$d['file2']?>" width="100" style="margin-top:5px;">
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
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
function fwrite_submit(f){
	return true;
}
</script>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>