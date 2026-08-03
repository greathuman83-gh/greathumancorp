<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_category_table';
?>
<script type="text/javascript">
// 분류 정렬 — ghInitSortable + 하위 depth AJAX 로드
document.addEventListener('DOMContentLoaded', function () {
	ghInitSortable('#sortable');

	document.addEventListener('click', function (e) {
		var li = e.target.closest('li');
		if (!li || !li.dataset.depth) {
			return;
		}
		var depth = li.dataset.depth;
		var code = li.dataset.code || '';
		var parent = li.dataset.parent || '';
		if (!depth || Number(depth) > 3) {
			return;
		}

		var body = new URLSearchParams();
		body.set('parent', parent);
		body.set('depth', depth);
		body.set('code', code);

		fetch('./depth_select.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString()
		})
		.then(function (res) { return res.text(); })
		.then(function (data) {
			var target = document.querySelector('.categoryDepth' + depth);
			if (depth === '2') {
				document.querySelectorAll('.categoryDepth2, .categoryDepth3').forEach(function (el) {
					el.innerHTML = '';
				});
			} else if (target) {
				target.innerHTML = '';
			}
			if (data && target) {
				target.innerHTML = data;
				ghInitSortableIn(target);
			}
		});
	});
});
</script>
<style>
.s_con{display:inline-block;vertical-align:middle;}
.sortable li{ margin: 0 5px 5px 0px; padding: 5px;width:230px;cursor:pointer;}
.ui-state-default{background:#eeeeee;}
.ui-state-highlight{background-color:#ffd3b7;height:25px;width:230px;line-height:25px;border:1px solid #ff9d5b;}
.sort_area{width:770px;height:550px;overflow:auto;}
.categorySort{display: inline-block;vertical-align:top;}
button{margin-bottom:10px;text-align:center;}
</style>

<form name="sort_frm" method="post" action="./category_sort_ok.php?<?=$func_library->queryString()?>" onsubmit="return fwrite_submit(this);" enctype="multipart/form-data" style="margin:0px;">
<div class="sort_area">
	<div class="categorySort">
		<button type="button" class="depth1_icon_btn">1차 분류</button>
		<ul id="sortable" class="sortable">
			<?php
				$bind_param = array();
				$where = "where depth='1' and category=:cate ";
				$bind_param[] = array('cate',$cate);
				$orderby = "num asc|c_code asc|idx desc";
				$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,999);
				$number = $list_result['number'];
				foreach($list_result['result'] as $d){
			?>
			<li class="ui-state-default" data-depth="2" data-parent="<?=$d['parent']?>" data-code="<?=$d['c_code']?>">
				<div class="s_con"><input type="hidden" name="sort_num[]" value="<?=$d['idx']?>"><?=$d['c_name']?></div>
				<div class="s_con"></div>
			</li>
			<?php }?>
		</ul>
	</div>
	<div class="categorySort categoryDepth2">
	</div>

	<div class="categorySort categoryDepth3">
	</div>
</div>
<p align="center" style="margin-top:50px;width:700px;">
	<a href="javascript:;" onclick="sort_chk();"><button type="button" class="red_btn">저 장</button></a>
	<button type="button" class="gray_btn" onclick="javascript:window.location='./category_sort.php?menu_code=<?=$menu_code?>&cate=<?=$cate?>'">취 소</button>
</p>
</form>

<script type="text/javascript">
	function sort_chk(){
		if (window.confirm("지금 순서로 수정하시겠습니까?"))
		{
			document.sort_frm.submit();
		}
	}
</script>
<?php include __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php';?>