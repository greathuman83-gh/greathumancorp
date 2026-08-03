<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$robots_path = $gh_path.'robots.txt';
if(file_exists($robots_path)){
	$fp = fopen($robots_path,'r');
	$robots_data = @fread($fp,filesize($robots_path));
	@fclose($fp);
}else{
	echo 'robots.txt 파일이 없습니다. 파일 생성 후 쓰기권한(777)을 부여해 주세요.';
}
?>
<form name="fwrite" method="post" action="./robots_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">robots.txt 설정</td>
	<td class="td2">
		<textarea name="robots_content" class="input_textarea" style="width:700px;height:150px;" required="required"><?=$robots_data ?? ''?></textarea>
	</td>
</tr>
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
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php';?>