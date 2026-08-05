<?php
// 협력사 등록·수정 — gh_partner_table, 첨부·담당자 각 최대 5건 JSON
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_partner_table';
$max_attach = 5;
$max_manager = 5;

if (($w ?? '') == 'u') {
	$d = $query_library->getData($idx, $table_name);
	$attach_files = json_decode((string)($d['attach_files'] ?? '[]'), true);
	$attach_files_name = json_decode((string)($d['attach_files_name'] ?? '[]'), true);
	$managers = json_decode((string)($d['p_manager'] ?? '[]'), true);
	if (!is_array($attach_files)) {
		$attach_files = [];
	}
	if (!is_array($attach_files_name)) {
		$attach_files_name = [];
	}
	if (!is_array($managers)) {
		$managers = [];
	}
} else {
	$d = $query_library->getColumn($table_name);
	$attach_files = [];
	$attach_files_name = [];
	$managers = [];
}

// 담당자 빈 행 — 등록 시 1행 기본 노출
if ($managers === []) {
	$managers[] = ['name' => '', 'email' => '', 'phone' => '', 'role' => ''];
}
?>
<form name="fwrite" method="post" action="./partner_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">사업자등록번호</td>
			<td class="td2">
				<input type="text" name="p_number" id="p-number" class="input_text" value="<?= gh_h((string)($d['p_number'] ?? '')) ?>" style="width:200px;" maxlength="12" inputmode="numeric" autocomplete="off">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">회사명</td>
			<td class="td2">
				<input type="text" name="p_name" class="input_text" value="<?= gh_h((string)($d['p_name'] ?? '')) ?>" style="width:700px;" maxlength="100" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">대표자명</td>
			<td class="td2">
				<input type="text" name="p_ceo_name" class="input_text" value="<?= gh_h((string)($d['p_ceo_name'] ?? '')) ?>" style="width:200px;" maxlength="30">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">담당자정보 <button type="button" class="add_manager red_icon_btn">+</button></td>
			<td class="td2">
				<div id="manager-list">
					<?php foreach ($managers as $mi => $manager) {
						if ($mi >= $max_manager) {
							break;
						}
						$m_name = (string)($manager['name'] ?? '');
						$m_email = (string)($manager['email'] ?? '');
						$m_phone = (string)($manager['phone'] ?? '');
						$m_role = (string)($manager['role'] ?? '');
					?>
						<div class="manager-row">
							<span class="manager-drag-handle" title="드래그하여 순서 변경">☰</span>
							담당자명 <input type="text" name="manager_name[]" class="input_text" value="<?= gh_h($m_name) ?>" style="width:120px;" maxlength="50">
							이메일 <input type="text" name="manager_email[]" class="input_text" value="<?= gh_h($m_email) ?>" style="width:180px;" maxlength="100">
							연락처 <input type="text" name="manager_phone[]" class="input_text" value="<?= gh_h($m_phone) ?>" style="width:140px;" maxlength="30">
							역할 <input type="text" name="manager_role[]" class="input_text" value="<?= gh_h($m_role) ?>" style="width:120px;" maxlength="50">
							<button type="button" class="del_manager red_icon_btn">-</button>
						</div>
					<?php } ?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">첨부파일 <button type="button" class="add_file red_icon_btn">+</button></td>
			<td class="td2">
				<?php if (array_filter($attach_files) != []) { ?>
					<?php
					for ($i = 0; $i < count($attach_files); $i++) {
						if ($i >= $max_attach) {
							break;
						}
						$file_base = $func_library->safeBoardUploadBasename((string)($attach_files[$i] ?? ''));
						$file_org = (string)($attach_files_name[$i] ?? $file_base);
					?>
						<div class="file-list">
							<input type="hidden" name="old_file[]" value="<?= gh_h($file_base) ?>">
							<input type="hidden" name="old_file_name[]" value="<?= gh_h($file_org) ?>">
							<input type="file" class="input_text" name="attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)">
							<?php if ($file_base !== '') { ?>
								<br><span class="file" style="margin-top:5px;"></span>
								<a href="<?= $gh_path ?>board/download.php?board=N&bbsid=partner&file_name=<?= rawurlencode($file_base) ?>&o_file_name=<?= rawurlencode($file_org) ?>" download><?= gh_h($file_org) ?></a>
								<label style="margin-left:10px;"><input type="checkbox" name="delete_files<?= $i ?>" value="<?= gh_h($file_base) ?>"> 삭제</label>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="file-list"><input type="file" class="input_text" name="attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)"></div>
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='partner_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>
<style>
	/* 담당자 행 드래그 — 핸들에서만 순서 변경, 입력 포커스와 충돌 방지 */
	#manager-list .manager-row {
		margin-bottom: 8px;
		padding: 8px 0;
		border-bottom: 1px dashed #ddd;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 4px 6px;
	}
	#manager-list .manager-row.is-dragging {
		opacity: 0.4;
	}
	#manager-list .manager-row.is-drag-over {
		border-top: 2px solid #e85a2a;
	}
	#manager-list .manager-drag-handle {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 22px;
		height: 22px;
		margin-right: 4px;
		color: #888;
		cursor: grab;
		user-select: none;
		font-size: 14px;
		line-height: 1;
	}
	#manager-list .manager-drag-handle:active {
		cursor: grabbing;
	}
