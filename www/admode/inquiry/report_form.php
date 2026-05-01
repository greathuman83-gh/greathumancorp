<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_report_table';

if($w == 'u'){//수정
	$d = $queryLibrary->getData($idx,$tableName);
}else{
	$d = $queryLibrary->getColumn($tableName);
}
?>
<form name="fwrite" method="post" action="./report_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
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
		<button type="button" class="gray_btn" onclick="javascript:window.location='./report_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
	</p>
</form>

<script type="text/javascript">
$(function(){
	//================== 파일 등록 폼 추가 =================
	$('.add_file').on('click',function(){
		var count = 10;
		var size = new Array();
		var list_num = $('.file_list').length;

		if($('.file_list').length >= count){
			alert('첨부 파일은  '+count+'개까지 등록하실 수 있습니다.');
			return;
		}
		var data;
		
		if(!size[$('.file_list').length]){
			var size_txt = '';
		}else{
			var size_txt = size[$('.file_list').length];
		}
		
		data = '<div class="file_list"><input type="file" class="input_text" name="attachFiles[]" > '+ size_txt+' <button type="button" class="del_file red_icon_btn">-</button></div>';
		$('.file_list').last().after(data);
	});


	$(document).on('click','.del_file',function(){
		if($('.file_list').length < 2){
			alert('더이상 삭제하실 수 없습니다.');
			return;
		}

		if (window.confirm('삭제하시겠습니까?')){
			$(this).parent('.file_list').remove();
		}
	});
	//================== 파일 등록 폼 추가 끝 ================
});

function fwrite_submit(f)
{
	return true;
}
</script>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>