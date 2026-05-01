<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_popup_table';
$uploadDirectory = 'popup';
$imageSize = '';
if($w == 'u'){//수정
	$d = $queryLibrary->getData($idx,$tableName);
}else{
	$d = $queryLibrary->getColumn($tableName);
}
?>
<!-- 메인 테이블 -->
<script language='javascript'>
function dreamkos_imgview()
{
  	img_pre = 'pre';
  	if(event.srcElement.value.match(/(.jpg|.jpeg|.gif|.png)/))
  	{
  		document.images[img_pre].src = event.srcElement.value;
  		document.images[img_pre].style.display = '';
  	}
  	else
  	{
		document.images[img_pre].style.display = 'none';
  	}
}

function usermode_view(view)
{
	document.f.pop_size_w.disabled = view;
}

function form_check(formthis)
{
	toOpener();
	return chkForm(formthis);

}
</script>
<!-- 컨텐츠 -->
<form name="fwrite" method="post" action="./popup_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">

<table width="100%" align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class='ht'>
	<td class="td1">팝업창 타이틀</td>
	<td class="td2">
		<input type="text" class="input_text" id="pop_subject" name="pop_subject" value="<?=$d['pop_subject']?>" style="width:450px;">
		<input type="hidden" name="pop_view" value="">
		<input type="checkbox" name="pop_view" value="1" <?php if($d['pop_view'] == '1'){?>checked<?php }?>> [<font color="red"><b>공개여부</font></b>]</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">기간</td>
	<td class="td2">
	<input type="text" value="<?php echo $d['start_date']; ?>" name="start_date" class="input_text frm_input date"  style="width:100px;" id="start_date" readonly> ~  <input type="text" value="<?php echo $d['end_date']; ?>" name="end_date" class="input_text frm_input date"  style="width:100px;" id="end_date" readonly> 
	<input type="hidden" name="always" value="">
	<input type="checkbox" name="always" value="Y" <?php if($d['always'] == 'Y'){?>checked<?php }?>> 항상
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">기본 팝업 사이즈</td>
	<td class="td2">
		<input type="radio" name="pop_size_d" value="0" checked>선택안함
		<input type="radio"  name="pop_size_d" value="1">가로 : 300 Ⅹ 세로 : 350
		<input type="radio"  name="pop_size_d" value="2">가로 : 350 Ⅹ  세로 : 300
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">팝업 사이즈</td>
	<td class="td2">
		가로 : <input type="text" name="pop_size_w" class="input_text" size="4"  maxlength="4" value="<?=$d['pop_size_w']?>">px
		세로 : <input type="text" name="pop_size_h" class="input_text" size="4"  maxlength="4" value="<?=$d['pop_size_h']?>">px
		(이미지 등록시 이미지 크기에 맞춰 자동 설정 됩니다.)
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">팝업창 위치</td>
	<td class="td2">
		왼쪽 : <input type="text" name="pop_location_left" class="input_text" size="4"  maxlength="4" value="<?=($d['pop_location_left'])?$d['pop_location_left']:"10"?>">px
		상단 : <input type="text" name="pop_location_top" class="input_text" size="4"  maxlength="4" value="<?=($d['pop_location_top'])?$d['pop_location_top']:"10"?>">px
		(표시될 팝업창의 위치 설정!)
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">팝업 링크 URL</td>
	<td class="td2">
		<select name="pop_target" class="input_select">
			<option value="_self" <?php if($d['pop_target']=="_self"){?>selected<?php }?>>현재창</option>
			<option value="_blank" <?php if($d['pop_target']=="_blank"){?>selected<?php }?>>새창</option>
		</select>
		<input type="text" name="pop_link_url" class="input_text" size="57" value="<?=$d['pop_link_url']?>">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">팝업 내용</td>
	<td class="td2"><textarea name="content" rows="10" cols=80 border=0 class="input_text"><?=$d['pop_content']?></textarea></td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">팝업 이미지 등록</td>
	<td class="td2">
		<input type="file" class="input_text" name="file1" onchange="imgFileCheck(this,<?=IMG_SIZE?>)">
		<?php if($d['file1']){ ?>
			<input type="hidden" name="oldFile1" value="<?=$d['file1']?>">
			<br><br><img src="<?=$ghPath?>data/<?=$uploadDirectory?>/<?=$d['file1']?>" width="150">
			<input type="checkbox" name="delFile1" value="<?=$d['file1']?>"> 삭제
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class='ht'>
	<td class="td1">주의사항</td>
	<td class="td2">
<p><b><font color="red">※ 필수적으로 입력하셔야 될 것.</font></b><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. 팝업창의 타이틀 제목<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. 팝업창이 위치할 곳(팝업 위치)가로위치,세로위치</p>
 <font color="red"><b>&nbsp;- 팝업을 이미지로 제작하신 경우</b></font><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. 팝업 이미지 등록을 이용하여 이미지를 선택하신 후<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. 팝업창을 눌렸을 때 이동할 경로를 팝업링크URL에 입력하세요.
<p><font color="red"><b>&nbsp;- 팝업을 텍스트로 쓰실 경우</b></font><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. 팝업창의 크기를 팝업사이즈에 입력하신후<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. 팝업창에 들어갈 내용을 팝업내용에 입력하세요.</p>
<p><b><font color="red">※ 등록하신후  [공개여부]에 체크하시면 메인에서 표시됩니다.</font></b><br>
	</td>
</tr>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='popup_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
function fwrite_submit(f)
{
	if($('#pop_subject').val() == ''){
		alert('타이틀을 입력해 주세요.');
		return false;
	}
	return true;
}
</script>

<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>