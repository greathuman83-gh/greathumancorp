<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
include_once __DIR__ . '/' . $gh_path . 'include/plugin/editor/smarteditor2/editor.lib.php';

$board_info = $query_library->getBoardInfo($bbsid); //게시판 설정
$file_text = explode('|',$board_info['b_file_text'] ??='');
$thumb_text = $board_info['b_thumb_text'];
$category_table = 'gh_category_table'; //분류

if ($w == 'u'){
	$d = $query_library->getData($idx,"gh_board_$bbsid");
	$content = $d['b_content'];
	$password = $d['b_password'];
	$b_file = explode('|',$d['b_file'] ??= '');
	$b_file_name = explode('|',$d['b_file_name'] ??= '');
}else if($w == 're'){
	$re = $query_library->getData($idx,"gh_board_$bbsid");
	$password = $re['b_password'];
	$d['b_subject'] = $re['b_subject'];
	$content = '';
}else{
	$d = $query_library->getColumn("gh_board_$bbsid");
	$content = '';
	$d['regdate'] = date('Y-m-d H:i:s');
}
?>
<form name="fwrite" method="post" action="./board_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<input type="hidden" name="b_parent" value="<?=$re['b_parent']?>">
<input type="hidden" name="m_id" value="<?=$d['m_id']?>">
<input type="hidden" name="b_name" value="<?=$d['b_name']?>">

