<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$tableName = 'gh_category_table';
$uploadDirectory = 'category';

$DB = new DBManager($conn);

$inputs['c_name'] = $c_name ??= null;
$inputs['c_open'] = $c_open ??= null;
$inputs['c_text1'] = $c_text1 ??= null;
$inputs['num'] = $num ??= 0;

if($w == 'a'){
	$inputs['category'] = $cate;

	if($depth == 1){
		$bindParam[] = array('cate',$cate);
		$d = $queryLibrary->getDataCustom($tableName,"c_code desc","c_code","where category =:cate and  depth=1",$bindParam);
		unset($bindParam);
		$c_code = $d?$d['c_code']+1:1;
		$c_code = sprintf("%03d", $c_code);
	}else{
		$count = ($depth-1)*3;
		$bindParam[] = array('parent', $parent);
		$bindParam[] = array('ccode', $ccode);
		$bindParam[] = array('depth', $depth);

		$d = $queryLibrary->getDataCustom($tableName,"c_code desc","c_code","where parent=:parent and substring(c_code,1,$count) = :ccode and depth=:depth",$bindParam);
		$c_code = $d?substr($d['c_code'],$count,3)+1:1;
		$c_code = sprintf("%03d", $c_code);
		$c_code = $ccode.$c_code;
		$inputs['parent'] = $parent;
	}

	$inputs['c_code'] = $c_code;
	$inputs['depth'] = $depth;
	

	$inputs['regdate'] = date("Y-m-d H:i:s");
	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		if($depth == 1){
			$idx = $conn->lastInsertId();
			
			$inputs = array();
			$inputs['parent'] = $idx;
			
			$where[] = array('idx', $idx,'and');
			if(!$DB->updateSet($tableName, $inputs, $where)){
				$funcLibrary->alert('문제가 발생하였습니다.');
			}
		}

		$funcLibrary->alert('등록되었습니다.','./category_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./category_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'd'){
	$where[] = array('idx', $idx);
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제 되었습니다.','./category_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>