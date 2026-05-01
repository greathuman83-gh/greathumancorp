<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_history_table';

if($w == 'u'){
	$d = $queryLibrary->getData($idx,$tableName);
	$historyData = json_decode($d['content'],true); //내용
}else{
	$d = $queryLibrary->getColumn($tableName);
	$d['regdate'] = date('Y-m-d H:i:s');
}

$size = '(369 x 235)';
?>
<form name="fwrite" method="post" action="./history_ok.php?<?=$funcLibrary->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<table width="100%" align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
<tr><td colspan="2" class="line1"></td></tr>
<tr class="ht">
	<td class="td1">연도</td>
	<td class="td2">
		<input type="text" class="input_text" name="year" id="year" value="<?=$d['year']?>" style="width:100px;"> 년
	</td>
</tr>
<tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">항목 <button type="button"class="addCHistory red_icon_btn" data-max="20" data-position="">+</button></td>
	<td class="td2">
		<?php if(count($historyData['history'] ??= array()) > 0){?>
			<?php for($i=0;$i<count($historyData['history']);$i++){
				$itemImage = $historyData['history'][$i]['historyImage'] ?? '';
			?>
				<div class="addContentList historyContentList<?=$i === 0 ? ' first-history-item' : ''?>" <?=$itemImage ? ' data-history-image="'.htmlspecialchars($itemImage).'"' : ''?>>
					<div class="history-item-row">
						<input type="hidden" name="history[]">
						<input type="text" class="input_text" name="historyTitle[]" value="<?=$historyData['history'][$i]['historyTitle']?>" style="width:50px;" maxlength="2" placeholder="월">
						<textarea class="input_textarea" name="historyContent[]" style="width:600px;height:50px;" placeholder="내용"><?=$historyData['history'][$i]['historyContent']?></textarea>
						<div class="history-item-image">
							<input type="file" class="input_text historyImageInput" name="historyImage[]" accept="image/*" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
							<?php if($itemImage){?>
								<input type="hidden" name="old_historyImage[]" value="<?=htmlspecialchars($itemImage)?>">
								<span class="history-image-actions">
									<img src="<?=$ghPath?>data/history/<?=htmlspecialchars($itemImage)?>" class="historyItemPreview" width="200">
									<button type="button" class="historyImageDel gray_icon_btn">X</button>
								</span>
							<?php }else{?>
								<input type="hidden" name="old_historyImage[]" value="">
							<?php }?>
						</div>
						<?php if($i > 0){?>
							<div class="contentDelete"><button type="button" class="addHistoryDel black_icon_btn">항목 삭제</button></div>
						<?php }?>
					</div>
				</div>
			<?php }?>
		<?php }else{?>
			<div class="addContentList historyContentList first-history-item">
				<div class="history-item-row">
					<input type="hidden" name="history[]">
					<input type="text" class="input_text" name="historyTitle[]" value="" style="width:50px;" maxlength="2" placeholder="월">
					<textarea class="input_textarea" name="historyContent[]" style="width:600px;height:50px;" placeholder="내용"></textarea>
					<div class="history-item-image">
						<input type="file" class="input_text historyImageInput" name="historyImage[]" accept="image/*" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
						<input type="hidden" name="old_historyImage[]" value="">
					</div>
				</div>
			</div>
		<?php }?>
	</td>
</tr>
<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">이미지</td>
	<td class="td2">
			<div class="file_list">
				<input type="file" class="input_text" name="file1" onchange="imgFileCheck(this,<?=IMG_SIZE?>)"> <?=$size?>
				<?php if($d['file1']){?>
					<input type="hidden" name="old_file1" value="<?=$d["file1"]?>">
					<br>
					<img src="<?=$ghPath?>/data/history/<?=$d['file1']?>" width="200" style="margin-top:5px;">
					<input type="checkbox" name="del_file1" value="<?=$d['file1']?>"> 삭제
				<?php }?>
			</div>
	</td>
</tr> -->
<tr><td colspan="2" class="line3"></td></tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable" style="margin-top:30px;">
	<tr>
		<td align="center">
			<button type="submit" class="red_btn">확 인</button>
			<button type="button" class="gray_btn" onclick="javascript:window.location='history_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
