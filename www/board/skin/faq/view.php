<div class="page bbsView">
	<div class="innerwrap">

		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>
					<div class="info">
						<span class="bbsId"><?= gh_h($page_depth3 ?? '') ?></span>
						<span class="dot">·</span>
						<span class="date"><?= gh_h((string) $regdate2) ?></span>
					</div>
					<div class="tit">
						<?= gh_h($d['title'] ?? '') ?>
					</div>
				</th>
			</tr>
			<?php
			//첨부파일
			$bind_param = array();
			$where = " where bbs_idx = :bbs_idx ";
			$bind_param[] = array('bbs_idx', $idx);
			$orderby = "attach_idx asc";
			$list_result = $query_library->getList($where, $bind_param, 'tb_bbs_attach', $orderby, 1, (int)$board_info['b_file'], 'attach_idx');
			if ($list_result['list_total'] > 0) {
			?>
				<tr>
					<td>
						<div class="file">
							<?php foreach ($list_result['result'] as $file_data) {
								$safe_fn = $func_library->safeBoardUploadBasename($file_data['filename'] ?? '');
								$real_fn = (string)($file_data['realfilename'] ?? '');
							?>
								<?php if ($update_date < '2024-01-01') { //데이터 이전 게시물
								?>
									<a href="./download.php?board=Y&bbsid=<?= gh_h((string) $bbsid) ?>&file_name=<?= rawurlencode($safe_fn) ?>&o_file_name=<?= rawurlencode($real_fn) ?>" download><?= gh_h($real_fn !== '' ? $real_fn : $safe_fn) ?></a>
								<?php } else { ?>
									<a href="./attach_download.php?bbsid=<?= gh_h((string) $bbsid) ?>&idx=<?= (int)($idx ?? 0) ?>&attachIdx=<?= (int)($file_data['attach_idx'] ?? 0) ?>">
										<?= gh_h($real_fn) ?>
									</a>
								<?php } ?>
							<?php } ?>
						</div>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td class="viewcon">
					<?= $func_library->sanitizeBoardHtmlForDisplay($d['content'] ?? null) ?>
				</td>
			</tr>
		</table>
		<div class="view_paging">
			<a class="page_prev" <?php if ($prev_data) { ?>href="?<?= $func_library->queryString('idx,w') ?>&idx=<?= (int)($prev_data['bbs_idx'] ?? 0) ?>" <?php } ?>>
				<div class="tit">이전글</div>
				<div class="arr"><img src="/images/page/view_paging_prev.png" alt=""></div>
				<div class="page_tit"><?php if ($prev_data) { ?><?= gh_h($func_library->cutString($prev_data['title'] ?? '', 80, '..')) ?><?php } else { ?>이전글이 없습니다.<?php } ?></div>
			</a>
			<a class="page_next" <?php if ($next_data) { ?>href="?<?= $func_library->queryString('idx,w') ?>&idx=<?= (int)($next_data['bbs_idx'] ?? 0) ?>" <?php } ?>>
				<div class="tit">다음글</div>
				<div class="arr"><img src="/images/page/view_paging_next.png" alt=""></div>
				<div class="page_tit"><?php if ($next_data) { ?><?= gh_h($func_library->cutString($next_data['title'] ?? '', 80, '..')) ?><?php } else { ?>다음글이 없습니다.<?php } ?></div>
			</a>
		</div>
		<div class="btn_list">
			<a href="?<?= $func_library->queryString('idx,w') ?>">목록으로 가기 <i></i></a>
		</div>


	</div>
</div>