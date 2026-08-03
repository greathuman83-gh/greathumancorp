<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
?>
<style>
.mainList{cursor:pointer;}
.mainList li{display:inline-block;height:35px;line-height:30px;}
.mainList .img{width:60px;}
.mainList.on{background:#dddddd;}
.upDown{margin:5px;cursor:pointer;width:150px;}
.upDown:hover{text-decoration:underline;}
</style>

<!-- <form name="category_select_form" method="post" action="?<?=$func_library->queryString()?>">
<div class="categorySelect">
	<select name="cate" class="input_select">
		<option value="">선택</option>
		<?php
			//1차 분류
			$where = "where depth = 1 and category = 'main' ";
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
$table_name = 'gh_main_table';
$upload_directory = 'main';
$bind_param = array();
$where = " where page_type = :pageType";
$bind_param[] = array('pageType', $page_type);
$orderby = "num asc|title asc|idx desc";
$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,1000);
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<tr><td>총 클라이언트 수 : <?=$list_result['list_total'];?></td></tr>
</table>
<form name="fwrite" method="post" action="./main_sort_ok.php?<?=$func_library->queryString()?>w=u&table_name=<?=$table_name?>" onsubmit="return formSubmit(this);" enctype="multipart/form-data" style="margin:0px;">
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
			<div class="mainList" <?=$_style?>>
				<input type="hidden" name="idx[]" value="<?=$d['idx']?>">
				<ul>
					<li class="img"><?php if($d['file1']){?><img src="<?=$gh_path?>data/<?=$upload_directory?>/<?=$d['file1']?>" width="50"><?php }?></li>
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
	<button type="button" class="gray_btn" onclick="javascript:window.location='./main_sort.php?<?=$func_library->queryString()?>'">취 소</button>
</p>
</form>
<br>
<script type="text/javascript">
function formSubmit(){
	if (!window.confirm("위 순서대로 수정하시겠습니까?")){
		return false;
	}
}

// 메인 목록 순서 — 선택 후 upDown으로 DOM 재배치
document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var item = e.target.closest('.mainList');
		if (item) {
			document.querySelectorAll('.mainList').forEach(function (el) {
				el.classList.remove('on');
			});
			item.classList.add('on');
			return;
		}

		var upDown = e.target.closest('.upDown');
		if (!upDown) {
			return;
		}

		var selected = document.querySelector('.mainList.on');
		if (!selected) {
			alert('포트폴리오를 선택해 주세요.');
			return;
		}

		var items = Array.prototype.slice.call(document.querySelectorAll('.mainList'));
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