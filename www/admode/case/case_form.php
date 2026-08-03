<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_case_table';
$category_table = 'gh_category_table';

if ($w == 'u') {
	$d = $query_library->getData($idx, $table_name);
	$attach_files = explode('|', $d['attach_files']);
} else {
	//초기화
	$d = $query_library->getColumn($table_name);
	$attach_files = array();
}

$size = '(477 x 278)';
$size_detail = '(514 x 300)';
?>
<form name="fwrite" method="post" action="./case_ok.php?<?= $func_library->queryString() ?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table align="center" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<tr>
			<td colspan="2" class="line1"></td>
		</tr>
		<tr class="ht">
			<td class="td1">공개설정</td>
			<td class="td2">
				<input type="checkbox" name="c_open" value="1" <?php if ($d['c_open'] == '1' || $w == 'a') { ?>checked<?php } ?>> 공개
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">대표사례</td>
			<td class="td2">
				<input type="checkbox" name="c_main" value="1" <?php if ($d['c_main'] == '1') { ?>checked<?php } ?>> 설정
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">분류</td>
			<td class="td2">
				<select name="c_code" class="input_select" required="required">
					<option value="">- 전체 -</option>
					<?php
					$bind_param = array();
					$where = "where category = 'case' and depth = '1' ";
					$orderby = "num asc|c_code asc|idx desc";
					$list_result = $query_library->getList($where, '', $category_table, $orderby, 1, 99);
					foreach ($list_result['result'] as $cate_data) {
					?>
						<option value="<?= $cate_data['c_code'] ?>" <?php if ($d['c_code'] == $cate_data['c_code']) { ?>selected<?php } ?>><?= $cate_data['c_name'] ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">타이틀</td>
			<td class="td2">
				<input type="text" name="title" class="input_text" value="<?= $d['title'] ?>" style="width:700px;" required="required">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">시공사</td>
			<td class="td2">
				<input type="text" name="c_company" class="input_text" value="<?= $d['c_company'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">주소</td>
			<td class="td2">
				<input type="hidden" name="c_lat" id="apiLat" value="<?= $d['c_lat'] ?>">
				<input type="hidden" name="c_lng" id="apiLng" value="<?= $d['c_lng'] ?>">
				<input type="text" name="c_address" id="apiAddress" class="input_text" value="<?= $d['c_address'] ?>" style="width:700px;" required="required" readonly> <button type="button" class="black_icon_btn" onclick="kakao_postcode();">주소 찾기</button>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">상세주소</td>
			<td class="td2">
				<input type="text" name="c_address_detail" id="apiAddressDetail" class="input_text" value="<?= $d['c_address_detail'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">공사연도</td>
			<td class="td2">
				<input type="text" name="c_text1" class="input_text" value="<?= $d['c_text1'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">버팀보 간격</td>
			<td class="td2">
				<input type="text" name="c_text2" class="input_text" value="<?= $d['c_text2'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">버팀보 길이</td>
			<td class="td2">
				<input type="text" name="c_text3" class="input_text" value="<?= $d['c_text3'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">굴착깊이</td>
			<td class="td2">
				<input type="text" name="c_text4" class="input_text" value="<?= $d['c_text4'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">흙막이 벽체</td>
			<td class="td2">
				<input type="text" name="c_text5" class="input_text" value="<?= $d['c_text5'] ?>" style="width:700px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">평면규모</td>
			<td class="td2">
				<input type="text" name="c_text6" class="input_text" value="<?= $d['c_text6'] ?>" style="width:700px;">
			</td>
		</tr>
		<!-- <tr><td colspan="2" class="line2"></td></tr>
<tr class="ht">
	<td class="td1">제품 사양 <button type="button"class="addMultiContent red_icon_btn" data-max="20">+</button></td>
	<td class="td2">
		<?php if (count($spec_data['spec'] ??= array()) > 0) { ?>
			<?php
			for ($i = 0; $i < count((array)$spec_data['spec']); $i++) {
				$spec_data['spec'][$i]['specContent'] = html_entity_decode($spec_data['spec'][$i]['specContent']);
			?>
				<div class="addContentList">
					<input type="hidden" name="spec[]">
					<input type="text" name="spec_part[]" class="input_text" value="<?= $spec_data['spec'][$i]['specPart'] ?>" style="width:200px;" placeholder="구분">
					<input type="text" name="spec_content[]" class="input_text" value="<?= $spec_data['spec'][$i]['specContent'] ?>" style="width:600px;" placeholder="사양">
					<?php if ($i > 0) { ?>
						<div class="contentDelete"><button type="button"class="addContentDel gray_icon_btn">-</button></div>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="addContentList">
				<input type="hidden" name="spec[]">
				<input type="text" name="spec_part[]" class="input_text" style="width:200px;" placeholder="구분">
				<input type="text" name="spec_content[]" class="input_text" style="width:600px;" placeholder="사양">
			</div>
		<?php } ?>
	</td>
</tr> -->
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">이미지</td>
			<td class="td2">
				<input type="file" class="input_text" name="thumb_file" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $size ?>
				<?php if ($d['thumb_file']) { ?>
					<input type="hidden" name="old_thumb_file" value="<?= $d['thumb_file'] ?>">
					<br>
					<img src="<?= $gh_path ?>data/case/<?= $d['thumb_file'] ?>" width="200" style="margin-top:5px;">
					<input type="checkbox" name="del_thumb_file" value="<?= $d['thumb_file'] ?>"> 삭제
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="line2"></td>
		</tr>
		<tr class="ht">
			<td class="td1">상세 이미지 <!-- <button type="button" class="add_file red_icon_btn">+</button> --></td>
			<td class="td2">
				<?php if (array_filter($attach_files) != []) { ?>
					<?php for ($i = 0; $i < count((array)$attach_files); $i++) { ?>
						<div class="fileList">
							<input type="hidden" name="old_file[]" value="<?= $attach_files[$i] ?>">
							<input type="hidden" name="old_file_name[]" value="<?= $attach_files_name[$i] ?>">
							<input type="file" class="input_text" name="attach_files[]" class="attachFiles" onchange="imgFileCheck(this,<?= IMG_SIZE ?>)"> <?= $size_detail ?>
							<?php if ($attach_files[$i]) { ?>
								<br>
								<img src="<?= $gh_path ?>data/case/<?= $attach_files[$i] ?>" width="200" style="margin-top:5px;">
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
				<button type="button" class="gray_btn" onclick="javascript:window.location='case_list.php?<?= $func_library->queryString('idx,w') ?>'">취 소</button>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=<?= NCP_MAPS_CLIENTID ?>&submodules=geocoder"></script>
<script src="https://t1.kakaocdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
	function kakao_postcode() {
		new daum.Postcode({
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분임

				// 각 주소의 노출 규칙에 따라 주소를 조합함
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 함
				var fullAddr = ''; // 최종 주소 변수임
				var extraAddr = ''; // 조합형 주소 변수임

				// 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져옴
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우임
					fullAddr = data.roadAddress;
				} else { // 사용자가 지번 주소를 선택했을 경우임(J)
					fullAddr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 조합함
				if (data.userSelectedType === 'R') {
					// 법정동명이 있을 경우 추가함
					if (data.bname !== '') {
						extraAddr += data.bname;
					}
					// 건물명이 있을 경우 추가함
					if (data.buildingName !== '') {
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
				}

				// 우편번호와 주소 정보를 해당 필드에 넣음
				document.getElementById("apiAddress").value = fullAddr;

				// =================================================================
				// 여기서부터 NCP 지오코딩 API를 호출하여 위경도를 구하는 로직 추가함
				// =================================================================
				naver.maps.Service.geocode({
					query: fullAddr // 카카오 우편번호 API로 얻은 최종 주소 넘김
				}, function(status, response) {
					// API 응답 상태가 정상이 아니면 에러 알림 띄움
					if (status !== naver.maps.Service.Status.OK) {
						return alert('주소의 위경도 변환에 실패함.');
					}

					var result = response.v2; // 검색 결과 컨테이너임
					var items = result.addresses; // 검색된 주소 배열임

					// 결과가 하나 이상 존재하는지 확인함
					if (items.length > 0) {
						var lng = parseFloat(items[0].x); // 경도임
						var lat = parseFloat(items[0].y); // 위도임

						//console.log("추출된 위도(lat): " + lat + ", 경도(lng): " + lng);
						document.getElementById("apiLat").value = lat;
						document.getElementById("apiLng").value = lng;
					} else {
						console.log("해당 주소에 대한 위경도 좌표를 찾을 수 없음.");
					}
				});
				// =================================================================

				// 커서를 상세주소 필드로 이동함
				document.getElementById("apiAddressDetail").focus();
			}
		}).open();
	}

	document.addEventListener('DOMContentLoaded', function() {
		// 상세이미지 행 추가 — 최대 10개
		document.addEventListener('click', function(e) {
			var addBtn = e.target.closest('.add_file');
			if (addBtn) {
				var count = 10;
				var fileLists = document.querySelectorAll('.fileList');
				if (fileLists.length >= count) {
					alert("상세이미지는 " + count + "개까지 등록하실 수 있습니다.");
					return;
				}
				var sizeTxt = <?= json_encode((string)($size_detail ?? ''), JSON_UNESCAPED_UNICODE) ?>;
				var data = '<div class="fileList"><input type="file" class="input_text" name="attach_files[]" > ' + sizeTxt + ' <button type="button" class="del_file red_icon_btn">-</button></div>';
				fileLists[fileLists.length - 1].insertAdjacentHTML('afterend', data);
				return;
			}

			var delBtn = e.target.closest('.del_file');
			if (delBtn) {
				if (document.querySelectorAll('.fileList').length < 2) {
					alert("더이상 삭제하실 수 없습니다.");
					return;
				}
				if (window.confirm("삭제하시겠습니까?")) {
					var row = delBtn.closest('.fileList');
					if (row) row.remove();
				}
				return;
			}

			// 옵션 라디오 — 선택값을 hidden.optionRadioData에 반영
			var optionEl = e.target.closest('.optionSelect');
			if (optionEl) {
				var parent = optionEl.parentElement;
				var hidden = parent ? parent.querySelector('.optionRadioData') : null;
				if (hidden) hidden.value = optionEl.value;
			}
		});
	});

	function fwrite_submit(f) {
		return true;
	}
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>