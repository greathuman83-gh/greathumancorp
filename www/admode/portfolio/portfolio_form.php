<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_portfolio_table';
$category_table = 'gh_category_table';

if($w == 'u'){
	$d = $query_library->getData($idx,$table_name);
	$attach_files = explode('|',$d['attach_files']);
	$text_data = explode('|',$d['content2']);
}else{
	//초기화
	$d = $query_library->getColumn($table_name);
	$attach_files = array();
	$text_data = array();
	$d['regdate'] = date('Y-m-d H:i:s');
}

$size = '(414x298)';
$size_detail = '(1760x800)';
?>
<form name="fwrite" method="post" action="./portfolio_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">공개설정</td>
	<td class="td2">
		<input type="radio" name="p_open" value="1" <?php if($d['p_open'] == '1' || $d['p_open'] == ''){?>checked<?php }?>> 공개
		<input type="radio" name="p_open" value="2" <?php if($d['p_open'] == '2'){?>checked<?php }?>> 비공개
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">분류</td>
	<td class="td2">
		<select name="c_code" class="input_select" required="required">
			<option value="">- 전체 -</option>
			<?php
				$bind_param = array();
				$where = "where category = 'portfolio' and depth = '1' ";
				$orderby = "num asc|c_code asc|idx desc";
				$list_result = $query_library->getList($where,'',$category_table,$orderby,1,99);
				foreach($list_result['result'] as $cate_data){
			?>
				<option value="<?=$cate_data['c_code']?>" <?php if($d['c_code'] == $cate_data['c_code']){?>selected<?php }?>><?=$cate_data['c_name']?></option>
			<?php }?>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">등록일</td>
	<td class="td2">
		<input type="text" name="regdate" class="input_text" value="<?=$d['regdate']?>" style="width:150px;" required="required" >
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">타이틀</td>
	<td class="td2">
		<input type="text" name="title" class="input_text" value="<?=$d['title']?>" style="width:700px;" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">내용</td>
	<td class="td2">
		<input type="text" name="title2" class="input_text" value="<?=$d['title2']?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">설명</td>
	<td class="td2">
		<textarea name="content" class="input_textarea" style="width:700px;height:150px;"><?=$d['content']?></textarea>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">농장명(업체명) *</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[0] ?? null?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">지역 [육묘, 수직농장]</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[1] ?? null?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">작목 [육묘]</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[2] ?? null?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">규모 [육묘, 수직농장]</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[3] ?? null?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">납품장비 [육묘장비]</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[4] ?? null?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">납품년월 *</td>
	<td class="td2">
		<input type="text" name="content2[]" class="input_text" value="<?=$text_data[5] ?? null?>" style="width:700px;">
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">제품 사양 <button type="button"class="addMultiContent red_icon_btn" data-max="20">+</button></td>
	<td class="td2">
		<?php if(count($spec_data['spec'] ??= array()) > 0){?>
			<?php
				for($i=0;$i<count((array)$spec_data['spec']);$i++){
				$spec_data['spec'][$i]['specContent'] = html_entity_decode($spec_data['spec'][$i]['specContent']);
			?>
				<div class="addContentList">
					<input type="hidden" name="spec[]">
					<input type="text" name="spec_part[]" class="input_text" value="<?=$spec_data['spec'][$i]['specPart']?>" style="width:200px;" placeholder="구분">
					<input type="text" name="spec_content[]" class="input_text" value="<?=$spec_data['spec'][$i]['specContent']?>" style="width:600px;" placeholder="사양">
					<?php if($i > 0){?>
						<div class="contentDelete"><button type="button"class="addContentDel gray_icon_btn">-</button></div>
					<?php }?>
				</div>
			<?php }?>
		<?php }else{?>
			<div class="addContentList">
				<input type="hidden" name="spec[]">
				<input type="text" name="spec_part[]" class="input_text" style="width:200px;" placeholder="구분">
				<input type="text" name="spec_content[]" class="input_text" style="width:600px;" placeholder="사양">
			</div>
		<?php }?>
	</td>
</tr> -->
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">리스트 썸네일</td>
	<td class="td2">
		<input type="file" class="input_text" name="thumb_file" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
		<?php if($d['thumb_file']){?>
			<input type="hidden" name="old_thumb_file" value="<?=$d['thumb_file']?>">
			<br>
			<img src="<?=$gh_path?>data/portfolio/<?=$d['thumb_file']?>" width="200" style="margin-top:5px;">
			<input type="checkbox" name="del_thumb_file" value="<?=$d['thumb_file']?>"> 삭제
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">상세 이미지 <button type="button" class="add_file red_icon_btn">+</button></td>
	<td class="td2">
		<?php if(array_filter($attach_files) != [] ){?>
			<?php for($i=0;$i<count((array)$attach_files);$i++){?>
			<div class="fileList">
				<input type="hidden" name="old_file[]" value="<?=$attach_files[$i]?>">
				<input type="hidden" name="old_file_name[]" value="<?=$attach_files_name[$i]?>">
				<input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size_detail?>
				<?php if($attach_files[$i]){?>
					<br>
					<img src="<?=$gh_path?>data/portfolio/<?=$attach_files[$i]?>" width="200" style="margin-top:5px;">
					<input type="checkbox" name="delete_files<?=$i?>" value="<?=$attach_files[$i]?>"> 삭제
				<?php }?>
			</div>
			<?php }?>
		<?php }else{?>
			<div class="fileList"><input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size_detail?></div>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='portfolio_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
	// 상세이미지 행 추가 — 최대 10개
	document.addEventListener('click', function(e){
		var addBtn = e.target.closest('.add_file');
		if (addBtn) {
			var count = 10;
			var fileLists = document.querySelectorAll('.fileList');
			if (fileLists.length >= count) {
				alert("상세이미지는 "+count+"개까지 등록하실 수 있습니다.");
				return;
			}
			var sizeTxt = <?= json_encode((string)($size_detail ?? ''), JSON_UNESCAPED_UNICODE) ?>;
			var data = '<div class="fileList"><input type="file" class="input_text" name="attach_files[]" > '+ sizeTxt+' <button type="button" class="del_file red_icon_btn">-</button></div>';
			fileLists[fileLists.length - 1].insertAdjacentHTML('afterend', data);
			return;
		}

		var delBtn = e.target.closest('.del_file');
		if (delBtn) {
			if (document.querySelectorAll('.fileList').length < 2) {
				alert("더이상 삭제하실 수 없습니다.");
				return;
			}
			if (window.confirm("삭제하시겠습니까?")) {
				var row = delBtn.closest('.fileList');
				if (row) row.remove();
			}
			return;
		}

		// 옵션 라디오 — 선택값을 hidden.optionRadioData에 반영
		var optionEl = e.target.closest('.optionSelect');
		if (optionEl) {
			var parent = optionEl.parentElement;
			var hidden = parent ? parent.querySelector('.optionRadioData') : null;
			if (hidden) hidden.value = optionEl.value;
		}
	});
});

function fwrite_submit(f){
	return true;
}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>