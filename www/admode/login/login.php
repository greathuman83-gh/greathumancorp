<?php
include_once __DIR__ . '/../../include/common/common.php';

// 이미 세션 로그인된 경우 — 로그인 폼 대신 관리자로 이동
if (!empty($admin_id)) {
	$func_library->gotoUrl('../member/manager_list.php?menu_code=001001');
}

// 카카오 로그인 CSRF — 콜백(kakao_login_ok.php)에서 대조
$_SESSION['kakao_oauth_state'] = bin2hex(random_bytes(16));

// 자동로그인 쿠키 — language|a_id|token 검증 후 세션 복구 (6개월 쿠키)
$auto_login_cookie = $_COOKIE['admin_auto_login'] ?? '';
if ($auto_login_cookie !== '') {
	$auto_parts = explode('|', $auto_login_cookie, 3);
	if (count($auto_parts) === 3) {
		[$cookie_language, $cookie_a_id, $cookie_token] = $auto_parts;
		if ($cookie_language !== '' && $cookie_a_id !== '' && preg_match('/^[a-f0-9]{64}$/', $cookie_token)) {
			$admin_where = ' where language = :language and BINARY(a_id) = :a_id ';
			$admin_bind_param = [];
			$admin_bind_param[] = ['a_id', $cookie_a_id];
			$admin_bind_param[] = ['language', $cookie_language];
			$admin_data = $query_library->getData2($admin_where, $admin_bind_param, 'gh_admin');
			$token_hash = hash('sha256', $cookie_token);
			if (
				$admin_data
				&& ($admin_data['a_auto_login_token'] ?? '') !== ''
				&& hash_equals((string) $admin_data['a_auto_login_token'], $token_hash)
			) {
				// 미승인 계정 — 자동로그인도 세션 복구하지 않음
				$is_super = (string) ($admin_data['super'] ?? '') === '1';
				if (!$is_super && ($admin_data['a_status'] ?? 'Y') !== 'Y') {
					setcookie('admin_auto_login', '', [
						'expires' => time() - 3600,
						'path' => '/',
						'secure' => COOKIE_SECURE,
						'httponly' => true,
						'samesite' => 'Strict',
					]);
					$func_library->alert($_pageText['해당 계정은 승인 대기중입니다.'], 'login.php');
				}

				$_SESSION['admin_id'] = $admin_data['a_id'];
				$_SESSION['admin_name'] = $admin_data['a_name'];
				$_SESSION['admin_level'] = $admin_data['a_level'];
				$_SESSION['admin_super'] = $admin_data['super'];
				$_SESSION['admin_auth'] = $admin_data['a_authority'];
				$_SESSION['language'] = $cookie_language;
				$func_library->gotoUrl('../member/manager_list.php?menu_code=001001');
			}
		}
	}
	// 쿠키 위조·만료 토큰 — 자동로그인 쿠키만 폐기
	setcookie('admin_auto_login', '', [
		'expires' => time() - 3600,
		'path' => '/',
		'secure' => COOKIE_SECURE,
		'httponly' => true,
		'samesite' => 'Strict',
	]);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">

<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $meta_tag_data['title'] ?? '' ?></title>
	<link rel="stylesheet" href="/include/css/css.css?<?= ADMIN_VERSION ?>" type="text/css">
	<script src="/include/js/common.js?<?= ADMIN_VERSION ?>"></script>
	<style>
		#wrap {
			background: url('/images/admin/login/admin_login_bg.gif') repeat;
			vertical-align: top;
			position: relative;
			padding-top: 80px;
		}

		.login-box .login-box-in .auto-login {
			width: 456px;
			margin: 12px auto 0;
			text-align: left;
			font-size: 14px;
			color: #555;
			box-sizing: border-box;
		}

		.login-box .login-box-in .auto-login label {
			cursor: pointer;
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}

		@media screen and (max-width: 767px) {
			#wrap {
				padding: 24px 12px 0;
				box-sizing: border-box;
			}

			.login-box {
				width: 100%;
				height: auto;
				box-sizing: border-box;
			}

			.login-box .login-box-in {
				width: 100%;
				height: auto;
				padding: 0 16px 24px;
				box-sizing: border-box;
			}

			.login-box .login-box-in .admin-logo {
				margin-top: 56px;
			}

			.login-box .login-box-in .admin-logo img {
				max-width: 100%;
				height: auto;
			}

			.login-box .login-box-in .login-info,
			.login-box .login-box-in .login-input,
			.login-box .login-box-in .auto-login,
			.login-box .login-box-in .submit-button,
			.login-box .login-box-in .kakao-login-button,
			.copyright {
				width: 100%;
				box-sizing: border-box;
			}

			.login-box .login-box-in .login-info {
				height: auto;
				padding: 16px 12px;
			}

			.login-box .login-box-in .login-input {
				padding-left: 12px;
				margin-top: 10px;
			}

			.copyright img {
				max-width: 120px;
				height: auto;
			}
		}
	</style>
