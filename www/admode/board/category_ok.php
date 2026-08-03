<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$table_name = 'gh_category_table';
$upload_directory = 'category';

$DB = new DBManager($conn);

$inputs['c_name'] = $c_name ??= null;
$inputs['c_open'] = $c_open ??= null;
$inputs['c_text1'] = $c_text1 ??= null;
$inputs['num'] = $num ??= 0;

if($w == 'a'){
	$inputs['category'] = $cate;

	if($depth == 1){
		$bind_param[] = array('cate',$cate);
		$d = $query_library->getDataCustom($table_name,"c_code desc","c_code","where category =:cate and  depth=1",$bind_param);
		unset($bind_param);
		$c_code = $d?$d['c_code']+1:1;
		$c_code = sprintf("%03d", $c_code);
	}else{
		$count = ($depth-1)*3;
		$bind_param[] = array('parent', $parent);
		$bind_param[] = array('ccode', $ccode);
		$bind_param[] = array('depth', $depth);

		$d = $query_library->getDataCustom($table_name,"c_code desc","c_code","where parent=:parent and substring(c_code,1,$count) = :ccode and depth=:depth",$bind_param);
		$c_code = $d?substr($d['c_code'],$count,3)+1:1;
		$c_code = sprintf("%03d", $c_code);
		$c_code = $ccode.$c_code;
		$inputs['parent'] = $parent;
	}

	$inputs['c_code'] = $c_code;
	$inputs['depth'] = $depth;
	

	$inputs['regdate'] = date("Y-m-d H:i:s");
	if(!$DB->insertInto($table_name, $inputs)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		if($depth == 1){
			$idx = $conn->lastInsertId();
			
			$inputs = array();
			$inputs['parent'] = $idx;
			
			$where[] = array('idx', $idx,'and');
			if(!$DB->updateSet($table_name, $inputs, $where)){
				$func_library->alert('문제가 발생하였습니다.');
			}
		}

		$func_library->alert('등록되었습니다.','./category_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($table_name, $inputs, $where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('수정되었습니다.','./category_list.php?'.$func_library->queryString('idx,w'));
	}

}else if($w == 'd'){
	$where[] = array('idx', $idx);
	if(!$DB->delete_db($table_name,$where)){
		$func_library->alert('문제가 발생하였습니다.');
	}else{
		$func_library->alert('삭제 되었습니다.','./category_list.php?'.$func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>