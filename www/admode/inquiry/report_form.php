<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_report_table';

if($w == 'u'){//수정
	$d = $query_library->getData($idx,$table_name);
}else{
	$d = $query_library->getColumn($table_name);
}
?>
<form name="fwrite" method="post" action="./report_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
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
	<td class="td1">휴대전화</td>
	<td class="td2">
		<input type="text" class="input_text" name="r_mobile" value="<?=$d['r_mobile']?>" style="width:150px;">
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
	<td class="td1">문의유형</td>
	<td class="td2">
		<?php
			foreach($_reportType as $key => $val){
			if($key == $d['r_type']){
				$checked = 'checked';
			}else{
				$checked = '';
			}

		?>
			<input type="radio" name="r_type" value="<?=$key?>" <?=$checked?>> <?=$val?>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">제목</td>
	<td class="td2">
		<input type="text" class="input_text" name="title" value="<?=$d['title']?>" style="width:700px;">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">내용</td>
	<td class="td2">
		<textarea name="content" class="input_textarea" style="width:700px;height:150px;"><?=$d['content']?></textarea>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">답변회신</td>
	<td class="td2">
		<?php
			foreach($_reportReply as $key => $val){
			if($key == $d['r_reply']){
				$checked = 'checked';
			}else{
				$checked = '';
			}

		?>
			<input type="radio" name="r_reply" value="<?=$key?>" <?=$checked?>> <?=$val?>
		<?php }?>
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
<tr><td colspan="2" class="line3"></td></tr>
</table>
	<p align="center" style="margin-top:30px;">
		<button type="submit" class="red_btn">확 인</button>
		<button type="button" class="gray_btn" onclick="javascript:window.location='./report_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
	</p>
</form>

<script type="text/javascript">
// 첨부파일 UI — 동적 행 추가/삭제 (현재 폼 마크업에는 미사용, 패턴 유지)
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