</head>

<body leftmargin="0" topmargin="0">
	<div id="wrap">
		<form name="login" method="post" action="login_ok.php" onsubmit="return login_sendit(this)">
			<input type="hidden" name="language" value="<?= $language ?>">
			<div class="login-box">
				<div class="login-box-in">
					<div class="admin-logo"><img src="/images/admin/login/admin_logo_big.png" width="350"></div>
					<div class="login-info">
						<b><?= $_pageText['웹사이트 운영을 위한 관리자 모드입니다.'] ?></b><br>
						<?= $_pageText['아이디와 비밀번호를 입력하신 후 로그인해 주시기 바랍니다.'] ?>
					</div>
					<input type="text" class="login-input" name="a_id" id="a_id" placeholder="<?= $_pageText['아이디를 입력해 주세요.'] ?>"><br>
					<input type="password" class="login-input" name="a_pwd" id="a_pwd" placeholder="<?= $_pageText['비밀번호를 입력해 주세요.'] ?>"><br>
					<div class="auto-login">
						<label for="auto_login">
							<input type="checkbox" name="auto_login" id="auto_login" value="Y">
							<?= $_pageText['자동 로그인'] ?>
						</label>
					</div>
					<button type="submit" class="submit-button"><?= $_pageText['로그인'] ?></button>
					<button type="button" class="kakao-login-button" id="kakao-login-button"><?= $_pageText['카카오 로그인'] ?></button>
				</div>
			</div>
		</form>
		<form name="kakao_login" id="kakao-login-form" method="post" action="kakao_login_ok.php">
			<input type="hidden" name="kakao_access_token" id="kakao-access-token" value="">
			<input type="hidden" name="kakao_state" value="<?= gh_h($_SESSION['kakao_oauth_state']) ?>">
			<input type="hidden" name="auto_login" id="kakao-auto-login" value="">
		</form>

		<div class="copyright">
			<img src="/images/admin/login/admin_logo.png" width="80"> &nbsp;&nbsp;<span style="color:#b5b5b5">Copyright © Designpixel Corp. All Rights Reserved.</span>
		</div>

	</div>
	<script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
	<script type="text/javascript">
		// 로그인 검증 — 아이디·비밀번호 빈 값 차단
		function login_sendit() {
			var aId = document.getElementById('a_id');
			var aPwd = document.getElementById('a_pwd');

			if (aId.value == '') {
				alert("<?= $_pageText['아이디를 입력해 주세요.'] ?>");
				aId.focus();
				return false;
			}

			if (aPwd.value == '') {
				alert("<?= $_pageText['비밀번호를 입력해 주세요.'] ?>");
				aPwd.focus();
				return false;
			}
		}

		// 카카오 로그인 — JS SDK 팝업 후 access_token을 서버에서 재검증
		function kakaoLogin() {
			if (typeof Kakao === 'undefined') {
				alert("<?= $_pageText['카카오 로그인에 실패했습니다.'] ?>");
				return;
			}
			if (!Kakao.isInitialized()) {
				Kakao.init('<?= gh_h(KAKAO_API_JS_KEY) ?>');
			}
			if (typeof Kakao.Auth.login !== 'function') {
				alert("<?= $_pageText['카카오 로그인에 실패했습니다.'] ?>");
				return;
			}
			Kakao.Auth.login({
				success: function (authObj) {
					var token = authObj && authObj.access_token ? authObj.access_token : '';
					if (token === '') {
						alert("<?= $_pageText['카카오 로그인에 실패했습니다.'] ?>");
						return;
					}
					var autoLoginEl = document.getElementById('auto_login');
					document.getElementById('kakao-access-token').value = token;
					document.getElementById('kakao-auto-login').value = (autoLoginEl && autoLoginEl.checked) ? 'Y' : '';
					document.getElementById('kakao-login-form').submit();
				},
				fail: function () {
					alert("<?= $_pageText['카카오 로그인에 실패했습니다.'] ?>");
				}
			});
		}

		document.getElementById('kakao-login-button').addEventListener('click', kakaoLogin);
	</script>
</body>

</html>
<?php
include_once __DIR__ . '/../../include/common/dbclose.php';
?>
