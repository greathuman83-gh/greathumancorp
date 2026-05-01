<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$menuTable = 'gh_admin_menu_table';
$tableName = 'gh_admin';
if($w == 'u'){//수정
	$d = $queryLibrary->getData($idx,$tableName);
	$adminAuthor = explode('|',$d['a_authority']);
}else{
	$d = $queryLibrary->getColumn($tableName);
	$adminAuthor = array();
}

if(!$adminSuper && $adminId != $d['a_id']){
	$funcLibrary->alert($_pageText['수정하실 권한이 없습니다.']);
}
?>
<form name="fwrite" method="post" action="./manager_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<input type="hidden" name="id_chk_ok" id="id_chk_ok" value="<?php if($w == 'a'){?>2<?php }?>">
<table width="100%" align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<?php if($adminSuper && !$d['super']){?>
<tr class="ht">
	<td class="td1">타입</td>
	<td class="td2">
		<select name="a_level" class="input_select">
			<?php
				foreach($_adminLevel as $key=>$val){
				if($d['a_level'] == $key){
					$selected = 'selected';
				}else{
					$selected = '';
				}
				if($key == 10){
					continue;
				}
			?>
			<option value="<?=$key?>" <?=$selected?>><?=$val?></option>
			<?php }?>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<?php }?>
<tr class="ht">
	<td class="td1"><?=$_pageText['아이디']?></td>
	<td class="td2">
		<?php if($w == 'a'){?>
			<input type="text" name="a_id" id="a_id" value="<?=$d['a_id']?>" style="width:150px;" onkeyup="id_chk(this.value);" class="input_text"> <span class="id_chk_txt"></span>
		<?php }else{?>
			<?=$d['a_id']?>
		<?php }?>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1"><?=$_pageText['이름']?></td>
	<td class="td2">
		<input type="text" id="a_name" name="a_name" value="<?=$d['a_name']?>" style="width:200px;" class="input_text">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1"><?=$_pageText['비밀번호']?></td>
	<td class="td2">
		<input type="password" id="a_pwd" name="a_pwd" style="width:200px;" class="input_text">
	</td>
</tr>
<!--
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">이메일</td>
	<td class="td2">
		<input type="text" name="a_email" value="<?=$d['a_email']?>" style="width:500px;" class="input_text">
	</td>
</tr>
-->
<?php if($adminSuper){//메인관리자만 권한 설정 가능?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">페이지 접근 권한</td>
		<td class="td2">
			<?php if($d['super'] != '1'){?>
				<?php
					//1차 분류
					$menuWhere = "where m_open = '1' and language = :language and depth = 1";
					$menuBindParam[] = array('language',LANGUAGE);
					$menuOrderby = "num asc|m_code asc|idx desc";
					$menuResult = $queryLibrary->getList($menuWhere,$menuBindParam,$menuTable,$menuOrderby,1,99);
					foreach($menuResult['result'] as $menuData){
					unset($menuBindParam);
				?>
					<strong style="margin;10px"><?=$menuData['m_name']?></strong><br>
					<div>
						<?php
							//2차 분류
							$menuWhere2 = "where m_open = '1' and parent = :parent and substring(m_code,1,3) = :m_code and depth = 2";
							$menuBindParam2[] = array('parent',$menuData['parent']);
							$menuBindParam2[] = array('m_code',$menuData['m_code']);
							$menuOrderby2 = "num asc|m_code asc|idx desc";
							$menuResult2 = $queryLibrary->getList($menuWhere2,$menuBindParam2,$menuTable,$menuOrderby2,1,99);
							foreach($menuResult2['result'] as $menuData2){
							unset($menuBindParam2);
						?>
							<input type="checkbox" name="a_authority[]" value="<?=$menuData2['m_code']?>" <?php if(in_array($menuData2['m_code'],$adminAuthor)){?>checked<?php }?>> <?=$menuData2['m_name']?>
						<?php }//2차분류 끝?>
					</div>
				<?php }//1차분류 끝?>
			<?php }else{//메인관리자?>
				모든 권한
			<?php }?>
		</td>
	</tr>
<?php }?>
<!--
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">이미지</td>
	<td class="td2">
		<input type="file" name='file1' class="input_text"> (111x134)
		<?php if($d['file1']){?>
		<br><img src="../../data/manager/<?=$d['file1']?>" width="100">
		<input type="checkbox" name="delFile1" value="<?=$d['file1']?>"> 삭제
		<?php }?>
	</td>
