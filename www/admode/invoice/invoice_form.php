<?php
$ghPath = '../../';
include_once($ghPath . 'include/html/admin_top.php');
include_once($ghPath . 'include/plugin/invoice_parser/InvoiceParser.php');

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

// 기존 파싱 데이터 조회
$invoiceParser = new InvoiceParser();
$invoiceDetail = null;
$invoiceItems = [];
if ($w == 'u' && !empty($idx)) {
	$invoiceDetail = $invoiceParser->getDetailByInvoiceIdx($conn, (int)$idx);
	if ($invoiceDetail) {
		$invoiceItems = $invoiceParser->getItemsByDetailIdx($conn, (int)$invoiceDetail['idx']);
	}
}
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
				<input type="file" class="input_text" name="file1" accept=".pdf,.jpg,.jpeg,.png,.gif" onchange="attachFileCheck(this,<?= FILE_SIZE ?>)">
				<span style="margin-left:10px;color:#888;font-size:11px;">PDF 파일 업로드 시 자동으로 계산서 정보가 파싱됩니다.</span>
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

	<?php if ($invoiceDetail) { ?>
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:20px;">
		<tr>
			<td colspan="4" class="line1"></td>
		</tr>
		<tr class="bgcol1 bold col1 ht center">
			<td colspan="4">전자세금계산서 파싱 정보</td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1" style="width:120px;">승인번호</td>
			<td class="td2" colspan="3"><?= htmlspecialchars($invoiceDetail['approval_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="bgcol1 bold col1 ht center">
			<td colspan="2" style="width:50%;">공급자</td>
			<td colspan="2" style="width:50%;">공급받는자</td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1" style="width:120px;">사업자번호</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['supplier_biz_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			<td class="td1" style="width:120px;">사업자번호</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['receiver_biz_no'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">상호</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['supplier_company'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			<td class="td1">상호</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['receiver_company'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">성명</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['supplier_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			<td class="td1">성명</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['receiver_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">사업장 주소</td>
			<td class="td2"><?= nl2br(htmlspecialchars($invoiceDetail['supplier_address'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
			<td class="td1">사업장 주소</td>
			<td class="td2"><?= nl2br(htmlspecialchars($invoiceDetail['receiver_address'] ?? '', ENT_QUOTES, 'UTF-8')) ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">이메일</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['supplier_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			<td class="td1">이메일</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['receiver_email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">작성일자</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['issue_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			<td class="td1">청구/영수</td>
			<td class="td2"><?= htmlspecialchars($invoiceDetail['claim_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">공급가액</td>
			<td class="td2">₩ <?= number_format((int)($invoiceDetail['supply_amount'] ?? 0)) ?></td>
			<td class="td1">세액</td>
			<td class="td2">₩ <?= number_format((int)($invoiceDetail['tax_amount'] ?? 0)) ?></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">합계금액</td>
			<td class="td2" colspan="3"><strong>₩ <?= number_format((int)($invoiceDetail['total_amount'] ?? 0)) ?></strong></td>
		</tr>
		<tr>
			<td colspan="4" class="line2"></td>
		</tr>
	</table>

	<?php if (!empty($invoiceItems)) { ?>
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:10px;">
		<tr>
			<td colspan="9" class="line1"></td>
		</tr>
		<tr class="bgcol1 bold col1 ht center">
			<td colspan="9">품목 내역</td>
		</tr>
		<tr>
			<td colspan="9" class="line2"></td>
		</tr>
		<tr class="bgcol1 bold col1 ht center">
			<td style="width:40px;">월</td>
			<td style="width:40px;">일</td>
			<td>품목</td>
			<td style="width:80px;">규격</td>
			<td style="width:60px;">수량</td>
			<td style="width:100px;">단가</td>
			<td style="width:120px;">공급가액</td>
			<td style="width:120px;">세액</td>
			<td style="width:80px;">비고</td>
		</tr>
		<?php foreach ($invoiceItems as $item) { ?>
			<tr class="list col1 ht center">
				<td><?= htmlspecialchars($item['item_month'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
				<td><?= htmlspecialchars($item['item_day'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
				<td style="text-align:left;padding-left:10px;"><?= htmlspecialchars($item['item_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
				<td><?= htmlspecialchars($item['item_spec'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
				<td><?= number_format((float)($item['item_qty'] ?? 0)) ?></td>
				<td style="text-align:right;padding-right:10px;">₩ <?= number_format((int)($item['item_unit_price'] ?? 0)) ?></td>
				<td style="text-align:right;padding-right:10px;">₩ <?= number_format((int)($item['item_supply_amount'] ?? 0)) ?></td>
				<td style="text-align:right;padding-right:10px;">₩ <?= number_format((int)($item['item_tax_amount'] ?? 0)) ?></td>
				<td><?= htmlspecialchars($item['item_remark'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
			</tr>
			<tr>
				<td colspan="9" class="line2"></td>
			</tr>
		<?php } ?>
	</table>
	<?php } ?>
	<?php } ?>

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