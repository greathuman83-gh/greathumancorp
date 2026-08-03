<?php
include_once __DIR__ . '/../common/common.php';
include_once __DIR__ . '/../common/permission.php';
$menu_table_name = 'gh_admin_menu_table'; //메뉴 DB
$admin_auth_array = explode('|', $admin_auth);

if (!isset($menu_code)) {
	$menu_code = null;
}

$category_table = 'gh_category_table'; //카테고리 테이블
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">

<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $meta_tag_data['title'] ?? '' ?></title>
	<link rel="stylesheet" href="/include/css/css.css?<?= ADMIN_VERSION ?>" type="text/css">
	<script src="/include/js/common.js?<?= ADMIN_VERSION ?>"></script>
	<script src="/include/js/admin-menu.js?<?= ADMIN_VERSION ?>"></script>
	<script src="/include/js/admin-sortable.js?<?= ADMIN_VERSION ?>"></script>
</head>

<body leftmargin="0" topmargin="0">
	<script type="text/javascript">
		// 관리자 날짜 입력 — native type=date (yyyy-mm-dd)
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('input.date').forEach(function(el) {
				if (el.type === 'text') {
					el.type = 'date';
				}
			});
		});
	</script>
	<div id="bg-black"></div>
	<div id="bg"></div>
	<div id="wrap">
		<div id="header">
			<div class="head">
				<button type="button" class="mobile-gnb-toggle" aria-label="메뉴 열기" aria-controls="admin-main-menu" aria-expanded="false">
					<span></span>
					<span></span>
					<span></span>
				</button>
				<ul>
					<li><img src="/images/admin/main/admin_logo.png">&nbsp;&nbsp;&nbsp;<div>|&nbsp;&nbsp;&nbsp;웹 통합 관리 시스템 <?= ADMIN_VERSION ?></div>
					</li>
					<li><a href="/" target="_blank">
							<div><img src="/images/admin/main/admin_icon1.png"><br>HOMEPAGE</div>
						</a><!-- <a href="<?= ADMIN_DIR ?>/member/manager_list.php?pn=2&sn=1"><div><img src="/images/admin/main/admin_icon2.png"><br>MAIN</div></a> --></li>
				</ul>
			</div>
			<div class="admin-menu-depth1" id="admin-main-menu">
				<ul>
					<?php
					//1차 메뉴
					$menu_where = "where language = :language and depth = 1 and m_open = '1' ";
					$menu_param[] = array('language', LANGUAGE);
					$menu_orderby = "num asc|m_code asc|idx desc";
					$menu_list_result = $query_library->getList($menu_where, $menu_param, $menu_table_name, $menu_orderby, 1, 30);
					foreach ($menu_list_result['result'] as $menu_data) {
						if (!$admin_super && $menu_data['m_code'] != '001') { //권한 확인
							$auth_pass = '';
							foreach ($admin_auth_array as $key => $val) {
								if ($menu_data['m_code'] == substr((string)$val, 0, 3)) {
									$auth_pass = '1';
								}
							}

							if (!$auth_pass) {
								continue;
							}
						}
						if (substr((string)$menu_code, 0, 3) == $menu_data['m_code']) {
							$depth_first_class = ' class="on"';
							$depth_first_name = $menu_data['m_name'];
						} else {
							$depth_first_class = '';
						}

						if ($menu_data['m_link']) {
							if ($menu_data['m_link_type'] == '1') {
								$href = 'href="' . $menu_data['m_link'] . '" onfocus="this.blur()" target="' . $menu_data['m_link_target'] . '" onfocus="this.blur()"';
							} else {
								//2차 메뉴코드 가져오기
								$depth2_menu_where = " where parent = :parent and substring(m_code,1,3) = :m_code and depth = '2' ";
								$depth2_menu_bind_param = array();
								$depth2_menu_bind_param[] = array('parent', $menu_data['parent']);
								$depth2_menu_bind_param[] = array('m_code', $menu_data['m_code']);
								$depth2_menu_data = $query_library->getDataCustom($menu_table_name, 'num asc', '*', $depth2_menu_where, $depth2_menu_bind_param);

								$href = $depth2_menu_data ? 'href="' . ADMIN_DIR . $menu_data['m_link'] . '&menu_code=' . $depth2_menu_data['m_code'] . '" onfocus="this.blur()" target="' . $menu_data['m_link_target'] . '" onfocus="this.blur()"' : '';
							}
						} else {
							$href = '';
						}
					?>
						<li<?= $depth_first_class ?>>
							<a <?= $href ?>><?= $menu_data['m_name'] ?></a>
							<!-- 2차메뉴  -->
							<ul class="admin-menu-depth2">
								<?php
								$menu_param2 = array();
								$menu_where2 = "where m_open = '1' and language = :language and substring(m_code,1,3) = :m_code and depth = 2";
								$menu_param2[] = array('language', LANGUAGE);
								$menu_param2[] = array('m_code', substr($menu_data['m_code'], 0, 3));
								$menu_orderby2 = "num asc|m_code asc|idx desc";
								$menu_list_result2 = $query_library->getList($menu_where2, $menu_param2, $menu_table_name, $menu_orderby2, 1, 30);
								foreach ($menu_list_result2['result'] as $menu_data2) {
									unset($menu_param2);
									if (!$admin_super && substr($menu_data2['m_code'], 0, 3) != '001') { //권한 확인
										$auth_pass = '';
										foreach ($admin_auth_array as $key => $val) {
											if ($menu_data2['m_code'] == $val) {
												$auth_pass = '1';
											}
										}

										if (!$auth_pass) {
											continue;
										}
									}

									if ($menu_data2['m_link']) {
										if ($menu_data2['m_link_type'] == '1') {
											$href = 'href="' . $menu_data2['m_link'] . '" onfocus="this.blur()" target="' . $menu_data2['m_link_target'] . '" onfocus="this.blur()"';
										} else {
											$href = 'href="' . ADMIN_DIR . $menu_data2['m_link'] . '&menu_code=' . $menu_data2['m_code'] . '" onfocus="this.blur()" target="' . $menu_data2['m_link_target'] . '" onfocus="this.blur()"';
										}
									} else {
										$href = '';
									}
								?>
									<li><a <?= $href ?>><?= $menu_data2['m_name'] ?></a></li>
								<?php } ?>
							</ul>
							<!-- 2차메뉴  -->
							</li>
						<?php } ?>
						<li><a href="<?= ADMIN_DIR ?>/login/logout_ok.php">로그아웃</a></li>
				</ul>
			</div>
		</div>
		<div id="left-gnb">
			<?php if (isset($menu_code)) { ?>
				<div class="depth1"><?= $depth_first_name ?></div>
				<div class="depth2">
					<ul>
						<?php
						//2차 메뉴
						$menu_param = array();
						$menu_where2 = "where m_open = '1' and language = :language and substring(m_code,1,3) = :m_code and depth = 2";
						$menu_param[] = array('language', LANGUAGE);
						$menu_param[] = array('m_code', substr($menu_code, 0, 3));
						$menu_orderby2 = "num asc|m_code asc|idx desc";
						$menu_list_result2 = $query_library->getList($menu_where2, $menu_param, $menu_table_name, $menu_orderby2, 1, 30);
						foreach ($menu_list_result2['result'] as $menu_data2) {
							unset($menu_param);
							if (!$admin_super && substr($menu_data2['m_code'], 0, 3) != '001') { //권한 확인
								$auth_pass = '';
								foreach ($admin_auth_array as $key => $val) {
									if ($menu_data2['m_code'] == $val) {
										$auth_pass = '1';
									}
								}

								if (!$auth_pass) {
									continue;
								}
							}

							if (substr($menu_code, 0, 6) == $menu_data2['m_code']) {
								$depth_second_class = ' class="on"';
								$depth_second_name = $menu_data2['m_name'];
							} else {
								$depth_second_class = '';
							}

							if ($menu_data2['m_link']) {
								if ($menu_data2['m_link_type'] == '1') {
									$href = 'href="' . $menu_data2['m_link'] . '" onfocus="this.blur()" target="' . $menu_data2['m_link_target'] . '" onfocus="this.blur()"';
								} else {
									$href = 'href="' . ADMIN_DIR . $menu_data2['m_link'] . '&menu_code=' . $menu_data2['m_code'] . '" onfocus="this.blur()" target="' . $menu_data2['m_link_target'] . '" onfocus="this.blur()"';
								}
							} else {
								$href = '';
							}
						?>
							<li<?= $depth_second_class ?>><a <?= $href ?>><?= $menu_data2['m_name'] ?></a></li>
								<?php
								//3차 메뉴
								$menu_where3 = "where m_open = '1' and language = :language and substring(m_code,1,6) = :m_code and depth = 3";
								$menu_param[] = array('language', LANGUAGE);
								$menu_param[] = array('m_code', substr($menu_code, 0, 6));
								$menu_orderby3 = "num asc|m_code asc|idx desc";
								$menu_list_result3 = $query_library->getList($menu_where3, $menu_param, $menu_table_name, $menu_orderby3, 1, 30);
								foreach ($menu_list_result3['result'] as $menu_data3) {
									unset($menu_param);
									if ($menu_code == $menu_data3['m_code']) {
										$depth_third_class = ' class="on"';
										$depth_third_name = $menu_data3['m_name'];
									} else {
										$depth_third_class = '';
									}

									if ($menu_data3['m_link']) {
										if ($menu_data3['m_link_type'] == '1') {
											$href = 'href="' . $menu_data3['m_link'] . '" onfocus="this.blur()" target="' . $menu_data3['m_link_target'] . '" onfocus="this.blur()"';
										} else {
											$href = 'href="' . ADMIN_DIR . $menu_data3['m_link'] . '&menu_code=' . $menu_data3['m_code'] . '" onfocus="this.blur()" target="' . $menu_data3['m_link_target'] . '" onfocus="this.blur()"';
										}
									} else {
										$href = '';
									}
								?>
									<li<?= $depth_third_class ?>><a <?= $href ?>><?= $menu_data3['m_name'] ?></a></li>
									<?php } ?>
								<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		<div id="contents">
			<div class="contents">
				<div class="page-title"><?php if (isset($depth_second_name)) { ?><?= $depth_second_name ?><?php } ?><?php if (isset($depth_third_name)) { ?> <?= $depth_third_name ?><?php } ?></div>