</tr> 
-->
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn"><?=$_pageText['확 인']?></button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='manager_list.php?<?=$funcLibrary->queryString('idx,w')?>'"><?=$_pageText['취 소']?></button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">

//아이디 및 고유알파벳 중복 체크
function id_chk(str){
	var a_id = $('#a_id').val();

	if($('#a_id').val() == ''){
		return;
	}

	if(a_id.length < 4){
		$('#id_chk_ok').val('2');
		$('.id_chk_txt').html("<font style='color:red'><?=$_pageText['4자리 이상 가능']?></font>");
		$('.id_chk_txt').fadeIn(300);
		return;
	}
	
	$.ajax({
		type : "POST" //"POST", "GET"
		, async : true //true, false
		, url : "id_chk.php" //Request URL
		, dataType : "Json" //전송받을 데이터의 타입
								   //"xml", "html", "script", "json" 등 지정 가능
								   //미지정시 자동 판단
		, timeout : 3000 //제한시간 지정
		, cache : false  //true, false
		, data : "a_id="+a_id //서버에 보낼 파라메터
							 //form에 serialize() 실행시 a=b&c=d 형태로 생성되며 한글은 UTF-8 방식으로 인코딩
							 //"a=b&c=d" 문자열로 직접 입력 가능
							 //{a:b, c:d} json 형식 입력 가능
		, contentType: "application/x-www-form-urlencoded; charset=UTF-8"
		, error : function(request, status, error) {
			//통신 에러 발생시 처리
			console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
		, success : function(response, status, request) {
		 //통신 성공시 처리
			if (response['result']=="N"){
				$("#id_chk_ok").val("2");
				$(".id_chk_txt").html("<font style='color:red'><?=$_pageText['사용불가']?></font>");
				$(".id_chk_txt").fadeIn(300);
				return;
			}else if(response['result']=="Y"){
				$("#id_chk_ok").val("1");
				$(".id_chk_txt").html("<font style='color:blue'><?=$_pageText['사용가능']?></font>");
				$(".id_chk_txt").fadeIn(300);
				return;
			}else if(response['result']=="nodata"){
				return;
			}else if(response['result']=="error"){
				alert("<?=$_pageText['페이지에 문제가 생겼습니다. 다시 시도해 주시기 바랍니다.']?>");
				return;
			}

		}
		, beforeSend: function() {
		 //통신을 시작할때 처리
		
		}
		, complete: function() {
		 //통신이 완료된 후 처리
		
		}
	});

}


function fwrite_submit(f){
	if($('#a_id').val() == ''){
		alert("<?=$_pageText['아이디를 입력해 주세요.']?>");
		$('#a_id').focus();
		return false;
	}

	if($('#id_chk_ok').val() == '2'){
		alert("<?=$_pageText['해당 아이디는 사용하실 수 없습니다.']?>");
		$("#a_id").focus();
		return false;
	}

	if($('#a_name').val() == ''){
		alert("<?=$_pageText['이름을 입력해 주세요.']?>");
		$('#a_name').focus();
		return false;
	}

	<?php if($w == 'a'){?>
		if($('#a_pwd').val() == ''){
			alert("<?=$_pageText['비밀번호를 입력해 주세요.']?>");
			$('#a_pwd').focus();
			return false;
		}
	<?php }?>

	return true;
}
</script>
<?php include_once($ghPath.'/include/html/admin_bottom.php'); ?>