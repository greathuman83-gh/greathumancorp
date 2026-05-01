<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$tableName = 'gh_admin_menu_table';

$DB = new DBManager($conn);

$inputs['m_name'] = $m_name?? '';
$inputs['m_codeName'] = $m_codeName ?? '';
$inputs['m_open'] = $m_open ?? '';
$inputs['m_link'] = $m_link ?? '';
$inputs['m_link_target'] = $m_link_target ?? '';
$inputs['m_link_type'] = $m_link_type ?? '';

if($w == 'a'){
	$inputs['language'] = LANGUAGE;

	if($depth == 1){
		$bindParam[] = array('language',LANGUAGE);
		$d = $queryLibrary->getDataCustom($tableName,"m_code desc","m_code","where language = :language and  depth=1",$bindParam);
		unset($bindParam);
		$m_code = $d['m_code']+1;
		$m_code = sprintf("%03d", $m_code);
	}else{
		$count = ($depth-1)*3;
		$bindParam[] = array('parent', $parent);
		$bindParam[] = array('mcode', $mcode);
		$bindParam[] = array('depth', $depth);

		$d = $queryLibrary->getDataCustom($tableName,"m_code desc","m_code","where parent = :parent and substring(m_code,1,$count) = :mcode and depth=:depth",$bindParam);
		$m_code = $d?substr($d['m_code'],$count,3)+1:1;
		$m_code = sprintf("%03d", $m_code);
		$m_code = $mcode.$m_code;
		$inputs['parent'] = $parent;
	}

	$inputs['m_code'] = $m_code;
	$inputs['depth'] = $depth;
	

	$inputs['regdate'] = date("Y-m-d H:i:s");
	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		if($depth == 1){
			$idx = $conn->lastInsertId();
			$sql = "update $tableName set parent = :parent where idx = :idx";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':parent',$idx);
			$stmt->bindParam(':idx',$idx);
			$stmt->execute();
		}

		$funcLibrary->alert('등록되었습니다.','menu_list.php?#$m_code');
	}

}else if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName, $inputs, $where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','menu_list.php?');
	}

}else if($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);
	//======== 하위 카테고리 확인 ===========
	$count = $d['depth']*3;

	$bindParam[] = array('parent', $d['parent']);
	$bindParam[] = array('m_code', $d['m_code']);
	$bindParam[] = array('depth', $d['depth']+1);

	$total = $queryLibrary->getDataCustom($tableName,'',"count(*) as total","where parent=:parent and substring(m_code,1,$count) = :m_code and depth=:depth",$bindParam);
	
	if($total['total'] >0){
		$funcLibrary->alert('하위 카테고리가 존재합니다.\\n하위 카테고리 삭제 후 다시 시도해 주세요.');
	}
	unset($bindParam);

	$where[] = array('idx', $idx,'');
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제 되었습니다.','menu_list.php?');
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>