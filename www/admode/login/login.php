<?php
include_once __DIR__ . '/../../include/common/common.php';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">

<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $meta_tag_data['title'] ?? '' ?></title>
	<link rel="stylesheet" href="/include/css/css.css?<?= ADMIN_VERSION ?>" type="text/css">
	<script src="/include/js/common.js?<?= ADMIN_VERSION ?>"></script>
</head>
<style>
	#wrap {
		background: url('/images/admin/login/admin_login_bg.gif') repeat;
		vertical-align: top;
		position: relative;
		padding-top: 80px;
	}

	@media screen and (max-width: 767px) {
		#wrap {
			padding: 24px 12px 0;
			box-sizing: border-box;
		}

		.login_box {
			width: 100%;
			height: auto;
			box-sizing: border-box;
		}

		.login_box .login_box_in {
			width: 100%;
			height: auto;
			padding: 0 16px 24px;
			box-sizing: border-box;
		}

		.login_box .login_box_in .admin_logo {
			margin-top: 56px;
		}

		.login_box .login_box_in .admin_logo img {
			max-width: 100%;
			height: auto;
		}

		.login_box .login_box_in .login_info,
		.login_box .login_box_in .login_input,
		.login_box .login_box_in .submit_button,
		.copyright {
			width: 100%;
			box-sizing: border-box;
		}

		.login_box .login_box_in .login_info {
			height: auto;
			padding: 16px 12px;
		}

		.login_box .login_box_in .login_input {
			padding-left: 12px;
			margin-top: 10px;
		}

		.copyright img {
			max-width: 120px;
			height: auto;
		}
	}
</style>

<body leftmargin="0" topmargin="0">
	<div id="wrap">
		<form name="login" method="post" action="login_ok.php" onsubmit="return login_sendit(this)">
			<input type="hidden" name="language" value="<?= $language ?>">
			<div class="login_box">
				<div class="login_box_in">
					<div class="admin_logo"><img src="/images/admin/login/admin_logo_big.png" width="350"></div>
					<div class="login_info">
						<b><?= $_pageText['웹사이트 운영을 위한 관리자 모드입니다.'] ?></b><br>
						<?= $_pageText['아이디와 비밀번호를 입력하신 후 로그인해 주시기 바랍니다.'] ?>
					</div>
					<input type="text" class="login_input" name="a_id" id="a_id" placeholder="<?= $_pageText['아이디를 입력해 주세요.'] ?>"><br>
					<input type="password" class="login_input" name="a_pwd" id="a_pwd" placeholder="<?= $_pageText['비밀번호를 입력해 주세요.'] ?>"><br>
					<button type="submit" class="submit_button"><?= $_pageText['로그인'] ?></button>
				</div>
			</div>
		</form>

		<div class="copyright">
			<img src="/images/admin/login/admin_logo.png" width="80"> &nbsp;&nbsp;<span style="color:#b5b5b5">Copyright © Designpixel Corp. All Rights Reserved.</span>
		</div>

	</div>
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
	</script>
</body>

</html>
<?php
include_once __DIR__ . '/../../include/common/dbclose.php';
?>
