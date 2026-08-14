<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$DB = new DBManager($conn);

$login_url = 'login.php';
$pending_msg = $_pageText['해당 계정은 승인 대기중입니다.'];

// CSRF — 로그인 화면에서 발급한 state와 불일치하면 중단
$kakao_state = (string) ($kakao_state ?? '');
$session_state = (string) ($_SESSION['kakao_oauth_state'] ?? '');
unset($_SESSION['kakao_oauth_state']);
if ($session_state === '' || $kakao_state === '' || !hash_equals($session_state, $kakao_state)) {
	$func_library->alert($_pageText['잘못된 방법으로 접근하셨습니다.'], $login_url);
}

$kakao_access_token = trim((string) ($kakao_access_token ?? ''));
if ($kakao_access_token === '' || strlen($kakao_access_token) > 512) {
	$func_library->alert($_pageText['카카오 로그인에 실패했습니다.'], $login_url);
}

// 카카오 사용자 조회 — 클라이언트 토큰을 서버에서 /v2/user/me 로 재검증
$ch = curl_init('https://kapi.kakao.com/v2/user/me');
curl_setopt_array($ch, [
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => [
		'Authorization: Bearer ' . $kakao_access_token,
		'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
	],
	CURLOPT_TIMEOUT => 10,
]);
$kakao_raw = curl_exec($ch);
$kakao_http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$kakao_me = is_string($kakao_raw) ? json_decode($kakao_raw, true) : null;
if ($kakao_http !== 200 || !is_array($kakao_me) || empty($kakao_me['id'])) {
	$func_library->alert($_pageText['카카오 로그인에 실패했습니다.'], $login_url);
}

$kakao_id = (string) $kakao_me['id'];
$kakao_name = '';
if (!empty($kakao_me['kakao_account']['profile']['nickname'])) {
	$kakao_name = (string) $kakao_me['kakao_account']['profile']['nickname'];
} elseif (!empty($kakao_me['properties']['nickname'])) {
	$kakao_name = (string) $kakao_me['properties']['nickname'];
}
if ($kakao_name === '') {
	$kakao_name = '카카오회원';
}
$kakao_name = mb_substr($kakao_name, 0, 20);
$kakao_email = '';
if (!empty($kakao_me['kakao_account']['email'])) {
	$kakao_email = mb_substr((string) $kakao_me['kakao_account']['email'], 0, 100);
}

$admin_where = ' where a_kakao_id = :a_kakao_id ';
$admin_bind_param = [];
$admin_bind_param[] = ['a_kakao_id', $kakao_id];
$admin_data = $query_library->getData2($admin_where, $admin_bind_param, 'gh_admin');

if ($admin_data) {
	// 미승인 — 슈퍼관리자만 상태와 무관하게 통과
	$is_super = (string) ($admin_data['super'] ?? '') === '1';
	if (!$is_super && ($admin_data['a_status'] ?? 'Y') !== 'Y') {
		$func_library->alert($pending_msg, $login_url);
	}

	$_SESSION['admin_id'] = $admin_data['a_id'];
	$_SESSION['admin_name'] = $admin_data['a_name'];
	$_SESSION['admin_level'] = $admin_data['a_level'];
	$_SESSION['admin_super'] = $admin_data['super'];
	$_SESSION['admin_auth'] = $admin_data['a_authority'];
	$_SESSION['language'] = LANGUAGE;
	$func_library->gotoUrl('../member/manager_list.php?menu_code=001001');
}

// 미가입 — 서브관리자·미승인으로 등록 후 로그인 차단
$inputs = [];
$inputs['language'] = LANGUAGE;
$inputs['a_id'] = 'kakao_' . $kakao_id;
$inputs['a_pwd'] = hash('sha256', bin2hex(random_bytes(32)));
$inputs['a_name'] = $kakao_name;
$inputs['a_email'] = $kakao_email;
$inputs['a_level'] = '2';
$inputs['super'] = '0';
$inputs['a_authority'] = '';
$inputs['a_kakao_id'] = $kakao_id;
$inputs['a_status'] = 'N';
$inputs['regdate'] = date('Y-m-d H:i:s');

$id_where = 'where a_id = :a_id';
$id_bind_param = [];
$id_bind_param[] = ['a_id', $inputs['a_id'], 'and'];
$id_total = $query_library->dataTotal($id_where, $id_bind_param, 'gh_admin');
if ($id_total > 0) {
	$inputs['a_id'] = 'kakao_' . $kakao_id . '_' . substr(bin2hex(random_bytes(4)), 0, 8);
}

try {
	$inserted = $DB->insertInto('gh_admin', $inputs);
} catch (PDOException $e) {
	$inserted = false;
}

if (!$inserted) {
	$func_library->alert($_pageText['문제가 발생했습니다.'], $login_url);
}

$func_library->alert($_pageText['회원가입이 완료되었습니다.'] . '\\n' . $pending_msg, $login_url);

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
