<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_worker_table';
$category_table = 'gh_category_table';

if ($w == 'u') {
	$d = $query_library->getData($idx, $table_name);
	$attach_files = explode('|', $d['attach_files'] ?? '');
	$attach_files_name = explode('|', $d['attach_files_name'] ?? '');
} else {
	$d = $query_library->getColumn($table_name);
	$attach_files = array();
	$attach_files_name = array();
}

$size_detail = '첨부파일';
?>
<form name="fwrite" method="post" action="./worker_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
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
						$selected = (string)($d['w_type'] ?? '') === (string)$key ? 'selected' : '';
					?>
						<option value="<?= gh_h((string)$key) ?>" <?= $selected ?>><?= gh_h($value) ?></option>
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
				<?php if (array_filter($attach_files) != []) { ?>
					<?php
					for ($i = 0; $i < count((array)$attach_files); $i++) {
						$file_extention = explode('.', $attach_files[$i]);
					?>
						<div class="fileList">
							<input type="hidden" name="old_file[]" value="<?= $attach_files[$i] ?>">
							<input type="hidden" name="old_file_name[]" value="<?= $attach_files_name[$i] ?>">
							<input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $size_detail ?>
							<?php if ($attach_files[$i]) { ?>
								<?php if (strpos($_config['img_ext'], $file_extention[1]) !== false) { ?>
									<br><img src="<?= $gh_path ?>data/worker/<?= $attach_files[$i] ?>" width="200" style="margin-top:5px;">
								<?php } else { ?>
									<br><span class="file" style="margin-top:5px;"></span> <a href="<?= $gh_path ?>board/download.php?board=N&bbsid=worker&file_name=<?= $attach_files[$i] ?>&o_file_name=<?= urlencode($attach_files_name[$i]) ?>" download><?= $attach_files_name[$i] ?></a>
								<?php } ?>
								<input type="checkbox" name="delete_files<?= $i ?>" value="<?= $attach_files[$i] ?>"> 삭제
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="fileList"><input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $size_detail ?></div>
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='worker_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	// 첨부파일·옵션 UI — 동적 행 추가/삭제, optionSelect 연동
	document.addEventListener('DOMContentLoaded', function () {
		document.addEventListener('click', function (e) {
			var addFileBtn = e.target.closest('.add_file');
			if (addFileBtn) {
				var count = 10;
				var fileLists = document.querySelectorAll('.fileList');

				if (fileLists.length >= count) {
					alert('상세이미지는 ' + count + '개까지 등록하실 수 있습니다.');
					return;
				}

				var sizeTxt = '<?= $size_detail ?>';
				var data = document.createElement('div');
				data.className = 'fileList';
				data.innerHTML = '<input type="file" class="input_text" name="attach_files[]" > ' + sizeTxt + ' <button type="button" class="del_file red_icon_btn">-</button>';
				fileLists[fileLists.length - 1].after(data);
				return;
			}

			var delFileBtn = e.target.closest('.del_file');
			if (delFileBtn) {
				var fileLists = document.querySelectorAll('.fileList');
				if (fileLists.length < 2) {
					alert('더이상 삭제하실 수 없습니다.');
					return;
				}

				if (window.confirm('삭제하시겠습니까?')) {
					var parent = delFileBtn.closest('.fileList');
					if (parent) {
						parent.remove();
					}
				}
				return;
			}

			// 라디오 선택값 — 형제 .optionRadioData hidden에 반영
			var optionSelect = e.target.closest('.optionSelect');
			if (optionSelect) {
				var radioData = optionSelect.value;
				var optionRadioData = optionSelect.parentElement.querySelector('.optionRadioData');
				if (optionRadioData) {
					optionRadioData.value = radioData;
				}
			}
		});
	});

	function fwrite_submit(f) {
		return true;
	}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>