<table width="100%" align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<?php if($bbsid == 'org'){?>
<tr class="ht">
	<td class="td1">순서</td>
	<td class="td2">
		<input type="number" class="input_text" name="list_num" value="<?=$d['list_num']?>" style="width:60px;"> (숫자가 높을수록 상위에 노출됩니다.)
	</td>
</tr>
<?php }?>
<?php if($board_info['b_notice']){?>
<tr class="ht">
	<td class="td1">공지사항</td>
	<td class="td2">
		<input type="checkbox" name="b_notice" value="1" <?php if($d['b_notice'] == 1){?>checked<?php }?>> 공지사항
	</td>
</tr>
<?php }?>
<?php if($board_info['b_cate']){?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">분류</td>
	<td class="td2">
		<select name="b_cate" class="input_select">
			<?php
				//1차 분류
				$where = "where depth = 1 and category = :bbsid";
				$bind_param[] = array('bbsid',$bbsid);
				$orderby = "num asc|c_code asc|idx desc";
				$list_result = $query_library->getList($where,$bind_param,$category_table,$orderby,1,99);
				foreach($list_result['result'] as $cate_data){
				unset($bind_param);
			?>
				<option value="<?=$cate_data['c_code']?>" <?php if($cate_data['c_code'] == $d['b_cate']){?>selected<?php }?>><?=$cate_data['c_name']?></option>
				<?php
					//2차 분류
					$where2 = "where parent = :parent and substring(c_code,1,3) = :c_code and depth = 2";
					$bind_param[] = array('parent',$cate_data['parent']);
					$bind_param[] = array('c_code',$cate_data['c_code']);
					$orderby2 = "num asc|c_code asc|idx desc";
					$list_result2 = $query_library->getList($where2,$bind_param,$category_table,$orderby2,1,99);
					foreach($list_result2['result'] as $cate_data2){
					unset($bind_param);
				?>
					<option value="<?=$cate_data2['c_code']?>" <?php if($cate_data2['c_code'] == $d['b_cate']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;2차 : <?=$cate_data2['c_name']?></option>
					<?php
						//3차 분류
						$where3 = "where parent = :parent and substring(c_code,1,6) = :c_code  and depth = 3";
						$bind_param[] = array('parent',$cate_data2['parent']);
						$bind_param[] = array('c_code',$cate_data2['c_code']);
						$orderby3 = "num asc|c_code asc|idx desc";
						$list_result3 = $query_library->getList($where3,$bind_param,$category_table,$orderby3,1,99);
						foreach($list_result3['result'] as $cate_data3){
						unset($bind_param);
					?>
						<option value="<?=$cate_data3['c_code']?>" <?php if($cate_data3['c_code'] == $d['b_cate']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3차 : <?=$cate_data3['c_name']?></option>
					<?php }?>
				<?php }?>
			<?php }?>
		</select>
	</td>
</tr>
<?php }?>
<?php if($bbsid == 'publications'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">카테고리</td>
		<td class="td2">
			<select name="b_data1" class="input_select">
				<?php
					foreach($_publicationsCategory as $key => $val){
					if($key == $d['b_data1']){
						$selected = 'selected';
					}else{
						$selected = '';
					}
				?>
					<option value="<?=$key?>" <?=$selected?>><?=$val?></option>
				<?php }?>
			</select>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">저자</td>
		<td class="td2">
			<textarea class="input_textarea" name="b_data2" style="width:300px;height:50px;"><?=$d['b_data2']?></textarea>
		</td>
	</tr>
<?php }?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">공개설정</td>
	<td class="td2">
		<input type="radio" name="b_open" value="1" <?php if($d['b_open'] == '1' || $d['b_open'] == ''){?>checked<?php }?>> 공개
		<input type="radio" name="b_open" value="2" <?php if($d['b_open'] == '2'){?>checked<?php }?>> 비공개
	</td>
</tr>
<?php if($bbsid == 'recruit'){?>
	<input type="hidden" name="regdate" value="<?=$d['regdate']?>">
<?php }else{?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">등록일</td>
	<td class="td2">
		<input type="text" class="input_text" name="regdate" value="<?=$d['regdate']?>" style="width:150px;">
	</td>
</tr>
<?php }?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">타이틀</td>
	<td class="td2">
		<input type="text" id="b_subject" class="input_text" name="b_subject" value="<?=$d['b_subject']?>" style="width:700px;">
	</td>
</tr>
<?php if($bbsid == 'enotice'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">구분</td>
		<td class="td2">
			<input type="text" class="input_text" name="b_data1" value="<?=$d['b_data1']?>" style="width:200px;">
		</td>
	</tr>
<?php }?>
<?php if($bbsid == 'recruit'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">기간</td>
		<td class="td2">
			<input type="text" class="input_text date" name="b_data1" value="<?=$d['b_data1']?>" style="width:100px;" readonly> ~
			<input type="text" class="input_text date" name="b_data2" value="<?=$d['b_data2']?>" style="width:100px;" readonly>
			<!-- <input type="checkbox" name="b_data4" value="1" <?php if($d['b_data4'] == '1'){?>checked<?php }?>> 상시채용 -->
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">모집구분</td>
		<td class="td2">
			<?php
				foreach($_recruitType as $key => $val){
				if(strpos($d['b_data5'] ?? '',$key) !== false){
					$checked = 'checked';
				}else{
					$checked = '';
				}
			?>
				<input type="checkbox" name="b_data5[]" value="<?=$key?>" <?=$checked?>> <?=$val?>
			<?php }?>
		</td>
	</tr>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">상태</td>
		<td class="td2">
			<select name="b_data3" class="input_select">
				<option value="1" <?php if($d['b_data3'] == '1'){?>selected<?php }?>>채용중</option>
				<option value="2" <?php if($d['b_data3'] == '2'){?>selected<?php }?>>채용마감</option>
			</select>
		</td>
	</tr>
<?php }?>
<?php if($bbsid == 'promotion' || $bbsid == 'seminar' || $bbsid == 'education'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">기간</td>
		<td class="td2">
			<input type="text" class="input_text date" name="b_data1" value="<?=$d['b_data1']?>" style="width:100px;" readonly> ~
			<input type="text" class="input_text date" name="b_data2" value="<?=$d['b_data2']?>" style="width:100px;" readonly>
		</td>
	</tr>
<?php }?>
<?php if($bbsid == 'seminar' || $bbsid == 'education'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">소개</td>
		<td class="td2">
			<input type="text" class="input_text" name="b_data3" value="<?=$d['b_data3']?>" style="width:700px;">
		</td>
	</tr>
<?php }?>
<?php if($board_info['b_secret']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">비밀번호</td>
		<td class="td2">
			<input type="text" id="b_password" class="input_text" name="b_password"  style="width:100px;" maxlength="10">
		</td>
	</tr>
<?php }?>
<?php if($board_info['b_content_type'] != '3'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">내용</td>
		<td class="td2">
			<?php if($board_info['b_content_type'] == '1'){?>
				<?php echo editor_html("b_content",$content);?>
			<?php }else{?>
				<textarea class="input_textarea" name="b_content" style="width:700px;height:200px;"><?=$content?></textarea>
			<?php }?>
		</td>
	</tr>
<?php }?>
<?php if($board_info['b_link']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">링크</td>
		<td class="td2">
			<input type="text" name="link_url" class="input_text" value="<?=$d['link_url']?>" style="width:700px;">
		</td>
	</tr>
<?php }?>
<?php if($board_info['b_type'] == '2'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">썸네일</td>
		<td class="td2">
			<input type="file" class="input_text" name="file_thumb" onchange="imgFileCheck(this,<?=THUMB_SIZE?>)"> <?=$thumb_text?>
			<?php if($d['file_thumb']){?>
				<input type="hidden" name="old_file_thumb" value="<?=$d['file_thumb']?>">
				<br>
				<img src="<?=$gh_path?>data/board/<?=$bbsid?>/<?=$d['file_thumb']?>" width="150" style="margin-top:5px;">
				<input type="checkbox" name="del_file_thumb" value="<?=$d['file_thumb']?>"> 삭제
			<?php }?>
		</td>
	</tr>
<?php }?>
<?php if($w != 're' && $board_info['b_file']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">첨부파일 <?php if($board_info['b_file'] > 1){?><button type="button" class="add_file red_icon_btn">+</button><?php }?></td>
		<td class="td2">
			<?php if($w == 'u'){?>
				<?php if(array_filter($b_file) != [] ){?>
					<?php
						for($i=0;$i<count((array)$b_file);$i++){
						$file_extention = explode('.',$b_file[$i]);
					?>
					<div class="fileList">
							<input type="hidden" name="o_name[]" value="<?=$b_file[$i]?>">
							<input type="hidden" name="o_ori_name[]" value="<?=$b_file_name[$i]?>">
							<input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$file_text[$i] ??= null?>
							<?php if($b_file[$i]){?>
								<?php if(strpos($_config['img_ext'],$file_extention[1]) !== false){?>
									<br><img src="<?=$gh_path?>data/board/<?=$bbsid?>/<?=$b_file[$i]?>" width="200" style="margin-top:5px;">
								<?php }else{?>
									<br><span class="file" style="margin-top:5px;"></span> <a href="<?=$gh_path?>board/download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a>
								<?php }?>
								<input type="checkbox" name="del_file<?=$i?>" value="<?=$b_file[$i]?>"> 삭제
							<?php }?>
					</div>
					<?php }?>
				<?php }else{?>
					<div class="fileList"><input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$file_text[0] ??= null?></div>
				<?php }?>
			<?php }else{?>
				<?php for($i=0;$i<$board_info['b_file'];$i++){?>
					<div class="fileList">
						<input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$file_text[$i] ??= null?>
					</div>
				<?php }?>
			<?php }?>
		</td>
	</tr>
<?php }?>
<tr><td colspan="2" class="line3"></td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<?php if($w == 'u' && $board_info['b_reply'] ??= ''){?>
				<button type="button" onclick="javascript:window.location='./board_form.php?<?=$func_library->queryString('idx,depth,w')?>depth=<?=$d['depth']?>&idx=<?=$d['idx']?>&w=re'" class="red_btn">답변</button>
			<?php }?>
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='./board_list.php?<?=$func_library->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
	// 첨부파일 행 추가 — 게시판 b_file 개수 제한
	var addFileBtn = document.querySelector('.add_file');
	if (addFileBtn) {
		addFileBtn.addEventListener('click', function(){
			var count = <?= (int)($board_info['b_file'] ?? 0) ?>;
			var size = [];
			<?php for($i=0;$i<count($file_text);$i++){?>
			size[<?=$i?>] = <?= json_encode((string)($file_text[$i] ?? ''), JSON_UNESCAPED_UNICODE) ?>;
			<?php }?>

			var fileLists = document.querySelectorAll('.fileList');
			if (fileLists.length >= count) {
				alert('첨부 파일은  '+count+'개까지 등록하실 수 있습니다.');
				return;
			}
			var sizeTxt = size[fileLists.length] || '';
			var data = '<div class="fileList"><input type="file" class="input_text" name="b_file[]" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> '+ sizeTxt+' <button type="button" class="del_file red_icon_btn">-</button></div>';
			fileLists[fileLists.length - 1].insertAdjacentHTML('afterend', data);
		});
	}

	document.addEventListener('click', function(e){
		var delBtn = e.target.closest('.del_file');
		if (!delBtn) return;
		if (document.querySelectorAll('.fileList').length < 2) {
			alert('더이상 삭제하실 수 없습니다.');
			return;
		}
		if (window.confirm('삭제하시겠습니까?')) {
			var row = delBtn.closest('.fileList');
			if (row) row.remove();
		}
	});
});

