<?php
include "include/common/common.php";
header('location:' . ADMIN_DIR . '/login/login.php');
?>
<!DOCTYPE html>
<html charset="utf-8">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes" />
	<title><?= $metaTagData['title'] ?? '' ?></title>
	<meta name="Keywords" content="<?= $metaTagData['meta_keywords'] ?? '' ?>" />
	<meta name="Description" content="<?= $metaTagData['meta_description'] ?? '' ?>" />
	<meta name="Author" content="DESIGNPIXEL" />
	<meta name="Copyright" content="(c)DESIGNPIXEL" />
	<meta name="reply-to" content="" />
	<meta name="date" content="" />
	<?php if (($ogTagData['og_use'] ??= '') == '1') { ?>
		<meta property="og:title" content="<?= $ogTagData['og_title'] ?? '' ?>" />
		<meta property="og:description" content="<?= $ogTagData['og_description'] ?? '' ?>" />
		<?php if ($ogTagData['file1'] ?? '') { ?>
			<meta property="og:image" content="<?= HOMEPAGE_URL ?>/data/seo/<?= $ogTagData['file1'] ?>" />
		<?php } ?>
		<meta property="og:url" content="<?= HOMEPAGE_URL ?><?= $_SERVER['REQUEST_URI'] ?>" />
		<meta property="og:type" content="<?= $ogTagData['og_type'] ?? '' ?>" />
		<meta property="og:site_name" content="<?= $ogTagData['og_sitename'] ?? '' ?>" />
		<meta property="og:locale" content="<?= $ogTagData['og_locale'] ?? '' ?>" />
		<meta property="og:image:width" content="<?= $ogTagData['og_image_width'] ?? '' ?>" />
		<meta property="og:image:height" content="<?= $ogTagData['og_image_height'] ?? '' ?>" />
	<?php } ?>
	<?php if ($metaTagData['file1'] ?? '') { ?>
		<link rel="icon" href="<?= HOMEPAGE_URL ?>/data/seo/<?= $metaTagData['file1'] ?>" type="image/png">
	<?php } ?>
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="/css/swiper.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.fullpage.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/root.css" />
	<link rel="stylesheet" type="text/css" href="/css/common.css?version=v01" />
	<link rel="stylesheet" type="text/css" href="/css/index_pc.css?version=v01" media="screen and (min-width:1400px)" />
	<link rel="stylesheet" type="text/css" href="/css/index_tablet.css?version=v01" media="screen and (min-width:813px) and (max-width:1399px)" />
	<link rel="stylesheet" type="text/css" href="/css/index_mobile.css?version=v01" media="screen and (max-width:812px)" />
	<script language="javascript" src="/js/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js" integrity="sha384-UoFnwgbUNlK0Rwou15cSoyrXQvKrOUxV8AkCoGKSSd0sxVeJ95ARU8uGMkMZhzpB" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js" integrity="sha384-poC0r6usQOX2Ayt/VGA+t81H6V3iN9L+Irz9iO8o+s0X20tLpzc9DOOtnKxhaQSE" crossorigin="anonymous"></script>
	<script type="text/javascript" src="/js/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="/js/jquery.fullpage.js"></script>
	<script type="text/javascript" src="/js/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="/js/swiper.js"></script>
	<script type="text/javascript" src="/js/common.js?version=v01"></script>
	<script type="text/javascript" src="/js/main.js?version=v01"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
</head>

<body>
	<?php //========================== 레이어 팝업 기능 ===========================================
	?>
	<script language="javascript">
		function setCookie(name, value, expiredays) {
			var todayDate = new Date();
			todayDate.setDate(todayDate.getDate() + expiredays);
			document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";"
		}

		function pop_close(name, idx) {
			setCookie(name, "done", 1);
			$("#layer_pop" + idx).hide();
		}

		function pop_hide(idx) {
			$("#layer_pop" + idx).hide();
		}

		$(function() {
			var pop_move = false;
			$(".main_layer_popup .pop_link").click(function() {
				var data = $(this).data("pop_idx");
				if (!pop_move) {
					pop_hide(data);
				}
			})

			$(".main_layer_popup").draggable({
				handle: "div.pop_bottom",
				cursor: "move"
			});
		});
	</script>
	<?php
	$popBindParam = array();
	$where = " where category = 'kr' and pop_view ='1' and (start_date <= :today_date and end_date >= :today_date or always = 'Y') ";
	$popBindParam[] = array('today_date', GH_TIME_YMD);
	$orderby = "num desc|idx desc";
	$listResult = $queryLibrary->getList($where, $popBindParam, 'gh_popup_table', $orderby, 1, 10);
	foreach ($listResult['result'] as $pd) {

		if (($_COOKIE['pop_' . $pd['idx']] ??= null) == 'done') {
			continue;
		}
		if ($pd['num'] < 2) {
			$pd['num'] = 1;
		}
	?>
		<div style="position:absolute;left:<?= $pd['pop_location_left'] ?>px;top:<?= $pd['pop_location_top'] ?>px;width:<?= $pd['pop_size_w'] ?>px;height:<?= $pd['pop_size_h'] + 59 ?>px;z-index:9999<?= $pd['num'] ?>" id="layer_pop<?= $pd['idx'] ?>" class="main_layer_popup">
			<div>
				<div style="<?php if (!$pd['file1']) { ?>padding:20px;<?php } ?>;background:#fff" class="m_h">
					<?php if ($pd['file1']) { ?>
						<a class="pop_link" <?php if ($pd['pop_link_url']) { ?>href="<?= $pd['pop_link_url'] ?>" target="<?= $pd['pop_target'] ?>" <?php } ?> data-pop_idx="<?= $pd['idx'] ?>"><img src="./data/popup/<?= $pd['file1'] ?>" width="<?= $pd['pop_size_w'] ?>"></a>
					<?php } else { ?>
						<?= nl2br($pd['pop_content']) ?>
					<?php } ?>
				</div>
				<div class="pop_bottom" style="text-align:right;background:#000000;height:35px;padding-right:10px;padding-top:6px;">
					<a href="javascript:pop_close('pop_<?= $pd['idx'] ?>','<?= $pd['idx'] ?>');"><button type="button" class="pop_today_close">24시간 동안 다시 열람하지 않습니다.</button></a>&nbsp;&nbsp;&nbsp;
					<a onclick="pop_hide('<?= $pd['idx'] ?>');"><button type="button" class="pop_today_close">닫기</button></a>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php //========================== 레이어 팝업 기능 끝 ==========================================
	?>
	<div id="wrap">

	</div><!-- //wrap -->

</body>

</html>