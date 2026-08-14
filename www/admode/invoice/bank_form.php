<?php
// 통장거래내역 등록·수정 — b_content JSON, category(1:출금 2:입금)
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_bank_table';

if (($w ?? '') == 'u') {
	$d = $query_library->getData($idx, $table_name);
	$content = json_decode((string)($d['b_content'] ?? ''), true);
	if (!is_array($content)) {
		$content = [];
	}
} else {
	$d = $query_library->getColumn($table_name);
	$content = [];
}

$category = (string)($d['category'] ?? '1');
if ($category !== '1' && $category !== '2') {
	$category = '1';
}

// 금액 표시 — 숫자만 추출 후 세자리 콤마
function bank_format_price(string $value): string
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

$tx_datetime = $c($content, 'transaction_datetime');
$tx_datetime_input = '';
if ($tx_datetime !== '') {
	$tx_datetime_input = str_replace(' ', 'T', $tx_datetime);
	if (!str_contains($tx_datetime_input, 'T')) {
		$tx_datetime_input .= 'T00:00:00';
	}
}
?>
<form name="fwrite" method="post" action="./bank_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" style="margin:0px;">
	<fieldset<?= $view_only ? ' disabled' : '' ?> style="border:0;margin:0;padding:0;min-width:0;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">거래형태</td>
			<td class="td2">
				<select name="category" class="input_select">
					<option value="1" <?php if ($category === '1') { ?>selected<?php } ?>>출금</option>
					<option value="2" <?php if ($category === '2') { ?>selected<?php } ?>>입금</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">보낸분/받는분</td>
			<td class="td2">
				<input type="text" name="counterparty" class="input_text" value="<?= gh_h($c($content, 'counterparty')) ?>" style="width:400px;" maxlength="100" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">거래금액</td>
			<td class="td2">
				<input type="text" name="amount" class="input_text bank-amount" value="<?= gh_h(bank_format_price($c($content, 'amount'))) ?>" style="width:300px;" inputmode="numeric" autocomplete="off" required="required"> 원
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">거래점</td>
			<td class="td2">
				<input type="text" name="branch" class="input_text" value="<?= gh_h($c($content, 'branch')) ?>" style="width:300px;" maxlength="50">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">거래일시</td>
			<td class="td2">
				<input type="datetime-local" name="transaction_datetime" class="input_text" value="<?= gh_h($tx_datetime_input) ?>" style="width:240px;" step="1" required="required">
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='bank_list.php?<?= $func_library->queryString('idx,w') ?>'">목록</button>
				<?php } else { ?>
				<button type="submit" class="red_btn">확 인</button>
				<button type="button" class="gray_btn" onclick="javascript:window.location='bank_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
				<?php } ?>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
	// 금액 필드 — 제출 전 숫자만 남김
	function fwrite_submit(f) {
		document.querySelectorAll('.bank-amount').forEach(function(el) {
			el.value = String(el.value).replace(/[^\d.-]/g, '');
		});
		return true;
	}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>
