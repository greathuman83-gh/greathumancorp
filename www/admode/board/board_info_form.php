<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_board';

if($w == 'u'){
	$d = $query_library->getData($idx,$table_name);
}else{
	$d = $query_library->getColumn($table_name);
}
?>
<form name="fwrite" method="post" action="./board_info_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">게시판이름</td>
	<td class="td2">
		<input type="text" name="b_name" id="boardName" class="input_text" style="width:150px;" value="<?=$d['b_name']?>" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">게시판아이디</td>
	<td class="td2">
		<input type="text" name="board_id" class="input_text" style="width:150px;" value="<?=$d['bbsid']?>" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">스킨명</td>
	<td class="td2">
		/board/skin/<input type="text" name="b_skin" class="input_text" style="width:150px;" value="<?=$d['b_skin']?>" required="required">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">게시판타입</td>
	<td class="td2">
		<select name="b_type" class="input_select">
			<option value="1" <?php if($d['b_type'] == '1'){?>selected<?php }?>>일반</option>
			<option value="2" <?php if($d['b_type'] == '2'){?>selected<?php }?>>갤러리(썸네일)</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">썸네일 텍스트(사이즈)</td>
	<td class="td2">
		<input type="text" name="b_thumb_text" class="input_text" style="width:250px;" value="<?=$d['b_thumb_text']?>" >
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">분류 사용여부</td>
	<td class="td2">
		<select name="b_cate" class="input_select">
			<option value="" <?php if($d['b_cate'] == ''){?>selected<?php }?>>미사용</option>
			<option value="1" <?php if($d['b_cate'] == '1'){?>selected<?php }?>>사용</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">링크 사용여부</td>
	<td class="td2">
		<select name="b_link" class="input_select">
			<option value="" <?php if($d['b_link'] == ''){?>selected<?php }?>>미사용</option>
			<option value="1" <?php if($d['b_link'] == '1'){?>selected<?php }?>>사용</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">댓글 사용여부</td>
	<td class="td2">
		<select name="b_comment" class="input_select">
			<option value="" <?php if($d['b_comment'] == ''){?>selected<?php }?>>미사용</option>
			<option value="1" <?php if($d['b_comment'] == '1'){?>selected<?php }?>>사용</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">공지 사용여부</td>
	<td class="td2">
		<select name="b_notice" class="input_select">
			<option value="" <?php if($d['b_notice'] == ''){?>selected<?php }?>>미사용</option>
			<option value="1" <?php if($d['b_notice'] == '1'){?>selected<?php }?>>사용</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">비밀글 사용여부</td>
	<td class="td2">
		<select name="b_secret" class="input_select">
			<option value="" <?php if($d['b_secret'] == ''){?>selected<?php }?>>미사용</option>
			<option value="1" <?php if($d['b_secret'] == '1'){?>selected<?php }?>>사용</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">게시판 형태</td>
	<td class="td2">
		<select name="b_reply" class="input_select">
			<option value="" <?php if($d['b_reply'] == ''){?>selected<?php }?>>일반</option>
			<option value="1" <?php if($d['b_reply'] == '1'){?>selected<?php }?>>계층형</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">상세내용타입</td>
	<td class="td2">
		<select name="b_content_type" class="input_select">
			<option value="1" <?php if($d['b_content_type'] == '1'){?>selected<?php }?>>EDITOR</option>
			<option value="2" <?php if($d['b_content_type'] == '2'){?>selected<?php }?>>일반</option>
			<option value="3" <?php if($d['b_content_type'] == '3'){?>selected<?php }?>>사용안함</option>
		</select>
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">접근허용권한</td>
	<td class="td2">
		<input type="number" name="b_level" class="input_text" style="width:70px;" max="100" value="<?=$d['b_level']?>">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">쓰기 권한</td>
	<td class="td2">
		<input type="number" name="b_write" class="input_text" style="width:70px;" max="100" value="<?=$d['b_write']?>">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">읽기 권한</td>
	<td class="td2">
		<input type="number" name="b_read" class="input_text" style="width:70px;" max="100" value="<?=$d['b_read']?>">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">첨부파일 갯수</td>
	<td class="td2">
		<input type="number" name="b_file" class="input_text" style="width:70px;" max="10" value="<?=$d['b_file']?>">
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">첨부파일텍스트</td>
	<td class="td2">
		<input type="text" name="b_file_text" class="input_text" style="width:700px;" value="<?=$d['b_file_text']?>">
	</td>
</tr>
<tr><td colspan="2" class="line3"></td></tr>
</table>
<p align="center" style="margin-top:30px;">
	<button type="submit" class="red_btn">확 인</button>
	<button type="button" class="gray_btn" onclick="javascript:window.location='./board_info_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
</p>
</form>

<script type="text/javascript">
function fwrite_submit(f)
{
	var boardNameEl = document.getElementById('boardName');
	if (!boardNameEl || boardNameEl.value === '') {
		alert("게시판 이름을 입력해 주세요.");
		return false;
	}

	return true;
}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>