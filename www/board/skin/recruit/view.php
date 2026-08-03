<?php
if($d['b_notice'] == '1'){
	if($bbsid == 'notice'){
		$category_class = 'cate1';
	}else if($bbsid == 'bidding'){
		$category_class = 'cate2';
	}else if($bbsid == 'plan'){
		$category_class = 'cate4';
	}else if($bbsid == 'disclosure'){
		$category_class = 'cate5';
	}
}else{
	$category_class = '';
}
?>
<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs_view"> <!-- m41,m42,m51,m52 css 공통 -->

	<div class="page_top pn<?= (int) $pn ?> sn<?= (int) $sn ?>">
		<div class="page_tit"><?= gh_h($dep2 ?? '') ?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
			<div class="titwrap">
				<div class="tit"><?php if($category_class){?><span class="notice <?= gh_h($category_class) ?>"><?= gh_h($board_info['b_name'] ?? '') ?></span><?php }?><?= gh_h($d['b_subject'] ?? '') ?></div>
				<div class="date"><?= gh_h((string) $regdate2) ?></div>
			</div>
		</div>
	</div>
	
	<div class="section section2">
		<div class="innerwrap">
			<div class="conwrap">
				<?php
					if (substr($d['editdate'] ?? '', 0, 10) < '2025-03-01') {
						echo nl2br(gh_h($d['b_content'] ?? ''), false);
					} else {
						echo $func_library->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null);
					}
				?>
			</div>
			<?php if(array_filter($b_file) != [] ){?>
				<div class="filewrap">
					<?php for($i=0;$i<count((array)$b_file);$i++){
						$fn = $func_library->safeBoardUploadBasename($b_file[$i] ?? '');
						if ($fn === '') { continue; }
						$ofn = (string)($b_file_name[$i] ?? '');
					?>
						<a href="./download.php?board=Y&bbsid=<?= gh_h((string) $bbsid) ?>&file_name=<?= rawurlencode($fn) ?>&o_file_name=<?= rawurlencode($ofn) ?>" download><?= gh_h($ofn !== '' ? $ofn : $fn) ?></a>
					<?php }?>
				</div>
			<?php }?>
			<div class="btnwrap">
				<a href="board.php?<?=$func_library->queryString('idx,w')?>" class="btn"><span>목록으로</span><i class="tab_icon"></i></a>
			</div>
		</div>
	</div>
</div>