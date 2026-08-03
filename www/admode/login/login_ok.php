<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';


$admin_where = " where language = :language and BINARY(a_id) = :a_id ";
$admin_bind_param = array();
$admin_bind_param[] = array('a_id', $a_id);
$admin_bind_param[] = array('language', LANGUAGE);
$admin_data = $query_library->getData2($admin_where,$admin_bind_param,'gh_admin');

$hash_pwd = hash('sha256',$a_pwd);

if ($admin_data){
	$data_pwd =$admin_data['a_pwd'];
	if ($hash_pwd==$data_pwd) {
		$_SESSION['admin_id'] = $admin_data['a_id'];
		$_SESSION['admin_name'] = $admin_data['a_name'];
		$_SESSION['admin_level'] = $admin_data['a_level'];
		$_SESSION['admin_super'] = $admin_data['super'];
		$_SESSION['admin_auth'] = $admin_data['a_authority'];
		$_SESSION['language'] = LANGUAGE;

		$func_library->gotoUrl('../member/manager_list.php?menu_code=001001');

	}else{
		$func_library->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
	}
}else{
	$func_library->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
}


include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>