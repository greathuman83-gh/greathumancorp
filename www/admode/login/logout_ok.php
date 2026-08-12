<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

// 자동로그인 해제 — 로그아웃 시 DB 토큰·쿠키 모두 폐기
if (!empty($admin_id)) {
	$DB = new DBManager($conn);
	$admin_where = ' where language = :language and BINARY(a_id) = :a_id ';
	$admin_bind_param = [];
	$admin_bind_param[] = ['a_id', $admin_id];
	$admin_bind_param[] = ['language', LANGUAGE];
	$admin_data = $query_library->getData2($admin_where, $admin_bind_param, 'gh_admin');
	if ($admin_data) {
		$inputs = [];
		$inputs['a_auto_login_token'] = '';
		$where = [];
		$where[] = ['idx', $admin_data['idx'], 'and'];
		$DB->updateSet('gh_admin', $inputs, $where);
	}
}

setcookie('admin_auto_login', '', [
	'expires' => time() - 3600,
	'path' => '/',
	'secure' => COOKIE_SECURE,
	'httponly' => true,
	'samesite' => 'Strict',
]);

session_destroy();
$func_library->gotoUrl(ADMIN_DIR.'/index.php');
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>
