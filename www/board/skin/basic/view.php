<?php
$regdate = date('d.m.Y', strtotime(substr($d['regdate'], 0, 10)));
?>
<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs">
	<div class="section section1">
		<div class="innerwrap">
			<table class="bbsView" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tit">
						<div class="title"><?= gh_h($d['b_subject'] ?? '') ?></div>
						<div class="date"><?= gh_h((string) $regdate) ?></div>
					</td>
				</tr>
				<?php if (array_filter($b_file) != []) { ?>
					<tr>
						<td class="file">
							<?php for ($i = 0; $i < count((array)$b_file); $i++) {
								$fn = $func_library->safeBoardUploadBasename($b_file[$i] ?? '');
								if ($fn === '') {
									continue;
								}
								$ofn = (string)($b_file_name[$i] ?? '');
							?>
								<a href="./download.php?board=Y&bbsid=<?= gh_h((string) $bbsid) ?>&file_name=<?= rawurlencode($fn) ?>&o_file_name=<?= rawurlencode($ofn) ?>" download><?= gh_h($ofn !== '' ? $ofn : $fn) ?><i></i></a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td class="con"><?= $func_library->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null) ?></td>
				</tr>

			</table>
			<div class="view_paging">
				<a class="page_prev" <?php if ($prev_data) {
											$pidx = (int)($prev_data['idx'] ?? $prev_data['bbs_idx'] ?? 0); ?>href="?<?= $func_library->queryString('idx,w') ?>&idx=<?= $pidx ?>" <?php } ?>>
					<div class="tit">이전글</div>
					<div class="arr"><img src="/images/page/view_paging_prev.png" alt=""></div>
					<div class="page_tit"><?php if ($prev_data) { ?><?= gh_h($func_library->cutString($prev_data['b_subject'] ?? $prev_data['title'] ?? '', 80, '..')) ?><?php } else { ?>이전글이 없습니다.<?php } ?></div>
				</a>
				<a class="page_next" <?php if ($next_data) {
											$nidx = (int)($next_data['idx'] ?? $next_data['bbs_idx'] ?? 0); ?>href="?<?= $func_library->queryString('idx,w') ?>&idx=<?= $nidx ?>" <?php } ?>>
					<div class="tit">다음글</div>
					<div class="arr"><img src="/images/page/view_paging_next.png" alt=""></div>
					<div class="page_tit"><?php if ($next_data) { ?><?= gh_h($func_library->cutString($next_data['b_subject'] ?? $next_data['title'] ?? '', 80, '..')) ?><?php } else { ?>다음글이 없습니다.<?php } ?></div>
				</a>
			</div>
			<div class="btn_list">
				<a href="?<?= $func_library->queryString('idx,w') ?>">목록으로 가기 <i></i></a>
			</div>

		</div>
	</div>
</div>