<?php
// 세금계산서 등록·수정 — i_content JSON, 매출=공급받는자 / 매입=공급자만 입력
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_invoice_table';
$page_type = (string)($page_type ?? '1');
if ($page_type !== '1' && $page_type !== '2') {
	$page_type = '1';
}
$target_label = $page_type === '1' ? '매출 대상' : '매입 대상';

if (($w ?? '') == 'u') {
	$d = $query_library->getData($idx, $table_name);
	$content = json_decode((string)($d['i_content'] ?? ''), true);
	if (!is_array($content)) {
		$content = [];
	}
	// 이전 buyer_/supplier_ 키 호환 — company_* 로 보정
	if (($content['company_name'] ?? '') === '') {
		if ($page_type === '1') {
			$content['company_biz_no'] = (string)($content['buyer_biz_no'] ?? $content['company_biz_no'] ?? '');
			$content['company_sub_no'] = (string)($content['buyer_sub_no'] ?? $content['company_sub_no'] ?? '');
			$content['company_name'] = (string)($content['buyer_name'] ?? $content['company_name'] ?? '');
			$content['company_ceo'] = (string)($content['buyer_ceo'] ?? $content['company_ceo'] ?? '');
			$content['company_address'] = (string)($content['buyer_address'] ?? $content['company_address'] ?? '');
			$content['company_email'] = (string)($content['buyer_email1'] ?? $content['company_email'] ?? '');
		} else {
			$content['company_biz_no'] = (string)($content['supplier_biz_no'] ?? $content['company_biz_no'] ?? '');
			$content['company_sub_no'] = (string)($content['supplier_sub_no'] ?? $content['company_sub_no'] ?? '');
			$content['company_name'] = (string)($content['supplier_name'] ?? $content['company_name'] ?? '');
			$content['company_ceo'] = (string)($content['supplier_ceo'] ?? $content['company_ceo'] ?? '');
			$content['company_address'] = (string)($content['supplier_address'] ?? $content['company_address'] ?? '');
			$content['company_email'] = (string)($content['supplier_email'] ?? $content['company_email'] ?? '');
		}
	}
} else {
	$d = $query_library->getColumn($table_name);
	$content = [];
}

// 대금 수금/지급 상태 — 테이블 컬럼 (1:미처리 2:부분처리 3:처리완료)
$i_payment_status = (string)($d['i_payment_status'] ?? '1');
if (!in_array($i_payment_status, ['1', '2', '3'], true)) {
	$i_payment_status = '1';
}
$i_part_payment = (string)($d['i_part_payment'] ?? '');

// 대금처리상태 옵션 색 — 목록과 동일 (미처리 파랑 / 부분처리 오렌지 / 처리완료 빨강)
$payment_status_colors = [
	'1' => 'blue',
	'2' => 'orange',
	'3' => 'red',
];
$payment_status_color = $payment_status_colors[$i_payment_status] ?? 'blue';

// 금액 표시 — 숫자만 추출 후 세자리 콤마
function invoice_format_price(string $value): string
{
	$digits = preg_replace('/[^\d.-]/', '', $value);
	if ($digits === '' || $digits === null || !is_numeric($digits)) {
		return '';
	}
	return number_format((float)$digits);
}

$c = static function (array $content, string $key): string {
	return (string)($content[$key] ?? '');
};

