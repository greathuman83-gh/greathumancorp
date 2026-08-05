<?php
// 사업건 등록·수정 — gh_business_table, 매출처/매입처 JSON·첨부(매출/매입 각 3건)
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_business_table';
$max_attach = 3;

if (($w ?? '') == 'u') {
	$d = $query_library->getData($idx, $table_name);
} else {
	$d = $query_library->getColumn($table_name);
}

$sales_info = json_decode((string)($d['b_sales_info'] ?? ''), true);
$purchasing_info = json_decode((string)($d['b_purchasing_info'] ?? ''), true);
if (!is_array($sales_info)) {
	$sales_info = [];
}
if (!is_array($purchasing_info)) {
	$purchasing_info = [];
}

$sales_manager = is_array($sales_info['manager'] ?? null) ? $sales_info['manager'] : [];
$purchasing_manager = is_array($purchasing_info['manager'] ?? null) ? $purchasing_info['manager'] : [];

$attach_files = json_decode((string)($d['attach_files'] ?? ''), true);
$attach_files_name = json_decode((string)($d['attach_files_name'] ?? ''), true);
if (!is_array($attach_files)) {
	$attach_files = [];
}
if (!is_array($attach_files_name)) {
	$attach_files_name = [];
}
$sales_files = is_array($attach_files['sales'] ?? null) ? $attach_files['sales'] : [];
$sales_files_name = is_array($attach_files_name['sales'] ?? null) ? $attach_files_name['sales'] : [];
$purchasing_files = is_array($attach_files['purchasing'] ?? null) ? $attach_files['purchasing'] : [];
$purchasing_files_name = is_array($attach_files_name['purchasing'] ?? null) ? $attach_files_name['purchasing'] : [];

// 금액 표시 — 숫자만 추출 후 세자리 콤마
function business_format_price(string $value): string
{
	$digits = preg_replace('/\D/', '', $value);
	if ($digits === '' || $digits === null) {
		return '';
	}
	return number_format((float)$digits);
}

$sales_total_price = business_format_price((string)($sales_info['total_price'] ?? ''));
$purchasing_total_price = business_format_price((string)($purchasing_info['total_price'] ?? ''));
$sales_price = business_format_price((string)($d['b_sales_price'] ?? ''));
$purchasing_price = business_format_price((string)($d['b_purchasing_price'] ?? ''));

