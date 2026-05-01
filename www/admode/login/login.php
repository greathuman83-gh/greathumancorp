<?php
$ghPath = '/../../';
include_once __DIR__ . $ghPath . 'include/common/common.php';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">

<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $metaTagData['title'] ?? '' ?></title>
	<link rel="stylesheet" href="<?= $ghPath ?>include/css/css.css?<?= ADMIN_VERSION ?>" type="text/css">
	<script src="<?= $ghPath ?>include/js/jquery.min.js"></script>
	<script src="<?= $ghPath ?>include/js/common.js?<?= ADMIN_VERSION ?>"></script>
</head>
<style>
	#wrap {
		background: url('<?= $ghPath ?>images/admin/login/admin_login_bg.gif') repeat;
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
					<div class="admin_logo"><img src="<?= $ghPath ?>images/admin/login/admin_logo_big.png" width="350"></div>
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
			<img src="<?= $ghPath ?>images/admin/login/admin_logo.png" width="80"> &nbsp;&nbsp;<span style="color:#b5b5b5">Copyright © Designpixel Corp. All Rights Reserved.</span>
		</div>

	</div>
	<script type="text/javascript">
		function login_sendit() {
			if ($("#a_id").val() == "") {
				alert("<?= $_pageText['아이디를 입력해 주세요.'] ?>");
				$("#a_id").focus();
				return false;
			}

			if ($("#a_pwd").val() == "") {
				alert("<?= $_pageText['비밀번호를 입력해 주세요.'] ?>");
				$("#a_pwd").focus();
				return false;
			}
		}
	</script>
</body>

</html>
<?php
include_once __DIR__ . $ghPath . 'include/common/dbclose.php';
?>