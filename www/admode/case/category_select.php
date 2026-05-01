<?php
$ghPath = '../../';
include_once($ghPath."include/common/common.php");
include_once($ghPath."include/common/permission.php");

$tableName = 'gh_category_table';

$bindParam = array();
$count = ($depth-1)*3;
$where = "where parent = :parent and substring(c_code,1,$count) = :c_code  and depth = :depth";
$bindParam[] = array('parent',$parent);
$bindParam[] = array('c_code',$code);
$bindParam[] = array('depth',$depth);
$orderby = "num asc|c_code asc|idx desc";
$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,1,999);

if($listResult['listTotal'] > 0){
?>
	<button type="button" class="depth<?=$depth?>_icon_btn"><?=$depth?>차 분류</button>
	<ul id="sortable<?=$depth?>" class="sortable">
		<?php
			foreach($listResult['result'] as $d){
		?>
			<li class="ui-state-default" data-depth="<?=$depth+1?>" data-parent="<?=$d['parent']?>" data-code="<?=$d['c_code']?>">
				<div class="s_con"><input type="hidden" name="sort_num<?=$depth?>[]" value="<?=$d['idx']?>"><?=$d['c_name']?></div>
				<div class="s_con"></div>
			</li>
		<?php }?>
	</ul>
<?php }else{?>
등록된 분류가 없습니다.
<?php }?>