//제품 정보 서브밋
function fwrite_submit(){
	if($('#year').val() == ''){
		alert('연도를 입력해 주세요.');
		return false;
	}
	return true;
}

$(function(){
	var $historyTd = $('form[name=fwrite][action*="history_ok"]').find('.addContentList').closest('td');
	if (!$historyTd.length) return;

	// 항목 추가: 캡처 단계에서 처리해 common.js보다 먼저 실행, 첫 행 템플릿 복사 후 모든 input 빈값
	$(document).on('click', '.addCHistory', function(e){
		if (!$(e.target).closest('.addCHistory').length) return;
		var $btn = $(e.target).closest('.addCHistory');
		if (!$btn.closest('form').length || $btn.closest('form').attr('action').indexOf('history_ok') === -1) return;
		e.stopImmediatePropagation();
		e.preventDefault();
		var $td = $btn.parents('td').next('td');
		var $firstRow = $td.find('.addContentList:first .history-item-row');
		if ($td.find('.addContentList').length >= 20) { alert('20개 까지만 가능합니다.'); return; }
		var copyContent = $firstRow.html();
		var $new = $('<div class="addContentList historyContentList">' +
			'<div class="history-item-row">' + copyContent +
			'<div class="contentDelete"><button type="button" class="addHistoryDel black_icon_btn">항목 삭제</button></div></div></div>');
		$td.append($new);
		$new.find('input[type="text"], textarea').val('');
		$new.find('input[name="old_historyImage[]"]').val('');
		$new.find('.historyImageInput').each(function(){ var $t=$(this); $t.replaceWith($t.clone(false)); });
		$new.find('.historyItemPreview, .history-image-actions').remove();
		$new.removeAttr('data-history-image');
		$new.find('input:checkbox').prop('checked', false);
	});

	// 항목 삭제 (첫 번째 항목은 삭제 불가)
	$(document).on('click', '.addHistoryDel', function(e){
		var $row = $(this).closest('.addContentList');
		if (!$row.closest('form[name=fwrite]').length || $row.closest('form').attr('action').indexOf('history_ok') === -1) return;
		if ($row.hasClass('first-history-item')) return;

		e.stopImmediatePropagation();
		if (!confirm('이 항목을 삭제하시겠습니까?')) return;
		var filename = $row.data('history-image');
		if (filename) {
			$.post('history_image_delete.php', { filename: filename }, function(){}, 'json').always(function(){
				$row.remove();
				window.dispatchEvent(new Event('resize'));
			});
		} else {
			$row.remove();
			window.dispatchEvent(new Event('resize'));
		}
		return false;
	});

	// 이미지만 삭제
	$(document).on('click', '.historyImageDel', function(e){
		var $wrap = $(this).closest('.history-item-image');
		var $row = $(this).closest('.addContentList');
		var filename = $row.data('history-image') || $wrap.find('input[name="old_historyImage[]"]').val();
		if (!filename || !confirm('첨부 이미지만 삭제합니다. 계속하시겠습니까?')) return;
		$.post('history_image_delete.php', { filename: filename }, function(){}, 'json').always(function(){
			$wrap.find('.historyItemPreview, .history-image-actions').remove();
			$wrap.find('input[name="old_historyImage[]"]').val('');
			$wrap.find('.historyImageInput').each(function(){ var $t=$(this); $t.replaceWith($t.clone(false)); });
			$row.removeAttr('data-history-image');
		});
	});

	// 이미지 선택 시 미리보기 + 이미지 삭제 버튼
	$(document).on('change', '.historyImageInput', function(){
		var $wrap = $(this).closest('.history-item-image');
		var file = this.files && this.files[0];
		$wrap.find('.historyItemPreview, .history-image-actions').remove();
		$wrap.find('input[name="old_historyImage[]"]').val('');
		if (file && file.type.indexOf('image') === 0) {
			var reader = new FileReader();
			reader.onload = function(e){
				var $actions = $('<span class="history-image-actions"></span>');
				$actions.append($('<img class="historyItemPreview" width="200">').attr('src', e.target.result));
				$actions.append($('<button type="button" class="historyImageDel gray_icon_btn">X</button>'));
				$wrap.append($actions);
			};
			reader.readAsDataURL(file);
		}
	});
});
</script>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>