function fwrite_submit(f)
{
	var subjectEl = document.getElementById('b_subject');
	if (!subjectEl || subjectEl.value === '') {
		alert('제목을 입력해 주세요.');
		return false;
	}
	<?php if($board_info['b_content_type'] == '1'){?>
	<?php echo get_editor_js('b_content'); ?>
	<?php }?>
	return true;
}
</script>
<!---- 댓글 시작 ---->
<?php if($w == 'u' && $board_info['b_comment']){?>
<div class="m74 verification_g view">
	<div class="section1">
		<div class="conwrap">
			<?php
				//댓글 시작
				$comment_table_name = 'gh_comment_table';
				//댓글 총 갯수
				$bind_param = array();
				$where = " where b_idx = :idx ";
				$bind_param[] = array('idx', $idx);
				$total = $query_library->getList($where,$bind_param,$comment_table_name,'',1,1);

				$bind_param = array();
				$where = " where b_idx = :idx and depth = '1' ";
				$orderby = "parent asc|idx asc";
				$bind_param[] = array('idx', $idx);
				$list_result = $query_library->getList($where,$bind_param,$comment_table_name,$orderby,1,100);
			?>
			<div class="cmt_g">
				<div class="cmt_count">댓글 <em><?=$total['list_total']?></em></div>
				<ul class="cmt_list" id="commentList">
					<?php
						//1뎁스 댓글
						$num = 1;

						foreach($list_result['result'] as $cd){
						$bind_param2 = array();
						$where2 = " where parent = :parent and depth = '2' ";
						$bind_param2[] = array('parent', $cd['idx']);
						$orderby2 = "parent desc|idx asc";
						$list_result2 = $query_library->getList($where2,$bind_param2,$comment_table_name,$orderby2,1,100);
					?>
					<li <?php if($list_result2['list_total'] == 0){?>class="null"<?php }?>>
						<div class="origin_box">
							<form name="editForm<?=$num?>" method="post" action="./comment_ok.php?">
							<input type="hidden" name="w" value="u">
							<input type="hidden" name="b_idx" value="<?=$idx?>">
							<input type="hidden" name="idx" value="<?=$cd['idx']?>">
							<div class="reply_contents">
								<strong class="name"><?php if($cd['c_id']){?>관리자<?php }else{?>제보자<?php }?></strong>
								<div class="text"><p><?=nl2br($cd['content'])?></p></div>
							</div>
							<div class="cmt_util">
								<button type="button" class="util_btn" id="cmt_edit">수정</button>
								<?php if($list_result2['list_total'] == 0){?>
									<button type="button" class="util_btn" id="cmt_delete" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='comment_ok.php?b_idx=<?=$idx?>&w=d&idx=<?=$cd['idx']?>';">삭제</button>
								<?php }else{?>
									<button type="button" class="util_btn" id="cmt_delete" onclick="javascript:alert('댓글에 다른 댓글이 달려있어 삭제하실 수 없습니다.')">삭제</button>
								<?php }?>
							</div> <!-- util -->
							<div class="cmt_util_hidden">
								<button type="submit" class="util_btn" id="cmt_confirm">확인</button>
								<button type="button" class="util_btn" id="cmt_cancel">취소</button>
							</div> <!-- util -->
							</form>
							<button type="button" class="reply_btn">답글</button>
						</div>
						<div class="reply_list">
							<?php
								//2뎁스 댓글
								$num2 = 1;
								foreach($list_result['result'] as $cd2){
							?>
							<form name="replyEditForm<?=$num2?>" method="post" action="./comment_ok.php?">
							<input type="hidden" name="w" value="u">
							<input type="hidden" name="b_idx" value="<?=$idx?>">
							<input type="hidden" name="idx" value="<?=$cd2['idx']?>">
							<div class="reply_box">
								<div class="reply_contents">
									<strong class="name"><?php if($cd2['c_id']){?>관리자<?php }else{?>제보자<?php }?></strong>
									<div class="text"><p><?=nl2br($cd2['content'])?></p></div>
								</div>
								<div class="cmt_util">
									<button type="button" class="util_btn" id="cmt_edit">수정</button>
									<button type="button" class="util_btn" id="cmt_delete" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='comment_ok.php?b_idx=<?=$idx?>&w=d&idx=<?=$cd2['idx']?>';">삭제</button>
								</div> <!-- util -->
								<div class="cmt_util_hidden">
									<button type="submit" class="util_btn" id="cmt_confirm">확인</button>
									<button type="button" class="util_btn" id="cmt_cancel">취소</button>
								</div> <!-- util -->
							</div>
							</form>
							<?php $num2++;}?>
							<form name="replyForm<?=$num?>" method="post" action="./comment_ok.php?">
							<input type="hidden" name="w" value="re">
							<input type="hidden" name="b_idx" value="<?=$idx?>">
							<input type="hidden" name="p_idx" value="<?=$cd['idx']?>">
							<div class="reply_comment_box">
								<div class="reply_contents">
									<strong class="name">관리자</strong>
									<div class="reply_textarea_g">
										<textarea name="content" placeholder="댓글을 입력하여주세요" required="required"></textarea>
										<button type="submit" class="submit-btn">입력</button>
									</div> <!-- text -->
								</div>
							</div>
							</form>
						</div> <!-- list -->
					</li>
					<?php $num++;}?>
				</ul>
			</div>
			<form name="comment_form" method="post" action="./comment_ok.php?">
			<input type="hidden" name="w" value="a">
			<input type="hidden" name="b_idx" value="<?=$idx?>">
			<div class="reply_textarea_g">
				<textarea name="content" placeholder="댓글을 입력하여주세요" required="required"></textarea>
				<button type="submit" class="submit-btn">입력</button>
			</div> <!-- text -->
			</form>
		</div>
	</div> <!-- sec1 -->
</div> <!-- m74 -->

<script>
document.addEventListener('DOMContentLoaded', function(){
	// 답글 토글 — null/is-open 클래스와 reply_comment_box 표시 연동
	document.addEventListener('click', function(e){
		var replyBtn = e.target.closest('.reply_btn');
		if (replyBtn) {
			var li = replyBtn.closest('li');
			if (!li) return;
			if (li.classList.contains('null')) {
				li.classList.remove('null');
			}
			if (li.classList.contains('is-open')) {
				if (li.querySelectorAll('.reply_box').length === 0) {
					li.classList.add('null');
				}
			}
			li.classList.toggle('is-open');
			var replyBox = li.querySelector('.reply_comment_box');
			if (replyBox) {
				var isHidden = window.getComputedStyle(replyBox).display === 'none';
				replyBox.style.display = isHidden ? 'block' : 'none';
			}
			return;
		}

		// 댓글 수정 — reply_contents 내 textarea 토글, 유틸 버튼 전환
		var editBtn = e.target.closest('button#cmt_edit');
		if (editBtn) {
			var util = editBtn.parentElement;
			var contents = util ? util.previousElementSibling : null;
			if (!contents) return;
			var textEl = contents.querySelector('.text');
			var pEl = contents.querySelector('p');
			var text = pEl ? pEl.textContent : '';
			if (contents.classList.contains('is-open')) {
				var ta = contents.querySelector('textarea');
				var editText = ta ? ta.value : '';
				if (textEl) textEl.innerHTML = '<p>' + editText + '</p>';
			} else if (textEl) {
				textEl.innerHTML = '<textarea class="reply_textarea" name="content" required="required">' + text + '</textarea>';
			}
			contents.classList.toggle('is-open');
			util.style.display = 'none';
			var hiddenUtil = util.parentElement ? util.parentElement.querySelector('.cmt_util_hidden') : null;
			if (hiddenUtil) hiddenUtil.style.display = '';
			return;
		}

		// 댓글 수정 취소 — 원문 p로 복원
		var cancelBtn = e.target.closest('button#cmt_cancel');
		if (cancelBtn) {
			var cancelUtil = cancelBtn.parentElement;
			if (!cancelUtil) return;
			cancelUtil.style.display = 'none';
			var showUtil = cancelUtil.parentElement ? cancelUtil.parentElement.querySelector('.cmt_util') : null;
			if (showUtil) showUtil.style.display = '';
			document.querySelectorAll('.reply_contents.is-open').forEach(function(el){
				el.classList.remove('is-open');
			});
			var replyContents = cancelUtil.previousElementSibling
				? cancelUtil.previousElementSibling.previousElementSibling
				: null;
			if (replyContents) {
				var cancelTa = replyContents.querySelector('textarea');
				var cancelText = cancelTa ? cancelTa.textContent : '';
				var cancelTextWrap = replyContents.querySelector('.text');
				if (cancelTextWrap) cancelTextWrap.innerHTML = '<p>' + cancelText + '</p>';
			}
		}
	});
});
</script>
<?php }?>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>