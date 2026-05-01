<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');
include_once($ghPath.'include/plugin/editor/smarteditor2/editor.lib.php');

$boardInfo = $queryLibrary->getBoardInfo($bbsid); //게시판 설정
$fileText = explode('|',$boardInfo['b_file_text'] ??='');
$thumbText = $boardInfo['b_thumb_text'];
$categoryTable = 'gh_category_table'; //분류

if ($w == 'u'){
	$d = $queryLibrary->getData($idx,"gh_board_$bbsid");
	$content = $d['b_content'];
	$password = $d['b_password'];
	$b_file = explode('|',$d['b_file'] ??= '');
	$b_file_name = explode('|',$d['b_file_name'] ??= '');
}else if($w == 're'){
	$re = $queryLibrary->getData($idx,"gh_board_$bbsid");
	$password = $re['b_password'];
	$d['b_subject'] = $re['b_subject'];
	$content = '';
}else{
	$d = $queryLibrary->getColumn("gh_board_$bbsid");
	$content = '';
	$d['regdate'] = date('Y-m-d H:i:s');
}
?>
<form name="fwrite" method="post" action="./board_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
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
<?php if($boardInfo['b_notice']){?>
<tr class="ht">
	<td class="td1">공지사항</td>
	<td class="td2">
		<input type="checkbox" name="b_notice" value="1" <?php if($d['b_notice'] == 1){?>checked<?php }?>> 공지사항
	</td>
</tr>
<?php }?>
<?php if($boardInfo['b_cate']){?>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">분류</td>
	<td class="td2">
		<select name="b_cate" class="input_select">
			<?php
				//1차 분류
				$where = "where depth = 1 and category = :bbsid";
				$bindParam[] = array('bbsid',$bbsid);
				$orderby = "num asc|c_code asc|idx desc";
				$listResult = $queryLibrary->getList($where,$bindParam,$categoryTable,$orderby,1,99);
				foreach($listResult['result'] as $cateData){
				unset($bindParam);
			?>
				<option value="<?=$cateData['c_code']?>" <?php if($cateData['c_code'] == $d['b_cate']){?>selected<?php }?>><?=$cateData['c_name']?></option>
				<?php
					//2차 분류
					$where2 = "where parent = :parent and substring(c_code,1,3) = :c_code and depth = 2";
					$bindParam[] = array('parent',$cateData['parent']);
					$bindParam[] = array('c_code',$cateData['c_code']);
					$orderby2 = "num asc|c_code asc|idx desc";
					$listResult2 = $queryLibrary->getList($where2,$bindParam,$categoryTable,$orderby2,1,99);
					foreach($listResult2['result'] as $cateData2){
					unset($bindParam);
				?>
					<option value="<?=$cateData2['c_code']?>" <?php if($cateData2['c_code'] == $d['b_cate']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;2차 : <?=$cateData2['c_name']?></option>
					<?php
						//3차 분류
						$where3 = "where parent = :parent and substring(c_code,1,6) = :c_code  and depth = 3";
						$bindParam[] = array('parent',$cateData2['parent']);
						$bindParam[] = array('c_code',$cateData2['c_code']);
						$orderby3 = "num asc|c_code asc|idx desc";
						$listResult3 = $queryLibrary->getList($where3,$bindParam,$categoryTable,$orderby3,1,99);
						foreach($listResult3['result'] as $cateData3){
						unset($bindParam);
					?>
						<option value="<?=$cateData3['c_code']?>" <?php if($cateData3['c_code'] == $d['b_cate']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3차 : <?=$cateData3['c_name']?></option>
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
<?php if($boardInfo['b_secret']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">비밀번호</td>
		<td class="td2">
			<input type="text" id="b_password" class="input_text" name="b_password"  style="width:100px;" maxlength="10">
		</td>
	</tr>
<?php }?>
<?php if($boardInfo['b_content_type'] != '3'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">내용</td>
		<td class="td2">
			<?php if($boardInfo['b_content_type'] == '1'){?>
				<?php echo editor_html("b_content",$content);?>
			<?php }else{?>
				<textarea class="input_textarea" name="b_content" style="width:700px;height:200px;"><?=$content?></textarea>
			<?php }?>
		</td>
	</tr>
<?php }?>
<?php if($boardInfo['b_link']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">링크</td>
		<td class="td2">
			<input type="text" name="link_url" class="input_text" value="<?=$d['link_url']?>" style="width:700px;">
		</td>
	</tr>
<?php }?>
<?php if($boardInfo['b_type'] == '2'){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">썸네일</td>
		<td class="td2">
			<input type="file" class="input_text" name="file_thumb" onchange="imgFileCheck(this,<?=THUMB_SIZE?>)"> <?=$thumbText?>
			<?php if($d['file_thumb']){?>
				<input type="hidden" name="old_file_thumb" value="<?=$d['file_thumb']?>">
				<br>
				<img src="<?=$ghPath?>data/board/<?=$bbsid?>/<?=$d['file_thumb']?>" width="150" style="margin-top:5px;">
				<input type="checkbox" name="del_file_thumb" value="<?=$d['file_thumb']?>"> 삭제
			<?php }?>
		</td>
	</tr>
<?php }?>
<?php if($w != 're' && $boardInfo['b_file']){?>
	<tr><td colspan="2" class="line2"></td></tr>
	<tr class="ht">
		<td class="td1">첨부파일 <?php if($boardInfo['b_file'] > 1){?><button type="button" class="add_file red_icon_btn">+</button><?php }?></td>
		<td class="td2">
			<?php if($w == 'u'){?>
				<?php if(array_filter($b_file) != [] ){?>
					<?php
						for($i=0;$i<count((array)$b_file);$i++){
						$fileExtention = explode('.',$b_file[$i]);
					?>
					<div class="fileList">
							<input type="hidden" name="o_name[]" value="<?=$b_file[$i]?>">
							<input type="hidden" name="o_ori_name[]" value="<?=$b_file_name[$i]?>">
							<input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$fileText[$i] ??= null?>
							<?php if($b_file[$i]){?>
								<?php if(strpos($_config['imgExt'],$fileExtention[1]) !== false){?>
									<br><img src="<?=$ghPath?>data/board/<?=$bbsid?>/<?=$b_file[$i]?>" width="200" style="margin-top:5px;">
								<?php }else{?>
									<br><span class="file" style="margin-top:5px;"></span> <a href="<?=$ghPath?>board/download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a>
								<?php }?>
								<input type="checkbox" name="del_file<?=$i?>" value="<?=$b_file[$i]?>"> 삭제
							<?php }?>
					</div>
					<?php }?>
				<?php }else{?>
					<div class="fileList"><input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$fileText[0] ??= null?></div>
				<?php }?>
			<?php }else{?>
				<?php for($i=0;$i<$boardInfo['b_file'];$i++){?>
					<div class="fileList">
						<input type="file" class="input_text" name="b_file[]" class="b_file" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> <?=$fileText[$i] ??= null?>
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
			<?php if($w == 'u' && $boardInfo['b_reply'] ??= ''){?>
				<button type="button" onclick="javascript:window.location='./board_form.php?<?=$funcLibrary->queryString('idx,depth,w')?>depth=<?=$d['depth']?>&idx=<?=$d['idx']?>&w=re'" class="red_btn">답변</button>
			<?php }?>
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='./board_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
$(function(){
	//================== 파일 등록 폼 추가 =================
	$('.add_file').on('click',function(){
		var count = '<?=$boardInfo['b_file']?>';
		var size = new Array();
		var list_num = $('.fileList').length;

		<?php for($i=0;$i<count($fileText);$i++){?>
			size[<?=$i?>] = '<?=$fileText[$i]?>';
		<?php }?>

		if($('.fileList').length >= count){
			alert('첨부 파일은  '+count+'개까지 등록하실 수 있습니다.');
			return;
		}
		var data;
		
		if(!size[$('.fileList').length]){
			var size_txt = '';
		}else{
			var size_txt = size[$('.fileList').length];
		}
		
		data = '<div class="fileList"><input type="file" class="input_text" name="b_file[]" onchange="attachFileCheck(this,<?=FILE_SIZE?>)"> '+ size_txt+' <button type="button" class="del_file red_icon_btn">-</button></div>';
		$(".fileList").last().after(data);
	});


	$(document).on('click','.del_file',function(){
		if($('.fileList').length < 2){
			alert('더이상 삭제하실 수 없습니다.');
			return;
		}

		if (window.confirm('삭제하시겠습니까?')){
			$(this).parent('.fileList').remove();
		}
	});
	//================== 파일 등록 폼 추가 끝 ================
});

function fwrite_submit(f)
{
	if($('#b_subject').val() == ''){
		alert('제목을 입력해 주세요.');
		return false;
	}
	<?php if($boardInfo['b_content_type'] == '1'){?>
	<?php echo get_editor_js('b_content'); ?>
	<?php }?>
	return true;
}
</script>
<!---- 댓글 시작 ---->
<?php if($w == 'u' && $boardInfo['b_comment']){?>
<div class="m74 verification_g view">
	<div class="section1">
		<div class="conwrap">
			<?php
				//댓글 시작
				$commentTableName = 'gh_comment_table';
				//댓글 총 갯수
				$bindParam = array();
				$where = " where b_idx = :idx ";
				$bindParam[] = array('idx', $idx);
				$total = $queryLibrary->getList($where,$bindParam,$commentTableName,'',1,1);

				$bindParam = array();
				$where = " where b_idx = :idx and depth = '1' ";
				$orderby = "parent asc|idx asc";
				$bindParam[] = array('idx', $idx);
				$listResult = $queryLibrary->getList($where,$bindParam,$commentTableName,$orderby,1,100);
			?>
			<div class="cmt_g">
				<div class="cmt_count">댓글 <em><?=$total['listTotal']?></em></div>
				<ul class="cmt_list" id="commentList">
					<?php
						//1뎁스 댓글
						$num = 1;

						foreach($listResult['result'] as $cd){
						$bindParam2 = array();
						$where2 = " where parent = :parent and depth = '2' ";
						$bindParam2[] = array('parent', $cd['idx']);
						$orderby2 = "parent desc|idx asc";
						$listResult2 = $queryLibrary->getList($where2,$bindParam2,$commentTableName,$orderby2,1,100);
					?>
					<li <?php if($listResult2['listTotal'] == 0){?>class="null"<?php }?>>
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
								<?php if($listResult2['listTotal'] == 0){?>
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
								foreach($listResult['result'] as $cd2){
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
			<form name="commentForm" method="post" action="./comment_ok.php?">
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
$(function(){
	//답글
	$(document).on('click', '.reply_btn', function(){

		//초기값 null 제거
		if($(this).parents('li').hasClass('null')) {
			$(this).parents('li').removeClass('null');
		}
		//버튼 닫을때
		if($(this).parents('li').hasClass('is-open')) {
			var cmtLength = $(this).parents('li').find('.reply_box').length;
			//댓글이 0개일때 class 추가
			if(cmtLength == 0){
				$(this).parents('li').addClass('null');
			}
		}

		$(this).parents('li').toggleClass('is-open');
		$(this).parents('li').find('.reply_comment_box').stop(true).toggle();
	});

	//수정
	$(document).on('click', '#cmt_edit', function(){
		var text = $(this).parent().prev().find('p').text();
		
		//수정 적용
		if($(this).parent().prev().hasClass('is-open')) {
			var editText = $(this).parent().prev().find('textarea').val();
			$(this).parent().prev().find('.text').html('<p>'+editText+'</p>');
		} else {
			//수정 할때
			$(this).parent().prev().find('.text').html('<textarea class="reply_textarea" name="content" required="required">'+text+'</textarea>');
		}
		$(this).parent().prev().toggleClass('is-open');
		
		//버튼 체인지
		$(this).parent().hide();
		$(this).parent().siblings(".cmt_util_hidden").show();
	});
	//취소
	$(document).on('click', '#cmt_cancel', function(){
		$(this).parent().hide();
		$(this).parent().siblings(".cmt_util").show();
		$(".reply_contents").removeClass("is-open");
		var text = $(this).parent().prev().prev().find('textarea').text();
		$(this).parent().prev().prev().find('.text').html('<p>'+text+'</p>');
	});

	//삭제
	/*
	$(document).on('click', '#cmt_delete', function(){
		var li = $(this).parents('li');
		var Length;

		$(this).parent().parent().remove();
		Length = li.find('.reply_box').length;
		
		//댓글이 0개일때 class 추가
		if(Length == 0) {
			li.addClass('null');
		}
	});
	*/
});
</script>
<?php }?>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>