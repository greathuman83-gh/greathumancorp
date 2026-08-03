<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';

$table_name = 'gh_admin_menu_table';

$bind_param = array();
$count = ($depth-1)*3;
$where = "where parent = :parent and substring(m_code,1,$count) = :m_code  and depth = :depth";
$bind_param[] = array('parent',$parent);
$bind_param[] = array('m_code',$code);
$bind_param[] = array('depth',$depth);
$orderby = "num asc|m_code asc|idx desc";
$list_result = $query_library->getList($where,$bind_param,$table_name,$orderby,1,99);

if($list_result['list_total'] > 0){
?>
	<button type="button" class="depth<?=$depth?>_icon_btn"><?=$depth?>차 메뉴</button>
	<ul id="sortable<?=$depth?>" class="sortable">
		<?php foreach($list_result['result'] as $d){?>
			<li class="ui-state-default" data-depth="<?=$depth+1?>" data-parent="<?=$d['parent']?>" data-code="<?=$d['m_code']?>">
				<div class="s_con"><input type="hidden" name="sort_num<?=$depth?>[]" value="<?=$d['idx']?>"><?=$d['m_name']?></div>
				<div class="s_con"></div>
			</li>
		<?php }?>
	</ul>
<?php }else{?>
등록된 메뉴가 없습니다.
<?php }?>