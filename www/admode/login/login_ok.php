<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

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

		// 자동로그인 — 체크 시 1년 쿠키·DB 토큰 저장, 미체크 시 기존 쿠키만 유지
		if (isset($auto_login) && $auto_login === 'Y') {
			$auto_token = bin2hex(random_bytes(32));
			$inputs = [];
			$inputs['a_auto_login_token'] = hash('sha256', $auto_token);
			$where = [];
			$where[] = ['idx', $admin_data['idx'], 'and'];
			$DB->updateSet('gh_admin', $inputs, $where);

			setcookie('admin_auto_login', LANGUAGE . '|' . $admin_data['a_id'] . '|' . $auto_token, [
				'expires' => time() + (365 * 24 * 60 * 60),
				'path' => '/',
				'secure' => COOKIE_SECURE,
				'httponly' => true,
				'samesite' => 'Strict',
			]);
		}

		$func_library->gotoUrl('../member/manager_list.php?menu_code=001001');

	}else{
		$func_library->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
	}
}else{
	$func_library->alert($_pageText['정보가 일치하지 않습니다.'],'login.php');
}


include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>
