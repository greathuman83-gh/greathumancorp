<?php
$ghPath = '../../';
include_once($ghPath . 'include/html/admin_top.php');

$tableName = 'gh_worker_table';
$categoryTable = 'gh_category_table';

if ($w == 'u') {
	$d = $queryLibrary->getData($idx, $tableName);
	$attachFiles = explode('|', $d['attach_files'] ?? '');
	$attachFilesName = explode('|', $d['attach_files_name'] ?? '');
} else {
	$d = $queryLibrary->getColumn($tableName);
	$attachFiles = array();
	$attachFilesName = array();
}

$sizeDetail = '첨부파일';
?>
<form name="fwrite" method="post" action="./worker_ok.php?<?= $funcLibrary->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">고용형태</td>
			<td class="td2">
				<select name="w_type" class="input_select">
					<?php
					foreach ($_workerType as $key => $value) {
						$selected = $d['w_type'] == $key ? 'selected' : '';
					?>
						<option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">이름</td>
			<td class="td2">
				<input type="text" name="w_name" class="input_text" value="<?= htmlspecialchars((string)($d['w_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:700px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">입사일</td>
			<td class="td2">
				<input type="text" name="w_enterdate" class="input_text date" value="<?= htmlspecialchars((string)($d['w_enterdate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">퇴사일</td>
			<td class="td2">
				<input type="text" name="w_leavedate" class="input_text date" value="<?= htmlspecialchars((string)($d['w_leavedate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">은행명</td>
			<td class="td2">
				<input type="text" name="w_bankname" class="input_text" value="<?= htmlspecialchars((string)($d['w_bankname'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:300px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">계좌번호</td>
			<td class="td2">
				<input type="number" name="w_banknumber" class="input_text" value="<?= htmlspecialchars((string)($d['w_banknumber'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:300px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">비고</td>
			<td class="td2">
				<textarea name="content" class="input_textarea" style="width:700px;height:100px;"><?= htmlspecialchars((string)($d['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">첨부파일일 <button type="button" class="add_file red_icon_btn">+</button></td>
			<td class="td2">
				<?php if (array_filter($attachFiles) != []) { ?>
					<?php
					for ($i = 0; $i < count((array)$attachFiles); $i++) {
						$fileExtention = explode('.', $attachFiles[$i]);
					?>
						<div class="fileList">
							<input type="hidden" name="oldFile[]" value="<?= $attachFiles[$i] ?>">
							<input type="hidden" name="oldFileName[]" value="<?= $attachFilesName[$i] ?>">
							<input type="file" class="input_text" name="attachFiles[]" class="attachFiles" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $sizeDetail ?>
							<?php if ($attachFiles[$i]) { ?>
								<?php if (strpos($_config['imgExt'], $fileExtention[1]) !== false) { ?>
									<br><img src="<?= $ghPath ?>data/worker/<?= $attachFiles[$i] ?>" width="200" style="margin-top:5px;">
								<?php } else { ?>
									<br><span class="file" style="margin-top:5px;"></span> <a href="<?= $ghPath ?>board/download.php?board=N&bbsid=worker&file_name=<?= $attachFiles[$i] ?>&o_file_name=<?= urlencode($attachFilesName[$i]) ?>" download><?= $attachFilesName[$i] ?></a>
								<?php } ?>
								<input type="checkbox" name="deleteFiles<?= $i ?>" value="<?= $attachFiles[$i] ?>"> 삭제
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="fileList"><input type="file" class="input_text" name="attachFiles[]" class="attachFiles" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $sizeDetail ?></div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line3"></td>
		</tr>
	</table>
	<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
		<tr>
			<td align="center">
				<button type="submit" class="red_btn">확 인</button>
				<button type="button" class="gray_btn" onclick="javascript:window.location='worker_list.php?<?= $funcLibrary->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	$(function() {
		//================== 파일 등록 폼 추가 =================
		$(document).on('click', '.add_file', function() {
			var count = 10;
			var list_num = $(".fileList").length;

			if ($(".fileList").length >= count) {
				alert("상세이미지는 " + count + "개까지 등록하실 수 있습니다.");
				return;
			}
			var data;

			var size_txt = "<?= $sizeDetail ?>";

			data = '<div class="fileList"><input type="file" class="input_text" name="attachFiles[]" > ' + size_txt + ' <button type="button" class="del_file red_icon_btn">-</button></div>';
			$(".fileList").last().after(data);
		});


		$(document).on("click", ".del_file", function() {
			if ($(".fileList").length < 2) {
				alert("더이상 삭제하실 수 없습니다.");
				return;
			}

			if (window.confirm("삭제하시겠습니까?")) {
				$(this).parent(".fileList").remove();
			}
		});
		//================== 파일 등록 폼 추가 끝 ================

		//라디오 박스 클릭시 배열에 선택 값 넣기
		$(document).on('click', '.optionSelect', function() {
			let radioData = $(this).val();
			$(this).parent().find('.optionRadioData').val(radioData);
		});
	});

	function fwrite_submit(f) {
		return true;
	}
</script>
<?php include_once $ghPath . 'include/html/admin_bottom.php'; ?>