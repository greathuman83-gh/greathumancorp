<?php
$ghPath = '../../';
include_once($ghPath . 'include/html/admin_top.php');

$tableName = 'gh_invoice_table';
$categoryTable = 'gh_category_table';

if ($w == 'u') {
	$d = $queryLibrary->getData($idx, $tableName);
} else {
	$d = $queryLibrary->getColumn($tableName);
}
// 금액(공급가액) 기준 VAT 10% · 총액 (숫자만 추출)
$priceNum = (float) preg_replace('/[^0-9.-]/', '', (string)($d['i_price'] ?? ''));
$priceVat = $priceNum > 0 ? (int) round($priceNum * 0.1) : 0;
$totalPrice = $priceNum + $priceVat;
?>
<form name="fwrite" method="post" action="./invoice_ok.php?<?= $funcLibrary->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">타이틀</td>
			<td class="td2">
				<input type="text" name="title" class="input_text" value="<?= htmlspecialchars((string)($d['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:700px;" maxlength="200" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">발행일</td>
			<td class="td2">
				<input type="text" name="i_date" class="input_text date" value="<?= htmlspecialchars((string)($d['i_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">회사명</td>
			<td class="td2">
				<input type="text" name="i_company" class="input_text" value="<?= htmlspecialchars((string)($d['i_company'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:700px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">금액<span class="sub_txt" style="margin-left:6px;color:#666;font-size:11px;">(공급가액)</span></td>
			<td class="td2">
				₩ <input type="number" name="i_price" id="i_price" class="input_text" value="<?= htmlspecialchars((string)($d['i_price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="width:300px;" maxlength="30" inputmode="numeric" autocomplete="off">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">VAT</td>
			<td class="td2">
				₩ <div id="priceVat" class="invoice_calc" style="display:inline-block;"><?= number_format($priceVat) ?></div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">총 금액</td>
			<td class="td2">
				₩ <div id="totalPrice" class="invoice_calc" style="display:inline-block;"><?= number_format($totalPrice) ?></div>
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
			<td class="td1">세금계산서 파일</td>
			<td class="td2">
				<input type="file" class="input_text" name="file1" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)">
				<?php if (!empty($d['file1'])) { ?>
					<input type="hidden" name="old_file1" value="<?= htmlspecialchars((string)$d['file1'], ENT_QUOTES, 'UTF-8') ?>">
					<br>
					<a href="<?= $ghPath ?>board/download.php?board=N&bbsid=invoice&file_name=<?= rawurlencode($d['file1']) ?>&o_file_name=<?= rawurlencode($d['file1_name']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars((string)($d['file1_name'] ?? $d['file1']), ENT_QUOTES, 'UTF-8') ?></a>
					<label style="margin-left:10px;"><input type="checkbox" name="delete_file1" value="1"> 삭제</label>
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='invoice_list.php?<?= $funcLibrary->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	(function() {
		function parseInvoicePrice(str) {
			var n = parseFloat(String(str).replace(/[^0-9.-]/g, ''));
			return isNaN(n) ? 0 : n;
		}

		function formatInvoiceNum(n) {
			return Math.round(n).toLocaleString('ko-KR');
		}

		function updateInvoiceAmounts() {
			var el = document.getElementById('i_price');
			if (!el) return;
			var supply = parseInvoicePrice(el.value);
			var vat = Math.round(supply * 0.1);
			var total = supply + vat;
			var vatEl = document.getElementById('priceVat');
			var totEl = document.getElementById('totalPrice');
			var hid = document.getElementById('i_price_vat');
			if (vatEl) vatEl.textContent = formatInvoiceNum(vat);
			if (totEl) totEl.textContent = formatInvoiceNum(total);
			if (hid) hid.value = String(vat);
		}
		document.addEventListener('DOMContentLoaded', function() {
			var el = document.getElementById('i_price');
			if (!el) return;
			el.addEventListener('input', updateInvoiceAmounts);
			el.addEventListener('change', updateInvoiceAmounts);
			updateInvoiceAmounts();
		});
	})();

	function fwrite_submit(f) {
		var p = f.i_price;
		if (p && p.value !== '') {
			p.value = String(p.value).replace(/[^0-9.-]/g, '');
		}
		return true;
	}
</script>
<?php include_once $ghPath . 'include/html/admin_bottom.php'; ?>