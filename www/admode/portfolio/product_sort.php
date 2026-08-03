<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
?>
<style>
.productList{cursor:pointer;}
.productList li{display:inline-block;height:45px;line-height:40px;}
.productList .img{width:60px;}
.productList.on{background:#dddddd;}
.upDown{margin:5px;cursor:pointer;width:150px;}
.upDown:hover{text-decoration:underline;}
.productList ul {
    display: flex;
    align-items: center; /* 세로 중앙 정렬 */
    gap: 10px; /* 이미지와 텍스트 사이 간격 */
}

.productList .img {
    display: flex;
    align-items: center; /* 이미지 세로 중앙 정렬 */
}

.productList .title {
    display: flex;
    align-items: center; /* 텍스트 세로 중앙 정렬 */
}
</style>

<!-- <form name="category_select_form" method="post" action="?<?=$func_library->queryString()?>">
<div class="categorySelect">
	<select name="cate" class="input_select">
		<option value="">선택</option>
		<?php
			//1차 분류
			$where = "where depth = 1 and category = 'product' ";
			$orderby = "num asc|c_code asc|idx desc";
			$list_result = $query_library->getList($where,$bind_param,'gh_category_table',$orderby,1,99);
			foreach($list_result['result'] as $cate_data){
			if($cate == $cate_data['c_code']){
				$selected = 'selected';
			}else{
				$selected = '';
			}
		?>
			<option value="<?=$cate_data['c_code']?>" <?=$selected?>><?=$cate_data['c_name']?></option>
		<?php }?>
	</select>
	<button type="submit" class="search_btn">검색</button>
</div>
</form> -->
<?php
$table_name = 'gh_product_table';
$upload_directory = 'product';
$bind_param = array();
$where = " where substring(c_code,1,3) = :pageType ";
$bind_param[] = array('pageType',$page_type);
$orderby = "num asc|idx desc";
$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,1000);
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<tr><td>총 제품 수 : <?=$list_result['list_total'];?></td></tr>
</table>
<form name="fwrite" method="post" action="./product_sort_ok.php?<?=$func_library->queryString()?>w=u&table_name=<?=$table_name?>" onsubmit="return formSubmit(this);" enctype="multipart/form-data" style="margin:0px;">
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="500"></col>
<col></col>
<tr><td colspan="10" class="line1"></td></tr>
<tr class="list col1">
	<td class="td2" valign="top">
		<div style="overflow-y:scroll;height:500px;">
			<?php
				$i = 1;
				foreach($list_result['result'] as $d){
				if(($d['p_open'] ??= null) != '1'){
					$_style = 'style="color:gray;"';
					$_openText = '';
					//$_openText = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [비공개]';
				}else{
					$_style = '';
					$_openText = '';
				}
			?>
			<div class="productList" <?=$_style?>>
				<input type="hidden" name="idx[]" value="<?=$d['idx']?>">
				<ul>
					<li class="img"><?php if($d['thumb_file']){?><img src="<?=$gh_path?>data/product/<?=$d['thumb_file']?>" width="50"><?php }?></li>
					<li class="title" style="vertical-align:middle;">[<?=$i?>]<?=$_openText?> <?=$d['title']?></li>
				</ul>
			</div>
			<?php $i++;}?>
		</div>
	</td>
	<td class="td2" valign="top">
		<div class="upDown" data-direction="up" data-number="max">↑ &nbsp;<strong>맨위로이동</strong></div>
		<div class="upDown" data-direction="up" data-number="20">↑ &nbsp;위로이동(+20)</div>
		<div class="upDown" data-direction="up" data-number="10">↑ &nbsp;위로이동(+10)</div>
		<div class="upDown" data-direction="up" data-number="5">↑ &nbsp;위로이동(+5)</div>
		<div class="upDown" data-direction="up" data-number="2">↑ &nbsp;위로이동(+2)</div>
		<div class="upDown" data-direction="up" data-number="1">↑ &nbsp;위로이동(+1)</div>
		<br>
		<div class="upDown" data-direction="down" data-number="1">↓ &nbsp;아래로이동(-1)</div>
		<div class="upDown" data-direction="down" data-number="2">↓ &nbsp;아래로이동(-2)</div>
		<div class="upDown" data-direction="down" data-number="5">↓ &nbsp;아래로이동(-5)</div>
		<div class="upDown" data-direction="down" data-number="10">↓ &nbsp;아래로이동(-10)</div>
		<div class="upDown" data-direction="down" data-number="20">↓ &nbsp;아래로이동(-20)</div>
		<div class="upDown" data-direction="down" data-number="max">↓ &nbsp;<strong>맨아래로이동</strong></div>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
</table>
<p align="center" style="margin-top:30px;width:980px;">
	<button type="submit" class="red_btn">확 인</button>
	<button type="button" class="gray_btn" onclick="javascript:window.location='./product_sort.php?<?=$func_library->queryString()?>'">취 소</button>
</p>
</form>
<br>
<script type="text/javascript">
function formSubmit(){
	if (!window.confirm("위 순서대로 수정하시겠습니까?")){
		return false;
	}
}

// 제품 목록 순서 — 선택 후 upDown으로 DOM 재배치
document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var item = e.target.closest('.productList');
		if (item) {
			document.querySelectorAll('.productList').forEach(function (el) {
				el.classList.remove('on');
			});
			item.classList.add('on');
			return;
		}

		var upDown = e.target.closest('.upDown');
		if (!upDown) {
			return;
		}

		var selected = document.querySelector('.productList.on');
		if (!selected) {
			alert('제품을 선택해 주세요.');
			return;
		}

		var items = Array.prototype.slice.call(document.querySelectorAll('.productList'));
		var selectIndex = items.indexOf(selected);
		var direction = upDown.dataset.direction;
		var number = upDown.dataset.number;
		var totalNumber = items.length - 1;
		var moveNumber;

		if (direction === 'up') {
			if (number === 'max') {
				items[0].parentNode.insertBefore(selected, items[0]);
			} else {
				moveNumber = selectIndex - Number(number);
				if (moveNumber <= 0) {
					items[0].parentNode.insertBefore(selected, items[0]);
				} else {
					items[moveNumber].parentNode.insertBefore(selected, items[moveNumber]);
				}
			}
		} else {
			if (number === 'max') {
				items[totalNumber].parentNode.insertBefore(selected, items[totalNumber].nextSibling);
			} else {
				moveNumber = selectIndex + Number(number);
				if (moveNumber > totalNumber) {
					items[totalNumber].parentNode.insertBefore(selected, items[totalNumber].nextSibling);
				} else {
					items[moveNumber].parentNode.insertBefore(selected, items[moveNumber].nextSibling);
				}
			}
		}
	});
});
</script>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php';?>