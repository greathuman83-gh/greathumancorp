<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_inquiry_table';

if($w == 'u'){//수정
	$d = $query_library->getData($idx,$table_name);
	$attach_files = explode('|',$d['attach_files'] ?? '');
	$attach_files_name = explode('|',$d['attach_files_name'] ?? '');
}else{
	$d = $query_library->getColumn($table_name);
	$attach_files = array();
	$attach_files_name = array();
}
?>
<form name="fwrite" method="post" action="./inquiry_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">이름</td>
	<td class="td2">
		<input type="text" class="input_text" name="r_name" value="<?=$d['r_name']?>" style="width:150px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">기관/회사</td>
	<td class="td2">
		<input type="text" class="input_text" name="r_company" value="<?=$d['r_company']?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">휴대전화</td>
	<td class="td2">
		<input type="text" class="input_text" name="r_tel" value="<?=$d['r_tel']?>" style="width:150px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">이메일</td>
	<td class="td2">
		<input type="text" class="input_text" name="r_email" value="<?=$d['r_email']?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">문의분야 제품</td>
	<td class="td2">
		<?php
			foreach($_inquiryCategory as $key => $val){
			if(strpos($d['r_product'],$key) !== false){
				$checked = 'checked';
			}else{
				$checked = '';
			}

		?>
			<input type="checkbox" name="r_product[]" value="<?=$key?>" <?=$checked?>> <?=$val?>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">문의분야 기타</td>
	<td class="td2">
		<?php
			foreach($_inquiryEtc as $key => $val){
			if(strpos($d['r_etc'],$key) !== false){
				$checked = 'checked';
			}else{
				$checked = '';
			}

		?>
			<input type="checkbox" name="r_etc[]" value="<?=$key?>" <?=$checked?>> <?=$val?>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">유입경로</td>
	<td class="td2">
		<?php
			foreach($_inquiryReferer as $key => $val){
			if(strpos($d['r_referer'],$key) !== false){
				$checked = 'checked';
			}else{
				$checked = '';
			}
		?>
			<input type="checkbox" name="r_referer[]" value="<?=$key?>" <?=$checked?>> <?=$val?>
		<?php }?>
		<input type="text" class="input_text" name="r_etc_text" value="<?=$d['r_etc_text']?>" style="width:300px;">
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">제목</td>
	<td class="td2">
		<input type="text" class="input_text" name="title" value="<?=$d['title']?>" style="width:700px;">
	</td>
</tr> -->
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">내용</td>
	<td class="td2">
		<textarea name="r_content" class="input_textarea" style="width:700px;height:150px;"><?=$d['r_content']?></textarea>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">상태</td>
	<td class="td2">
		<select name="status" class="input_select">
			<option value="1" <?php if($d['status'] == '1'){?>selected<?php }?>>확인중</option>
			<option value="2" <?php if($d['status'] == '2'){?>selected<?php }?>>완료</option>
		</select>
	</td>
</tr>
<!--
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">첨부파일 <button type="button" class="add_file red_icon_btn">+</button></td>
	<td class="td2">
		<?php if(array_filter($attach_files) != [] ){?>
			<?php
				for($i=0;$i<count((array)$attach_files);$i++){
			?>
			<div class="file_list">
				<input type="hidden" name="old_file[]" value="<?=$attach_files[$i]?>">
				<input type="hidden" name="old_file_name[]" value="<?=$attach_files_name[$i]?>">
				<input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="attachFileCheck(this,<?=FILE_SIZE?>)">
				<?php if($attach_files[$i]){?>
					<br><span class="file" style="margin-top:5px;"></span> <a href="<?=$gh_path?>board/download.php?board=N&bbsid=inquiry&file_name=<?=$attach_files[$i]?>&o_file_name=<?=urlencode($attach_files_name[$i])?>"><?=$attach_files_name[$i]?></a>
					<input type="checkbox" name="del_file<?=$i?>" value="<?=$attach_files[$i]?>"> 삭제
				<?php }?>
			</div>
			<?php }?>
		<?php }else{?>
			<div class="file_list"><input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$size3?></div>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">첨부파일</td>
	<td class="td2">
		<input type="file" class="input_text" name="file1" onchange="attachFileCheck(this,<?=FILE_SIZE?>)">
		<?php if($d['file1']){?>
			<input type="hidden" name="old_file1" value="<?=$d['file1']?>">
			&nbsp;&nbsp;&nbsp;
			 다운로드 : <a href="<?=$gh_path?>board/download.php?board=N&bbsid=inquiry&file_name=<?=$d['file1']?>&o_file_name=<?=$d['file1_name']?>"><?=$d['file1_name']?></a> 
			&nbsp;&nbsp;&nbsp;&nbsp;
			 <input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
		<?php }?>
	</td>
</tr>
-->
<tr><td colspan="2" class="line3"></td></tr>
</table>
	<p align="center" style="margin-top:30px;">
		<button type="submit" class="red_btn">확 인</button>
		<button type="button" class="gray_btn" onclick="javascript:window.location='./inquiry_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
	</p>
</form>

<script type="text/javascript">
// 첨부파일 UI — 동적 행 추가/삭제 (마크업 주석 구간과 동일 패턴)
document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var addFileBtn = e.target.closest('.add_file');
		if (addFileBtn) {
			var count = 10;
			var size = [];
			var fileLists = document.querySelectorAll('.file_list');

			if (fileLists.length >= count) {
				alert('첨부 파일은  ' + count + '개까지 등록하실 수 있습니다.');
				return;
			}

			var sizeTxt = size[fileLists.length] ? size[fileLists.length] : '';
			var data = document.createElement('div');
			data.className = 'file_list';
			data.innerHTML = '<input type="file" class="input_text" name="attach_files[]" > ' + sizeTxt + ' <button type="button" class="del_file red_icon_btn">-</button>';
			if (fileLists.length > 0) {
				fileLists[fileLists.length - 1].after(data);
			}
			return;
		}

		var delFileBtn = e.target.closest('.del_file');
		if (delFileBtn) {
			var fileLists = document.querySelectorAll('.file_list');
			if (fileLists.length < 2) {
				alert('더이상 삭제하실 수 없습니다.');
				return;
			}

			if (window.confirm('삭제하시겠습니까?')) {
				var parent = delFileBtn.closest('.file_list');
				if (parent) {
					parent.remove();
				}
			}
		}
	});
});

function fwrite_submit(f)
{
	return true;
}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>