$view_only = !$admin_super;
// 등록 — 슈퍼관리자만, 상세(w=u)는 일반 관리자 읽기전용
if (($w ?? '') != 'u' && $view_only) {
	$func_library->alert($_pageText['등록하실 권한이 없습니다.']);
}
?>
<form name="fwrite" method="post" action="./invoice_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" style="margin:0px;">
	<fieldset<?= $view_only ? ' disabled' : '' ?> style="border:0;margin:0;padding:0;min-width:0;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 상호</td>
			<td class="td2">
				<input type="text" name="company_name" class="input_text" value="<?= gh_h($c($content, 'company_name')) ?>" style="width:700px;" maxlength="100">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">대금 수금/지급 상태</td>
			<td class="td2">
				<select name="i_payment_status" id="i-payment-status" class="input_select" style="color:<?= gh_h($payment_status_color) ?>;">
					<option value="1" style="color:blue;" <?php if ($i_payment_status === '1') { ?>selected<?php } ?>>미처리</option>
					<option value="2" style="color:orange;" <?php if ($i_payment_status === '2') { ?>selected<?php } ?>>부분처리</option>
					<option value="3" style="color:red;" <?php if ($i_payment_status === '3') { ?>selected<?php } ?>>처리완료</option>
				</select>
				부분처리대금 : ₩ <input type="text" name="i_part_payment" class="input_text invoice-amount" value="<?= gh_h(invoice_format_price($i_part_payment)) ?>" style="width:200px;" inputmode="numeric" autocomplete="off">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">품목명</td>
			<td class="td2">
				<input type="text" name="item_name" class="input_text" value="<?= gh_h($c($content, 'item_name')) ?>" style="width:700px;" maxlength="200" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">합계금액</td>
			<td class="td2">
				₩ <input type="text" name="total_amount" class="input_text invoice-amount" value="<?= gh_h(invoice_format_price($c($content, 'total_amount'))) ?>" style="width:300px;" inputmode="numeric" autocomplete="off">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">공급가액</td>
			<td class="td2">
				₩ <input type="text" name="supply_amount" class="input_text invoice-amount" value="<?= gh_h(invoice_format_price($c($content, 'supply_amount'))) ?>" style="width:300px;" inputmode="numeric" autocomplete="off">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">세액</td>
			<td class="td2">
				₩ <input type="text" name="tax_amount" class="input_text invoice-amount" value="<?= gh_h(invoice_format_price($c($content, 'tax_amount'))) ?>" style="width:300px;" inputmode="numeric" autocomplete="off">
			</td>
		</tr>

		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">작성일자</td>
			<td class="td2">
				<input type="text" name="write_date" class="input_text date" value="<?= gh_h($c($content, 'write_date')) ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">승인번호</td>
			<td class="td2">
				<input type="text" name="approval_no" class="input_text" value="<?= gh_h($c($content, 'approval_no')) ?>" style="width:400px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">발급일자</td>
			<td class="td2">
				<input type="text" name="issue_date" class="input_text date" value="<?= gh_h($c($content, 'issue_date')) ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">전송일자</td>
			<td class="td2">
				<input type="text" name="transmit_date" class="input_text date" value="<?= gh_h($c($content, 'transmit_date')) ?>" style="width:200px;" maxlength="10" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 사업자등록번호</td>
			<td class="td2">
				<input type="text" name="company_biz_no" class="input_text" value="<?= gh_h($c($content, 'company_biz_no')) ?>" style="width:200px;" maxlength="20">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 종사업장번호</td>
			<td class="td2">
				<input type="text" name="company_sub_no" class="input_text" value="<?= gh_h($c($content, 'company_sub_no')) ?>" style="width:200px;" maxlength="20">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 대표자명</td>
			<td class="td2">
				<input type="text" name="company_ceo" class="input_text" value="<?= gh_h($c($content, 'company_ceo')) ?>" style="width:200px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 주소</td>
			<td class="td2">
				<input type="text" name="company_address" class="input_text" value="<?= gh_h($c($content, 'company_address')) ?>" style="width:700px;" maxlength="200">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1"><?= gh_h($target_label) ?> 이메일</td>
			<td class="td2">
				<input type="text" name="company_email" class="input_text" value="<?= gh_h($c($content, 'company_email')) ?>" style="width:400px;" maxlength="100">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line3"></td>
		</tr>
	</table>
	</fieldset>
	<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
		<tr>
			<td align="center">
				<?php if ($view_only) { ?>
				<button type="button" class="gray_btn" onclick="javascript:window.location='invoice_list.php?<?= $func_library->queryString('idx,w') ?>'">목록</button>
				<?php } else { ?>
				<button type="submit" class="red_btn">확 인</button>
				<button type="button" class="gray_btn" onclick="javascript:window.location='invoice_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
				<?php } ?>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	// 금액 필드 — 숫자만 추출 후 세자리 콤마
	function invoiceFormatAmount(value) {
		var digits = String(value || '').replace(/[^\d]/g, '');
		if (digits === '') {
			return '';
		}
		return digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
	}

	// 금액 필드 — 제출 전 숫자만 남김
	function fwrite_submit(f) {
		document.querySelectorAll('.invoice-amount').forEach(function(el) {
			el.value = String(el.value).replace(/[^\d]/g, '');
		});
		return true;
	}

	// 금액 입력 — 숫자만 허용, 입력 중 세자리 콤마 표기
	(function() {
		document.querySelectorAll('.invoice-amount').forEach(function(el) {
			el.addEventListener('input', function() {
				var caret = el.selectionStart;
				var prevLen = el.value.length;
				el.value = invoiceFormatAmount(el.value);
				var nextLen = el.value.length;
				var nextCaret = Math.max(0, (caret || 0) + (nextLen - prevLen));
				try {
					el.setSelectionRange(nextCaret, nextCaret);
				} catch (e) {}
			});
			el.addEventListener('keydown', function(e) {
				// 제어키·단축키 허용
				if (e.ctrlKey || e.metaKey || e.altKey) {
					return;
				}
				var allow = ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
				if (allow.indexOf(e.key) !== -1) {
					return;
				}
				if (!/^\d$/.test(e.key)) {
					e.preventDefault();
				}
			});
			el.addEventListener('paste', function(e) {
				e.preventDefault();
				var text = '';
				if (e.clipboardData) {
					text = e.clipboardData.getData('text');
				}
				el.value = invoiceFormatAmount(text);
			});
			el.value = invoiceFormatAmount(el.value);
		});
	})();

	// 대금처리상태 select — 선택값에 맞춰 표시 색 갱신
	(function() {
		var colors = {
			'1': 'blue',
			'2': 'orange',
			'3': 'red'
		};
		var sel = document.getElementById('i-payment-status');
		if (!sel) {
			return;
		}

		function syncPaymentStatusColor() {
			sel.style.color = colors[sel.value] || 'blue';
		}
		sel.addEventListener('change', syncPaymentStatusColor);
		syncPaymentStatusColor();
	})();
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>