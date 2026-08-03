<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$table_name = 'gh_admin_menu_table';

$DB = new DBManager($conn);

$inputs['m_name'] = $m_name?? '';
$inputs['m_code_name'] = $m_code_name ?? '';
$inputs['m_open'] = $m_open ?? '';
$inputs['m_link'] = $m_link ?? '';
$inputs['m_link_target'] = $m_link_target ?? '';
$inputs['m_link_type'] = $m_link_type ?? '';

if($w == 'a'){
	$inputs['language'] = LANGUAGE;

	if($depth == 1){
		$bind_param[] = array('language',LANGUAGE);
		$d = $query_library->getDataCustom($table_name,"m_code desc","m_code","where language = :language and  depth=1",$bind_param);
		unset($bind_param);
		$m_code = $d['m_code']+1;
		$m_code = sprintf("%03d", $m_code);
	}else{
		$count = ($depth-1)*3;
		$bind_param[] = array('parent', $parent);
		$bind_param[] = array('mcode', $mcode);
		$bind_param[] = array('depth', $depth);

		$d = $query_library->getDataCustom($table_name,"m_code desc","m_code","where parent = :parent and substring(m_code,1,$count) = :mcode and depth=:depth",$bind_param);
		$m_code = $d?substr($d['m_code'],$count,3)+1:1;
		$m_code = sprintf("%03d", $m_code);
		$m_code = $mcode.$m_code;
		$inputs['parent'] = $parent;
	}

	$inputs['m_code'] = $m_code;
	$inputs['depth'] = $depth;
	

	$inputs['regdate'] = date("Y-m-d H:i:s");
	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		if($depth == 1){
			$idx = $conn->lastInsertId();
			$sql = "update $table_name set parent = :parent where idx = :idx";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':parent',$idx);
			$stmt->bindParam(':idx',$idx);
			$stmt->execute();
		}

		$func_library->alert('등록되었습니다.','menu_list.php?#$m_code');
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name, $inputs, $where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','menu_list.php?');
	}

}else if($w == 'd'){
	$d = $query_library->getData($idx,$table_name);
	//======== 하위 카테고리 확인 ===========
	$count = $d['depth']*3;

	$bind_param[] = array('parent', $d['parent']);
	$bind_param[] = array('m_code', $d['m_code']);
	$bind_param[] = array('depth', $d['depth']+1);

	$total = $query_library->getDataCustom($table_name,'',"count(*) as total","where parent=:parent and substring(m_code,1,$count) = :m_code and depth=:depth",$bind_param);
	
	if($total['total'] >0){
		$func_library->alert('하위 카테고리가 존재합니다.\\n하위 카테고리 삭제 후 다시 시도해 주세요.');
	}
	unset($bind_param);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('삭제 되었습니다.','menu_list.php?');
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>