</style>
<script type="text/javascript">
	// 등록폼 동적 UI — 담당자·첨부 각 최대 5건, partner_ok JSON 저장과 연동
	document.addEventListener('DOMContentLoaded', function() {
		var maxAttach = <?= (int)$max_attach ?>;
		var maxManager = <?= (int)$max_manager ?>;
		var managerList = document.getElementById('manager-list');
		var dragRow = null;

		function managerRowHtml() {
			return '<span class="manager-drag-handle" title="드래그하여 순서 변경">☰</span> ' +
				'담당자명 <input type="text" name="manager_name[]" class="input_text" style="width:120px;" maxlength="50"> ' +
				'이메일 <input type="text" name="manager_email[]" class="input_text" style="width:180px;" maxlength="100"> ' +
				'연락처 <input type="text" name="manager_phone[]" class="input_text" style="width:140px;" maxlength="30"> ' +
				'역할 <input type="text" name="manager_role[]" class="input_text" style="width:120px;" maxlength="50"> ' +
				'<button type="button" class="del_manager red_icon_btn">-</button>';
		}

		function clearDragOver() {
			if (!managerList) {
				return;
			}
			managerList.querySelectorAll('.manager-row.is-drag-over').forEach(function(el) {
				el.classList.remove('is-drag-over');
			});
		}

		function getDragAfterRow(y) {
			var rows = [].slice.call(managerList.querySelectorAll('.manager-row:not(.is-dragging)'));
			var closest = { offset: Number.NEGATIVE_INFINITY, element: null };
			rows.forEach(function(child) {
				var box = child.getBoundingClientRect();
				var offset = y - box.top - box.height / 2;
				if (offset < 0 && offset > closest.offset) {
					closest = { offset: offset, element: child };
				}
			});
			return closest.element;
		}

		// 사업자등록번호 — 숫자만, 3-2-5 하이픈 자동 완성 (예: 111-87-03332)
		var pNumberEl = document.getElementById('p-number');

		function formatBizNumber(value) {
			var digits = String(value || '').replace(/\D/g, '').slice(0, 10);
			if (digits.length <= 3) {
				return digits;
			}
			if (digits.length <= 5) {
				return digits.slice(0, 3) + '-' + digits.slice(3);
			}
			return digits.slice(0, 3) + '-' + digits.slice(3, 5) + '-' + digits.slice(5);
		}
		if (pNumberEl) {
			pNumberEl.value = formatBizNumber(pNumberEl.value);
			pNumberEl.addEventListener('input', function() {
				pNumberEl.value = formatBizNumber(pNumberEl.value);
			});
			pNumberEl.addEventListener('keydown', function(e) {
				if (e.ctrlKey || e.metaKey || e.altKey) {
					return;
				}
				var allow = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
				if (allow.indexOf(e.key) !== -1) {
					return;
				}
				if (!/^\d$/.test(e.key)) {
					e.preventDefault();
				}
			});
		}

		// 담당자 순서 — 핸들 mousedown 시에만 draggable (입력 필드 드래그 방지)
		if (managerList) {
			managerList.addEventListener('mousedown', function(e) {
				var row = e.target.closest('.manager-row');
				if (!row || !managerList.contains(row)) {
					return;
				}
				row.setAttribute('draggable', e.target.closest('.manager-drag-handle') ? 'true' : 'false');
			});

			managerList.addEventListener('dragstart', function(e) {
				var row = e.target.closest('.manager-row');
				// mousedown에서 핸들일 때만 draggable=true — 입력 필드 드래그와 구분
				if (!row || !managerList.contains(row) || row.getAttribute('draggable') !== 'true') {
					e.preventDefault();
					return;
				}
				dragRow = row;
				e.dataTransfer.effectAllowed = 'move';
				e.dataTransfer.setData('text/plain', '');
				setTimeout(function() {
					row.classList.add('is-dragging');
				}, 0);
			});

			managerList.addEventListener('dragend', function() {
				if (dragRow) {
					dragRow.classList.remove('is-dragging');
					dragRow.setAttribute('draggable', 'false');
				}
				clearDragOver();
				dragRow = null;
			});

			managerList.addEventListener('dragover', function(e) {
				e.preventDefault();
				if (!dragRow) {
					return;
				}
				clearDragOver();
				var after = getDragAfterRow(e.clientY);
				if (after) {
					after.classList.add('is-drag-over');
				}
			});

			managerList.addEventListener('drop', function(e) {
				e.preventDefault();
				if (!dragRow) {
					return;
				}
				var after = getDragAfterRow(e.clientY);
				if (after == null) {
					managerList.appendChild(dragRow);
				} else {
					managerList.insertBefore(dragRow, after);
				}
				clearDragOver();
			});
		}

		document.addEventListener('click', function(e) {
			// 담당자 행 추가 — 최대 maxManager
			var addManagerBtn = e.target.closest('.add_manager');
			if (addManagerBtn) {
				var rows = document.querySelectorAll('#manager-list .manager-row');
				if (rows.length >= maxManager) {
					alert('담당자는 ' + maxManager + '명까지 등록할 수 있습니다.');
					return;
				}
				var data = document.createElement('div');
				data.className = 'manager-row';
				data.innerHTML = managerRowHtml();
				managerList.appendChild(data);
				return;
			}

			var delManagerBtn = e.target.closest('.del_manager');
			if (delManagerBtn) {
				var rows = document.querySelectorAll('#manager-list .manager-row');
				if (rows.length < 2) {
					alert('더이상 삭제하실 수 없습니다.');
					return;
				}
				if (window.confirm('삭제하시겠습니까?')) {
					var parent = delManagerBtn.closest('.manager-row');
					if (parent) {
						parent.remove();
					}
				}
				return;
			}

			// 첨부파일 행 추가 — 최대 maxAttach (.file-list CSS)
			var addFileBtn = e.target.closest('.add_file');
			if (addFileBtn) {
				var fileLists = document.querySelectorAll('.file-list');
				if (fileLists.length >= maxAttach) {
					alert('첨부파일은 ' + maxAttach + '개까지 등록하실 수 있습니다.');
					return;
				}
				var data = document.createElement('div');
				data.className = 'file-list';
				data.innerHTML = '<input type="file" class="input_text" name="attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)"> <button type="button" class="del_file red_icon_btn">-</button>';
				fileLists[fileLists.length - 1].after(data);
				return;
			}

			var delFileBtn = e.target.closest('.del_file');
			if (delFileBtn) {
				var fileLists = document.querySelectorAll('.file-list');
				if (fileLists.length < 2) {
					alert('더이상 삭제하실 수 없습니다.');
					return;
				}
				if (window.confirm('삭제하시겠습니까?')) {
					var parent = delFileBtn.closest('.file-list');
					if (parent) {
						parent.remove();
					}
				}
			}
		});
	});

	function fwrite_submit(f) {
		return true;
	}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>