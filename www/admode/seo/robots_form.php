<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$robotsPath = $ghPath.'robots.txt';
if(file_exists($robotsPath)){
	$fp = fopen($robotsPath,'r');
	$robotsData = @fread($fp,filesize($robotsPath));
	@fclose($fp);
}else{
	echo 'robots.txt 파일이 없습니다. 파일 생성 후 쓰기권한(777)을 부여해 주세요.';
}
?>
<form name="fwrite" method="post" action="./robots_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">robots.txt 설정</td>
	<td class="td2">
		<textarea name="robotsContent" class="input_textarea" style="width:700px;height:150px;" required="required"><?=$robotsData ?? ''?></textarea>
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
<?php include_once $ghPath.'include/html/admin_bottom.php';?>