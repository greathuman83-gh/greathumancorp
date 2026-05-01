<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');


$adminWhere = " where language = :language and BINARY(a_id) = :a_id ";
$adminBindParam = array();
$adminBindParam[] = array('a_id', $a_id);
$adminBindParam[] = array('language', LANGUAGE);
$adminData = $queryLibrary->getData2($adminWhere,$adminBindParam,'gh_admin');

$hashPwd = hash('sha256',$a_pwd);

if ($adminData){
	$dataPwd =$adminData['a_pwd'];
	if ($hashPwd==$dataPwd) {
		$_SESSION['adminId'] = $adminData['a_id'];
		$_SESSION['adminName'] = $adminData['a_name'];
		$_SESSION['adminLevel'] = $adminData['a_level'];
		$_SESSION['adminSuper'] = $adminData['super'];
		$_SESSION['adminAuth'] = $adminData['a_authority'];
		$_SESSION['language'] = LANGUAGE;

		$funcLibrary->gotoUrl('../member/manager_list.php?menuCode=001001');

	}else{
		$funcLibrary->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
	}
}else{
	$funcLibrary->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
}


include_once($ghPath.'include/common/dbclose.php');
?>