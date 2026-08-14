<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$menu_table = 'gh_admin_menu_table';
$table_name = 'gh_admin';
if($w == 'u'){//수정
	$d = $query_library->getData($idx,$table_name);
	$admin_author = explode('|',$d['a_authority']);
}else{
	$d = $query_library->getColumn($table_name);
	$admin_author = array();
}

if(!$admin_super && $admin_id != $d['a_id']){
	$func_library->alert($_pageText['수정하실 권한이 없습니다.']);
}
?>
<form name="fwrite" method="post" action="./manager_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<input type="hidden" name="id_chk_ok" id="id_chk_ok" value="<?php if($w == 'a'){?>2<?php }?>">
<table width="100%" align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<?php if($admin_super && !$d['super']){?>
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
<?php if($admin_super){?>
<tr class="ht">
	<td class="td1"><?=$_pageText['상태']?></td>
	<td class="td2">
		<select name="a_status" class="input_select">
			<option value="Y" <?php if(($d['a_status'] ?? 'Y') === 'Y'){?>selected<?php }?>><?=$_pageText['승인완료']?></option>
			<option value="N" <?php if(($d['a_status'] ?? 'Y') === 'N'){?>selected<?php }?>><?=$_pageText['승인대기']?></option>
		</select>
		<?php if(($d['a_kakao_id'] ?? '') !== ''){?>
			<span style="margin-left:10px;"><?=$_pageText['카카오 연동']?></span>
		<?php }?>
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
<?php if($admin_super){//메인관리자만 권한 설정 가능?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">페이지 접근 권한</td>
		<td class="td2">
			<?php if($d['super'] != '1'){?>
				<?php
					//1차 분류
					$menu_where = "where m_open = '1' and language = :language and depth = 1";
					$menu_bind_param[] = array('language',LANGUAGE);
					$menu_orderby = "num asc|m_code asc|idx desc";
					$menu_result = $query_library->getList($menu_where,$menu_bind_param,$menu_table,$menu_orderby,1,99);
					foreach($menu_result['result'] as $menu_data){
					unset($menu_bind_param);
				?>
					<strong style="margin;10px"><?=$menu_data['m_name']?></strong><br>
					<div>
						<?php
							//2차 분류
							$menu_where2 = "where m_open = '1' and parent = :parent and substring(m_code,1,3) = :m_code and depth = 2";
							$menu_bind_param2[] = array('parent',$menu_data['parent']);
							$menu_bind_param2[] = array('m_code',$menu_data['m_code']);
							$menu_orderby2 = "num asc|m_code asc|idx desc";
							$menu_result2 = $query_library->getList($menu_where2,$menu_bind_param2,$menu_table,$menu_orderby2,1,99);
							foreach($menu_result2['result'] as $menu_data2){
							unset($menu_bind_param2);
						?>
							<input type="checkbox" name="a_authority[]" value="<?=$menu_data2['m_code']?>" <?php if(in_array($menu_data2['m_code'],$admin_author)){?>checked<?php }?>> <?=$menu_data2['m_name']?>
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
		<input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
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
			<button type="button" class="gray_btn" onclick="javascript:window.location='manager_list.php?<?=$func_library->queryString('idx,w')?>'"><?=$_pageText['취 소']?></button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
// 아이디 중복 안내 — fadeIn(300) 대체
function fadeInEl(el, duration) {
	el.style.opacity = '0';
	el.style.display = '';
	var start = null;
	function step(timestamp) {
		if (!start) start = timestamp;
		var progress = (timestamp - start) / duration;
		el.style.opacity = String(Math.min(progress, 1));
		if (progress < 1) {
			requestAnimationFrame(step);
		}
	}
	requestAnimationFrame(step);
}

// 아이디 중복 체크 — id_chk.php POST, 4자 미만·사용가능/불가 표시
function id_chk(str){
	var aIdEl = document.getElementById('a_id');
	var idChkOk = document.getElementById('id_chk_ok');
	var idChkTxt = document.querySelector('.id_chk_txt');
	var aId = aIdEl.value;

	if (aId == '') {
		return;
	}

	if (aId.length < 4) {
		idChkOk.value = '2';
		idChkTxt.innerHTML = "<font style='color:red'><?=$_pageText['4자리 이상 가능']?></font>";
		fadeInEl(idChkTxt, 300);
		return;
	}

	var controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
	var timeoutId = null;
	if (controller) {
		timeoutId = setTimeout(function () {
			controller.abort();
		}, 3000);
	}

	fetch('id_chk.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
		},
		body: 'a_id=' + encodeURIComponent(aId),
		cache: 'no-store',
		signal: controller ? controller.signal : undefined
	})
	.then(function (response) {
		if (!response.ok) {
			throw new Error('code:' + response.status);
		}
		return response.json();
	})
	.then(function (response) {
		if (response['result'] == 'N') {
			idChkOk.value = '2';
			idChkTxt.innerHTML = "<font style='color:red'><?=$_pageText['사용불가']?></font>";
			fadeInEl(idChkTxt, 300);
			return;
		} else if (response['result'] == 'Y') {
			idChkOk.value = '1';
			idChkTxt.innerHTML = "<font style='color:blue'><?=$_pageText['사용가능']?></font>";
			fadeInEl(idChkTxt, 300);
			return;
		} else if (response['result'] == 'nodata') {
			return;
		} else if (response['result'] == 'error') {
			alert("<?=$_pageText['페이지에 문제가 생겼습니다. 다시 시도해 주시기 바랍니다.']?>");
			return;
		}
	})
	.catch(function (error) {
		console.log(error);
	})
	.finally(function () {
		if (timeoutId) {
			clearTimeout(timeoutId);
		}
	});
}

// 관리자 폼 검증 — 아이디·중복·이름·등록 시 비밀번호
function fwrite_submit(f){
	var aIdEl = document.getElementById('a_id');
	var idChkOk = document.getElementById('id_chk_ok');
	var aNameEl = document.getElementById('a_name');
	var aPwdEl = document.getElementById('a_pwd');

	if (aIdEl && aIdEl.value == '') {
		alert("<?=$_pageText['아이디를 입력해 주세요.']?>");
		aIdEl.focus();
		return false;
	}

	if (idChkOk.value == '2') {
		alert("<?=$_pageText['해당 아이디는 사용하실 수 없습니다.']?>");
		if (aIdEl) {
			aIdEl.focus();
		}
		return false;
	}

	if (aNameEl.value == '') {
		alert("<?=$_pageText['이름을 입력해 주세요.']?>");
		aNameEl.focus();
		return false;
	}

	<?php if($w == 'a'){?>
		if (aPwdEl.value == '') {
			alert("<?=$_pageText['비밀번호를 입력해 주세요.']?>");
			aPwdEl.focus();
			return false;
		}
	<?php }?>

	return true;
}
</script>
<?php include_once __DIR__ . '/' . $gh_path . '/include/html/admin_bottom.php'; ?>