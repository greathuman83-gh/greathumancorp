<?php
include_once($ghPath . 'include/common/common.php');
include_once($ghPath . 'include/common/permission.php');
$menuTableName = 'gh_admin_menu_table'; //메뉴 DB
$adminAuthArray = explode('|', $adminAuth);

if (!isset($menuCode)) {
	$menuCode = null;
}

$categoryTable = 'gh_category_table'; //카테고리 테이블
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">

<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= $metaTagData['title'] ?? '' ?></title>
	<link rel="stylesheet" href="<?= $ghPath ?>include/css/css.css?<?= ADMIN_VERSION ?>" type="text/css">
	<script language="javascript" src="//code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
	<script language="javascript" src="<?= $ghPath ?>include/js/common.js?<?= ADMIN_VERSION ?>"></script>
</head>

<body leftmargin="0" topmargin="0">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
	<style type="text/css">
		<!--
		.ui-datepicker {
			font: 12px dotum;
		}

		.ui-datepicker select.ui-datepicker-month,
		.ui-datepicker select.ui-datepicker-year {
			width: 70px;
		}

		.ui-datepicker-trigger {
			margin-left: 5px;
			vertical-align: middle;
		}
		-->
	</style>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$.datepicker.regional['ko'] = {
				closeText: '닫기',
				prevText: '이전달',
				nextText: '다음달',
				currentText: '오늘',
				monthNames: ['1월(JAN)', '2월(FEB)', '3월(MAR)', '4월(APR)', '5월(MAY)', '6월(JUN)',
					'7월(JUL)', '8월(AUG)', '9월(SEP)', '10월(OCT)', '11월(NOV)', '12월(DEC)'
				],
				monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월',
					'7월', '8월', '9월', '10월', '11월', '12월'
				],
				dayNames: ['일', '월', '화', '수', '목', '금', '토'],
				dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
				dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
				weekHeader: 'Wk',
				dateFormat: 'yy-mm-dd',
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: true,
				yearSuffix: ''
			};
			$.datepicker.setDefaults($.datepicker.regional['ko']);

			$('.date').datepicker({
				showOn: 'button',
				buttonImage: '<?= $ghPath ?>/images/admin/calendar.gif',
				buttonImageOnly: true,
				buttonText: "달력",
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				yearRange: 'c-30:c+10',
				maxDate: '+1y',
			});

		});
	</script>
	<div id="bg_black"></div>
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
					<li><img src="<?= $ghPath ?>images/admin/main/admin_top_logo.png" width="170">&nbsp;&nbsp;&nbsp;<div>|&nbsp;&nbsp;&nbsp;웹 통합 관리 시스템 <?= ADMIN_VERSION ?></div>
					</li>
					<li><a href="/" target="_blank">
							<div><img src="<?= $ghPath ?>images/admin/main/admin_icon1.png"><br>HOMEPAGE</div>
						</a><!-- <a href="<?= ADMIN_DIR ?>/member/manager_list.php?pn=2&sn=1"><div><img src="<?= $ghPath ?>images/admin/main/admin_icon2.png"><br>MAIN</div></a> --></li>
				</ul>
			</div>
			<div class="admin_menu_depth1" id="admin-main-menu">
				<ul>
					<?php
					//1차 메뉴
					$menuWhere = "where language = :language and depth = 1 and m_open = '1' ";
					$menuParam[] = array('language', LANGUAGE);
					$menuOrderby = "num asc|m_code asc|idx desc";
					$menuListResult = $queryLibrary->getList($menuWhere, $menuParam, $menuTableName, $menuOrderby, 1, 30);
					foreach ($menuListResult['result'] as $menuData) {
						if (!$adminSuper && $menuData['m_code'] != '001') { //권한 확인
							$authPass = '';
							foreach ($adminAuthArray as $key => $val) {
								if ($menuData['m_code'] == substr((string)$val, 0, 3)) {
									$authPass = '1';
								}
							}

							if (!$authPass) {
								continue;
							}
						}
						if (substr((string)$menuCode, 0, 3) == $menuData['m_code']) {
							$depthFirstClass = ' class="on"';
							$depthFirstName = $menuData['m_name'];
						} else {
							$depthFirstClass = '';
						}

						if ($menuData['m_link']) {
							if ($menuData['m_link_type'] == '1') {
								$href = 'href="' . $menuData['m_link'] . '" onfocus="this.blur()" target="' . $menuData['m_link_target'] . '" onfocus="this.blur()"';
							} else {
								//2차 메뉴코드 가져오기
								$depth2MenuWhere = " where parent = :parent and substring(m_code,1,3) = :m_code and depth = '2' ";
								$depth2MenuBindParam = array();
								$depth2MenuBindParam[] = array('parent', $menuData['parent']);
								$depth2MenuBindParam[] = array('m_code', $menuData['m_code']);
								$depth2MenuData = $queryLibrary->getDataCustom($menuTableName, 'num asc', '*', $depth2MenuWhere, $depth2MenuBindParam);

								$href = $depth2MenuData ? 'href="' . ADMIN_DIR . $menuData['m_link'] . '&menuCode=' . $depth2MenuData['m_code'] . '" onfocus="this.blur()" target="' . $menuData['m_link_target'] . '" onfocus="this.blur()"' : '';
							}
						} else {
							$href = '';
						}
					?>
						<li<?= $depthFirstClass ?>>
							<a <?= $href ?>><?= $menuData['m_name'] ?></a>
							<!-- 2차메뉴  -->
							<ul class="admin_menu_depth2">
								<?php
								$menuParam2 = array();
								$menuWhere2 = "where m_open = '1' and language = :language and substring(m_code,1,3) = :m_code and depth = 2";
								$menuParam2[] = array('language', LANGUAGE);
								$menuParam2[] = array('m_code', substr($menuData['m_code'], 0, 3));
								$menuOrderby2 = "num asc|m_code asc|idx desc";
								$menuListResult2 = $queryLibrary->getList($menuWhere2, $menuParam2, $menuTableName, $menuOrderby2, 1, 30);
								foreach ($menuListResult2['result'] as $menuData2) {
									unset($menuParam2);
									if (!$adminSuper && substr($menuData2['m_code'], 0, 3) != '001') { //권한 확인
										$authPass = '';
										foreach ($adminAuthArray as $key => $val) {
											if ($menuData2['m_code'] == $val) {
												$authPass = '1';
											}
										}

										if (!$authPass) {
											continue;
										}
									}

									if ($menuData2['m_link']) {
										if ($menuData2['m_link_type'] == '1') {
											$href = 'href="' . $menuData2['m_link'] . '" onfocus="this.blur()" target="' . $menuData2['m_link_target'] . '" onfocus="this.blur()"';
										} else {
											$href = 'href="' . ADMIN_DIR . $menuData2['m_link'] . '&menuCode=' . $menuData2['m_code'] . '" onfocus="this.blur()" target="' . $menuData2['m_link_target'] . '" onfocus="this.blur()"';
										}
									} else {
										$href = '';
									}
								?>
									<li><a <?= $href ?>><?= $menuData2['m_name'] ?></a></li>
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
			<?php if (isset($menuCode)) { ?>
				<div class="depth1"><?= $depthFirstName ?></div>
				<div class="depth2">
					<ul>
						<?php
						//2차 메뉴
						$menuParam = array();
						$menuWhere2 = "where m_open = '1' and language = :language and substring(m_code,1,3) = :m_code and depth = 2";
						$menuParam[] = array('language', LANGUAGE);
						$menuParam[] = array('m_code', substr($menuCode, 0, 3));
						$menuOrderby2 = "num asc|m_code asc|idx desc";
						$menuListResult2 = $queryLibrary->getList($menuWhere2, $menuParam, $menuTableName, $menuOrderby2, 1, 30);
						foreach ($menuListResult2['result'] as $menuData2) {
							unset($menuParam);
							if (!$adminSuper && substr($menuData2['m_code'], 0, 3) != '001') { //권한 확인
								$authPass = '';
								foreach ($adminAuthArray as $key => $val) {
									if ($menuData2['m_code'] == $val) {
										$authPass = '1';
									}
								}

								if (!$authPass) {
									continue;
								}
							}

							if (substr($menuCode, 0, 6) == $menuData2['m_code']) {
								$depthSecondClass = ' class="on"';
								$depthSecondName = $menuData2['m_name'];
							} else {
								$depthSecondClass = '';
							}

							if ($menuData2['m_link']) {
								if ($menuData2['m_link_type'] == '1') {
									$href = 'href="' . $menuData2['m_link'] . '" onfocus="this.blur()" target="' . $menuData2['m_link_target'] . '" onfocus="this.blur()"';
								} else {
									$href = 'href="' . ADMIN_DIR . $menuData2['m_link'] . '&menuCode=' . $menuData2['m_code'] . '" onfocus="this.blur()" target="' . $menuData2['m_link_target'] . '" onfocus="this.blur()"';
								}
							} else {
								$href = '';
							}
						?>
							<li<?= $depthSecondClass ?>><a <?= $href ?>><?= $menuData2['m_name'] ?></a></li>
								<?php
								//3차 메뉴
								$menuWhere3 = "where m_open = '1' and language = :language and substring(m_code,1,6) = :m_code and depth = 3";
								$menuParam[] = array('language', LANGUAGE);
								$menuParam[] = array('m_code', substr($menuCode, 0, 6));
								$menuOrderby3 = "num asc|m_code asc|idx desc";
								$menuListResult3 = $queryLibrary->getList($menuWhere3, $menuParam, $menuTableName, $menuOrderby3, 1, 30);
								foreach ($menuListResult3['result'] as $menuData3) {
									unset($menuParam);
									if ($menuCode == $menuData3['m_code']) {
										$depthThirdClass = ' class="on"';
										$depthThirdName = $menuData3['m_name'];
									} else {
										$depthThirdClass = '';
									}

									if ($menuData3['m_link']) {
										if ($menuData3['m_link_type'] == '1') {
											$href = 'href="' . $menuData3['m_link'] . '" onfocus="this.blur()" target="' . $menuData3['m_link_target'] . '" onfocus="this.blur()"';
										} else {
											$href = 'href="' . ADMIN_DIR . $menuData3['m_link'] . '&menuCode=' . $menuData3['m_code'] . '" onfocus="this.blur()" target="' . $menuData3['m_link_target'] . '" onfocus="this.blur()"';
										}
									} else {
										$href = '';
									}
								?>
									<li<?= $depthThirdClass ?>><a <?= $href ?>><?= $menuData3['m_name'] ?></a></li>
									<?php } ?>
								<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		<div id="contents">
			<div class="contents">
				<div class="page-title"><?php if (isset($depthSecondName)) { ?><?= $depthSecondName ?><?php } ?><?php if (isset($depthThirdName)) { ?> <?= $depthThirdName ?><?php } ?></div>