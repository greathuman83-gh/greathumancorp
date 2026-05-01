<?php
$ghPath = '../../';
include_once($ghPath."include/common/common.php");
include_once($ghPath."include/common/permission.php");

$tableName = 'gh_admin_menu_table';

$bindParam = array();
$count = ($depth-1)*3;
$where = "where parent = :parent and substring(m_code,1,$count) = :m_code  and depth = :depth";
$bindParam[] = array('parent',$parent);
$bindParam[] = array('m_code',$code);
$bindParam[] = array('depth',$depth);
$orderby = "num asc|m_code asc|idx desc";
$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,1,99);

if($listResult['listTotal'] > 0){
?>
	<button type="button" class="depth<?=$depth?>_icon_btn"><?=$depth?>차 메뉴</button>
	<ul id="sortable<?=$depth?>" class="sortable">
		<?php foreach($listResult['result'] as $d){?>
			<li class="ui-state-default" data-depth="<?=$depth+1?>" data-parent="<?=$d['parent']?>" data-code="<?=$d['m_code']?>">
				<div class="s_con"><input type="hidden" name="sort_num<?=$depth?>[]" value="<?=$d['idx']?>"><?=$d['m_name']?></div>
				<div class="s_con"></div>
			</li>
		<?php }?>
	</ul>
<?php }else{?>
등록된 메뉴가 없습니다.
<?php }?>