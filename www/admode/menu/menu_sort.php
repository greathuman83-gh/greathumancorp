<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');
?>
<script type="text/javascript">
$( function() {
	$("#sortable" ).sortable({
		placeholder: "ui-state-highlight"
	});
	$( "#sortable" ).disableSelection();

	$(document).on("click","li",function(){
		var depth = $(this).data("depth");
		var code = $(this).data("code");
		var parent = $(this).data("parent");
		if(!depth || depth > 3){
			return;
		}
		$.ajax({ 
			type: "POST", 
			url: "depthSelect.php", 
			data: "parent="+parent+"&depth="+depth+"&code="+code,
			success: function(data){ 
				if(data){
					if(depth=="2"){//1차 카테고리 선택시 모든 하위 분류 초기화
						$(".categoryDepth2,.categoryDepth3").html('');
					}else{
						$(".categoryDepth"+depth+"").html('');
					}
					$(".categoryDepth"+depth+"").html(data);

					$("#sortable"+depth+"" ).sortable({
						placeholder: "ui-state-highlight"
					});
					$( "#sortable"+depth+"" ).disableSelection();
				}else{
					if(depth=="2"){//1차 카테고리 선택시 모든 하위 분류 초기화
						$(".categoryDepth2,.categoryDepth3").html('');
					}else{
						$(".categoryDepth"+depth+"").html('');
					}
				}
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

<form name="sort_frm" method="post" action="menu_sort_ok.php">
<input type="hidden" name="cate" value="<?=$cate?>">
<div class="sort_area">
	<div class="categorySort">
		<button type="button" class="depth1_icon_btn">1차 분류</button>
		<ul id="sortable" class="sortable">
			<?php
				$bindParam = array();
				$where = "where depth = '1' ";
				$orderby = "num asc|m_code asc|idx desc";
				$listResult = $queryLibrary->getList($where,$bindParam,'gh_admin_menu_table',$orderby,1,999);
				$number = $listResult['number'];
				foreach($listResult['result'] as $d){
			?>
			<li class="ui-state-default" data-depth="2" data-parent="<?=$d['parent']?>" data-code="<?=$d['m_code']?>">
				<div class="s_con"><input type="hidden" name="sort_num[]" value="<?=$d['idx']?>"><?=$d['m_name']?></div>
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
	<button type="button" class="gray_btn" onclick="javascript:window.location='menu_list.php?<?=$funcLibrary->queryString('idx,w')?>'">취 소</button>
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
<?php include $ghPath.'include/html/admin_bottom.php';?>