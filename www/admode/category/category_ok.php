<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$tableName = 'gh_category_table';
$uploadDirectory = 'category';

$DB = new DBManager($conn);

if($w != 'd'){

	if(isset($delFile1)){
		@unlink($ghPath."data/$uploadDirectory/".$delFile1);
		$inputs['file1'] = '';
	}


	if(isset($_FILES['file1'])){
		$file = $_FILES['file1']['tmp_name'];
		$file_size = $_FILES["file1"]['size'];
		if($file && $file_size>0){
			@unlink($ghPath."data/$uploadDirectory/$oldFile1");
			$mfile = $funcLibrary->uploadFile('file1','',$ghPath.'data/'.$uploadDirectory);
			$inputs['file1'] = $mfile['filename'];
		}
	}

	$inputs['c_name'] = $c_name ?? null;
	$inputs['c_open'] = $c_open ?? null;
	$inputs['c_text1'] = $c_text1 ?? null;
}

if($w == 'a'){
	$inputs['category'] = $cate;
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if($depth == 1){
		$bindParam[] = array('cate',$cate);
		$d = $queryLibrary->getDataCustom($tableName,"c_code desc","c_code","where category =:cate and depth=1",$bindParam);
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
	$d = $queryLibrary->getData($idx,$tableName);
	
	//======== 하위 카테고리 확인 ===========
	$count = $d['depth']*3;

	$bindParam[] = array('parent', $d['parent']);
	$bindParam[] = array('c_code', $d['c_code']);
	$bindParam[] = array('depth', $d['depth']+1);

	$total = $queryLibrary->getDataCustom($tableName,'',"count(*) as total","where parent=:parent and substring(c_code,1,$count) = :c_code and depth=:depth",$bindParam);
	
	if($total['total'] >0){
		$funcLibrary->alert('하위 카테고리가 존재합니다.\\n하위 카테고리 삭제 후 다시 시도해 주세요.');
	}
	unset($bindParam);
	//===============================

	//======== 등록된 제품 확인 ============
	/*
	$bindParam[] = array('c_code',$d['c_code']);
	
	$total = $queryLibrary->getDataCustom("gh_product_table","","count(*) as total","where c_code = :c_code",$bindParam);
	
	if($total['total'] >0){
		$funcLibrary->alert("해당 브랜드에 등록된 제품이 존재합니다..\\n등록된 제품을 삭제하거나 변경하시고 시도해 주세요.");
		exit;
	}
	unset($bindParam);
	*/
	//===============================

	@unlink($ghPath."data/$uploadDirectory/".$d['file1']);
	$where[] = array('idx', $idx);
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('삭제 되었습니다.','./category_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>