$margin_num = (float)preg_replace('/\D/', '', (string)($d['b_total_margin'] ?? '0'));
$margin_vat = $margin_num > 0 ? (int)round($margin_num * 1.1) : 0;
?>
<form name="fwrite" method="post" action="./business_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">사업명(프로젝트명)</td>
			<td class="td2">
				<input type="text" name="b_name" class="input_text" value="<?= gh_h((string)($d['b_name'] ?? '')) ?>" style="width:700px;" maxlength="150" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">사업기간</td>
			<td class="td2">
				<input type="month" name="b_start_date" class="input_text" value="<?= gh_h((string)($d['b_start_date'] ?? '')) ?>" style="width:160px;">
				~
				<input type="month" name="b_end_date" class="input_text" value="<?= gh_h((string)($d['b_end_date'] ?? '')) ?>" style="width:160px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">매출처 정보</td>
			<td class="td2">
				<div class="partner-info-block">
					<input type="hidden" name="sales_partner_idx" id="sales-partner-idx" value="<?= (int)($sales_info['partner_idx'] ?? 0) ?>">
					<div class="partner-info-row">
						<span class="partner-info-field">회사명 <input type="text" name="sales_company" id="sales-company" class="input_text" value="<?= gh_h((string)($sales_info['company'] ?? '')) ?>" style="width:280px;" maxlength="100"></span>
						<button type="button" class="black_icon_btn partner-search-btn" data-target="sales">협력사검색</button>
					</div>
					<div class="partner-info-row">
						<span class="partner-info-field">담당자명 <input type="text" name="sales_manager_name" id="sales-manager-name" class="input_text" value="<?= gh_h((string)($sales_manager['name'] ?? '')) ?>" style="width:120px;" maxlength="50"></span>
						<span class="partner-info-field">이메일 <input type="text" name="sales_manager_email" id="sales-manager-email" class="input_text" value="<?= gh_h((string)($sales_manager['email'] ?? '')) ?>" style="width:180px;" maxlength="100"></span>
						<span class="partner-info-field">연락처 <input type="text" name="sales_manager_phone" id="sales-manager-phone" class="input_text" value="<?= gh_h((string)($sales_manager['phone'] ?? '')) ?>" style="width:140px;" maxlength="30"></span>
					</div>
					<div class="partner-info-row">
						<span class="partner-info-field">총매출금액 ₩ <input type="text" name="sales_total_price" id="sales-total-price" class="input_text business-price" value="<?= gh_h($sales_total_price) ?>" style="width:200px;" inputmode="numeric" autocomplete="off"></span>
						<span class="partner-info-field">1회 매출금액 ₩ <input type="text" name="b_sales_price" id="b-sales-price" class="input_text business-price" value="<?= gh_h($sales_price) ?>" style="width:200px;" inputmode="numeric" autocomplete="off"></span>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">매입처 정보</td>
			<td class="td2">
				<div class="partner-info-block">
					<input type="hidden" name="purchasing_partner_idx" id="purchasing-partner-idx" value="<?= (int)($purchasing_info['partner_idx'] ?? 0) ?>">
					<div class="partner-info-row">
						<span class="partner-info-field">회사명 <input type="text" name="purchasing_company" id="purchasing-company" class="input_text" value="<?= gh_h((string)($purchasing_info['company'] ?? '')) ?>" style="width:280px;" maxlength="100"></span>
						<button type="button" class="black_icon_btn partner-search-btn" data-target="purchasing">협력사검색</button>
					</div>
					<div class="partner-info-row">
						<span class="partner-info-field">담당자명 <input type="text" name="purchasing_manager_name" id="purchasing-manager-name" class="input_text" value="<?= gh_h((string)($purchasing_manager['name'] ?? '')) ?>" style="width:120px;" maxlength="50"></span>
						<span class="partner-info-field">이메일 <input type="text" name="purchasing_manager_email" id="purchasing-manager-email" class="input_text" value="<?= gh_h((string)($purchasing_manager['email'] ?? '')) ?>" style="width:180px;" maxlength="100"></span>
						<span class="partner-info-field">연락처 <input type="text" name="purchasing_manager_phone" id="purchasing-manager-phone" class="input_text" value="<?= gh_h((string)($purchasing_manager['phone'] ?? '')) ?>" style="width:140px;" maxlength="30"></span>
					</div>
					<div class="partner-info-row">
						<span class="partner-info-field">총매입금액 ₩ <input type="text" name="purchasing_total_price" id="purchasing-total-price" class="input_text business-price" value="<?= gh_h($purchasing_total_price) ?>" style="width:200px;" inputmode="numeric" autocomplete="off"></span>
						<span class="partner-info-field">1회 매입금액 ₩ <input type="text" name="b_purchasing_price" id="b-purchasing-price" class="input_text business-price" value="<?= gh_h($purchasing_price) ?>" style="width:200px;" inputmode="numeric" autocomplete="off"></span>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">총 마진</td>
			<td class="td2">
				<input type="hidden" name="b_total_margin" id="b-total-margin" value="<?= gh_h((string)($d['b_total_margin'] ?? '')) ?>">
				₩ <span id="total-margin-display"><?= number_format($margin_num) ?></span>
				<span id="total-margin-vat-display">(<?= number_format($margin_vat) ?>)</span>
				<span class="sub_txt" style="margin-left:8px;color:#666;font-size:11px;">총마진금액 (VAT 포함)</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">매출용 첨부파일 <button type="button" class="add_file red_icon_btn" data-file-type="sales">+</button></td>
			<td class="td2" id="sales-file-wrap">
				<?php if (array_filter($sales_files) != []) { ?>
					<?php
					for ($i = 0; $i < count($sales_files); $i++) {
						if ($i >= $max_attach) {
							break;
						}
						$file_base = $func_library->safeBoardUploadBasename((string)($sales_files[$i] ?? ''));
						$file_org = (string)($sales_files_name[$i] ?? $file_base);
					?>
						<div class="file-list" data-file-type="sales">
							<input type="hidden" name="old_sales_file[]" value="<?= gh_h($file_base) ?>">
							<input type="hidden" name="old_sales_file_name[]" value="<?= gh_h($file_org) ?>">
							<input type="file" class="input_text" name="sales_attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)">
							<?php if ($file_base !== '') { ?>
								<br><span class="file" style="margin-top:5px;"></span>
								<a href="<?= $gh_path ?>board/download.php?board=N&bbsid=business&file_name=<?= rawurlencode($file_base) ?>&o_file_name=<?= rawurlencode($file_org) ?>" download><?= gh_h($file_org) ?></a>
								<label style="margin-left:10px;"><input type="checkbox" name="delete_sales_files<?= $i ?>" value="<?= gh_h($file_base) ?>"> 삭제</label>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="file-list" data-file-type="sales"><input type="file" class="input_text" name="sales_attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)"></div>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">매입용 첨부파일 <button type="button" class="add_file red_icon_btn" data-file-type="purchasing">+</button></td>
			<td class="td2" id="purchasing-file-wrap">
				<?php if (array_filter($purchasing_files) != []) { ?>
					<?php
					for ($i = 0; $i < count($purchasing_files); $i++) {
						if ($i >= $max_attach) {
							break;
						}
						$file_base = $func_library->safeBoardUploadBasename((string)($purchasing_files[$i] ?? ''));
						$file_org = (string)($purchasing_files_name[$i] ?? $file_base);
					?>
						<div class="file-list" data-file-type="purchasing">
							<input type="hidden" name="old_purchasing_file[]" value="<?= gh_h($file_base) ?>">
							<input type="hidden" name="old_purchasing_file_name[]" value="<?= gh_h($file_org) ?>">
							<input type="file" class="input_text" name="purchasing_attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)">
							<?php if ($file_base !== '') { ?>
								<br><span class="file" style="margin-top:5px;"></span>
								<a href="<?= $gh_path ?>board/download.php?board=N&bbsid=business&file_name=<?= rawurlencode($file_base) ?>&o_file_name=<?= rawurlencode($file_org) ?>" download><?= gh_h($file_org) ?></a>
								<label style="margin-left:10px;"><input type="checkbox" name="delete_purchasing_files<?= $i ?>" value="<?= gh_h($file_base) ?>"> 삭제</label>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="file-list" data-file-type="purchasing"><input type="file" class="input_text" name="purchasing_attach_files[]" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)"></div>
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='business_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>

<!-- 협력사 검색 레이어 — partner_search.php JSON 연동 -->
<div id="partner-search-popup" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);width:720px;max-width:94vw;max-height:80vh;background:#fff;z-index:1002;padding:20px;box-sizing:border-box;overflow:auto;">
	<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
		<strong>협력사 검색</strong>
		<button type="button" class="close gray_icon_btn" id="partner-popup-close">닫기</button>
	</div>
	<div style="margin-bottom:12px;">
		<input type="text" id="partner-search-keyword" class="input_text" style="width:260px;">
		<button type="button" class="search_btn" id="partner-search-submit">검색</button>
	</div>
	<table cellpadding="0" cellspacing="0" class="adminMenuTable" style="width:100%;">
		<thead>
			<tr class="bgcol1 bold col1 ht center">
				<td width="60">선택</td>
				<td>회사명</td>
				<td width="140">사업자등록번호</td>
				<td width="100">대표자</td>
				<td width="100">담당자</td>
			</tr>
		</thead>
		<tbody id="partner-search-body">
			<tr><td colspan="5" class="center">검색어를 입력하거나 검색을 눌러 주세요.</td></tr>
		</tbody>
	</table>
</div>

<style>
	#total-margin-vat-display { color: #666; margin-left: 4px; }
	#partner-search-popup .partner-pick { cursor: pointer; }
	/* 매출처·매입처 입력 — 행/필드 간격 */
	.partner-info-block {
		padding: 6px 0;
	}
	.partner-info-row {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 10px 16px;
		margin-bottom: 14px;
	}
	.partner-info-row:last-child {
		margin-bottom: 0;
	}
	.partner-info-field {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		white-space: nowrap;
	}
</style>
<script type="text/javascript">
	// 등록폼 UI — 금액 콤마·총마진 자동계산·협력사 레이어·첨부(매출/매입 각 3건)
	document.addEventListener('DOMContentLoaded', function() {
		var maxAttach = <?= (int)$max_attach ?>;
		var partnerTarget = '';
		var popup = document.getElementById('partner-search-popup');
		var bg = document.getElementById('bg');

		function digitsOnly(value) {
			return String(value || '').replace(/\D/g, '');
		}

		function formatPrice(value) {
			var digits = digitsOnly(value);
			if (digits === '') {
				return '';
			}
			return Number(digits).toLocaleString('ko-KR');
		}

		function parsePrice(value) {
			var n = parseFloat(digitsOnly(value));
			return isNaN(n) ? 0 : n;
		}

		// 총마진 — 총매출-총매입, VAT 포함액은 *1.1
		function updateMargin() {
			var sales = parsePrice(document.getElementById('sales-total-price').value);
			var purchasing = parsePrice(document.getElementById('purchasing-total-price').value);
			var margin = sales - purchasing;
			var vatIncluded = Math.round(margin * 1.1);
			document.getElementById('b-total-margin').value = String(margin);
			document.getElementById('total-margin-display').textContent = margin.toLocaleString('ko-KR');
			document.getElementById('total-margin-vat-display').textContent = '(' + vatIncluded.toLocaleString('ko-KR') + ')';
		}

		document.querySelectorAll('.business-price').forEach(function(el) {
			el.addEventListener('input', function() {
				var start = el.selectionStart;
				var before = el.value;
				el.value = formatPrice(el.value);
				// 커서 보정 — 콤마 삽입으로 인한 위치 어긋남 최소화
				var diff = el.value.length - before.length;
				if (typeof start === 'number') {
					el.setSelectionRange(Math.max(0, start + diff), Math.max(0, start + diff));
				}
				if (el.id === 'sales-total-price' || el.id === 'purchasing-total-price') {
					updateMargin();
				}
			});
			el.addEventListener('keydown', function(e) {
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
		});
		updateMargin();

		function openPartnerPopup(target) {
			partnerTarget = target;
			if (popup) {
				popup.style.display = 'block';
			}
			if (bg) {
				bg.style.display = 'block';
				bg.style.opacity = '1';
			}
			loadPartners('');
		}

		function closePartnerPopup() {
			if (popup) {
				popup.style.display = 'none';
			}
			if (bg) {
				bg.style.display = 'none';
			}
		}

		function applyPartner(item) {
			var prefix = partnerTarget === 'purchasing' ? 'purchasing' : 'sales';
			var manager = item.manager || {};
			document.getElementById(prefix + '-partner-idx').value = String(item.idx || 0);
			document.getElementById(prefix + '-company').value = item.p_name || '';
			document.getElementById(prefix + '-manager-name').value = manager.name || '';
			document.getElementById(prefix + '-manager-email').value = manager.email || '';
			document.getElementById(prefix + '-manager-phone').value = manager.phone || '';
			closePartnerPopup();
		}

		function loadPartners(keyword) {
			var body = document.getElementById('partner-search-body');
			if (!body) {
				return;
			}
			body.innerHTML = '<tr><td colspan="5" class="center">조회 중...</td></tr>';
			var url = './partner_search.php?keyword=' + encodeURIComponent(keyword || '');
			fetch(url, { credentials: 'same-origin' })
				.then(function(res) { return res.json(); })
				.then(function(data) {
					if (!data || !data.ok) {
						body.innerHTML = '<tr><td colspan="5" class="center">조회에 실패했습니다.</td></tr>';
						return;
					}
					var list = data.list || [];
					if (list.length === 0) {
						body.innerHTML = '<tr><td colspan="5" class="center">검색 결과가 없습니다.</td></tr>';
						return;
					}
					var html = '';
					list.forEach(function(item, index) {
						html += '<tr class="list col1 ht center partner-pick" data-index="' + index + '">';
						html += '<td><button type="button" class="black_icon_btn partner-select-btn">선택</button></td>';
						html += '<td class="td2">' + escapeHtml(item.p_name || '') + '</td>';
						html += '<td>' + escapeHtml(item.p_number || '') + '</td>';
						html += '<td>' + escapeHtml(item.p_ceo_name || '') + '</td>';
						html += '<td>' + escapeHtml((item.manager && item.manager.name) || '') + '</td>';
						html += '</tr>';
					});
					body.innerHTML = html;
					body.querySelectorAll('.partner-pick').forEach(function(row) {
						row.addEventListener('click', function(e) {
							if (e.target.closest('button') || e.target.closest('.partner-pick')) {
								var idx = parseInt(row.getAttribute('data-index'), 10);
								if (!isNaN(idx) && list[idx]) {
									applyPartner(list[idx]);
								}
							}
						});
					});
				})
				.catch(function() {
					body.innerHTML = '<tr><td colspan="5" class="center">조회에 실패했습니다.</td></tr>';
				});
		}

		function escapeHtml(str) {
			return String(str)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;');
		}

		document.querySelectorAll('.partner-search-btn').forEach(function(btn) {
			btn.addEventListener('click', function() {
				openPartnerPopup(btn.getAttribute('data-target') || 'sales');
			});
		});

		var closeBtn = document.getElementById('partner-popup-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', closePartnerPopup);
		}
		if (bg) {
			bg.addEventListener('click', function() {
				if (popup && popup.style.display !== 'none') {
					closePartnerPopup();
				}
			});
		}

		var searchSubmit = document.getElementById('partner-search-submit');
		var searchKeyword = document.getElementById('partner-search-keyword');
		if (searchSubmit) {
			searchSubmit.addEventListener('click', function() {
				loadPartners(searchKeyword ? searchKeyword.value : '');
			});
		}
		if (searchKeyword) {
			searchKeyword.addEventListener('keydown', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					loadPartners(searchKeyword.value);
				}
			});
		}

		document.addEventListener('click', function(e) {
			var addFileBtn = e.target.closest('.add_file');
			if (addFileBtn) {
				var fileType = addFileBtn.getAttribute('data-file-type') || 'sales';
				var wrap = document.getElementById(fileType + '-file-wrap');
				if (!wrap) {
					return;
				}
				var fileLists = wrap.querySelectorAll('.file-list');
				if (fileLists.length >= maxAttach) {
					alert('첨부파일은 ' + maxAttach + '개까지 등록하실 수 있습니다.');
					return;
				}
				var inputName = fileType === 'purchasing' ? 'purchasing_attach_files[]' : 'sales_attach_files[]';
				var data = document.createElement('div');
				data.className = 'file-list';
				data.setAttribute('data-file-type', fileType);
				data.innerHTML = '<input type="file" class="input_text" name="' + inputName + '" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)"> <button type="button" class="del_file red_icon_btn">-</button>';
				fileLists[fileLists.length - 1].after(data);
				return;
			}

			var delFileBtn = e.target.closest('.del_file');
			if (delFileBtn) {
				var parent = delFileBtn.closest('.file-list');
				if (!parent) {
					return;
				}
				var wrap = parent.parentElement;
				var fileLists = wrap.querySelectorAll('.file-list');
				if (fileLists.length < 2) {
					alert('더이상 삭제하실 수 없습니다.');
					return;
				}
				if (window.confirm('삭제하시겠습니까?')) {
					parent.remove();
				}
			}
		});
	});

	function fwrite_submit(f) {
		['sales_total_price', 'purchasing_total_price', 'b_sales_price', 'b_purchasing_price', 'b_total_margin'].forEach(function(name) {
			if (f[name] && f[name].value !== '') {
				f[name].value = String(f[name].value).replace(/\D/g, '');
			}
		});
		return true;
	}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>
