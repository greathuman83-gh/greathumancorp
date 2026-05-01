<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_admin';
$uploadDirectory = 'manager';

if(isset($delFile1)){
	@unlink($ghPath."data/manager/".$delFile1);
	$inputs['file1'] = '';
}

if(isset($_FILES['file1'])){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		$mfile = $funcLibrary->uploadFile('file1','',$ghPath.'data/'.$uploadDirectory);
		$inputs['file1'] = $mfile['filename'];
	}
}

if($adminSuper && isset($a_authority)){//메인관리자만 페이지 접근 권한 설정 가능
	$a_authority_array = '';
	for($i=0;$i<count((array)$a_authority);$i++){
		$a_authority_array .= $a_authority[$i].'|';
	}
	$a_authority_array = substr($a_authority_array,0,-1);
	$inputs['a_authority'] = $a_authority_array;
}

$inputs['a_name'] = $a_name ?? null;
$inputs['a_tel'] = $a_tel ?? null;
$inputs['a_hp'] = $a_hp ?? null;
$inputs['a_email'] = $a_email ?? null;
$inputs['a_data1'] = $a_data1 ?? null;
$inputs['a_data2'] = $a_data2 ?? null;
$inputs['a_data3'] = $a_data3 ?? null;
$inputs['a_data4'] = $a_data4 ?? null;

if(isset($a_level)){
	$inputs['a_level'] = $a_level;
}

if($a_pwd ??= null){
	$inputs['a_pwd'] = hash('sha256',$a_pwd);
}

if($w == 'a'){
	
	if(!$adminSuper){
		$funcLibrary->alert($_pageText['등록하실 권한이 없습니다.']);
	}

	$inputs['language'] = $_SESSION['language'];
	$inputs['a_id'] = $a_id;
	$inputs['regdate'] = date("Y-m-d H:i:s");

	$where = "where a_id = :a_id";
	$bindParam[] = array('a_id', $a_id,'and');
	$total = $queryLibrary->dataTotal($where,$bindParam,$tableName);

	if($total > 0){
		$funcLibrary->alert($_pageText['이미 존재하는 아이디 입니다.']);
	}

	if(!$DB->insertInto($tableName, $inputs)){
		$funcLibrary->alert($_pageText['문제가 발생했습니다.']);
	}else{
		$funcLibrary->alert($_pageText['등록 되었습니다.'],'./manager_list.php?'.$funcLibrary->queryString('idx,w'));
	}

}else if($w == 'u'){

	$d = $queryLibrary->getData($idx,$tableName);
	if(!$adminSuper && $adminId != $d['a_id']){
		$funcLibrary->alert($_pageText['수정하실 권한이 없습니다.']);
	}

	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert($_pageText['문제가 발생했습니다.']);
	}else{
		$funcLibrary->alert($_pageText['수정 되었습니다.'],'./manager_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}else if($w == 'd'){
	$d = $queryLibrary->getData($idx,$tableName);
	if($d['super'] == '1'){
		$funcLibrary->alert($_pageText['슈퍼관리자는 삭제하실 수 없습니다.']);
	}

	if(!$adminSuper && $adminId != $d['a_id']){
		$funcLibrary->alert($_pageText['삭제하실 권한이 없습니다.']);
	}

	$where[] = array('idx', $idx);
	if(!$DB->delete_db($tableName,$where)){
		$funcLibrary->alert($_pageText['문제가 발생했습니다.']);
	}else{
		@unlink($ghPath."data/manager/".$d['file1']);
		$funcLibrary->alert($_pageText['삭제 되었습니다.'],'./manager_list.php?'.$funcLibrary->queryString('idx,w'));
	}
}

include_once($ghPath.'include/common/dbclose.php');
?>