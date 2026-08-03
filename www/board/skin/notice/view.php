<div class="page bbs_view">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?= gh_h($page_name ?? '') ?></h3>
		
		<div class="titwrap">
			<div class="tit"><?= gh_h($d['b_subject'] ?? '') ?></div>
			<div class="date"><?= gh_h((string) $regdate2) ?></div>
		</div>
		<?php if(array_filter($b_file ?? array()) != [] ){?>
			<div class="filewrap">
				<?php for($i=0;$i<count($b_file);$i++){
					$fn = $func_library->safeBoardUploadBasename($b_file[$i] ?? '');
					if ($fn === '') { continue; }
					$ofn = (string)($b_file_name[$i] ?? '');
				?>
					<dl>
						<dt><img src="/images/page/file_icon.png" alt="">첨부파일</dt>
						<dd><a href="./download.php?board=Y&bbsid=<?= gh_h((string) $bbsid) ?>&file_name=<?= rawurlencode($fn) ?>&o_file_name=<?= rawurlencode($ofn) ?>" download><?= gh_h($ofn !== '' ? $ofn : $fn) ?></a></dd>
					</dl>
				<?php }?>
			</div>
		<?php }?>
		<div class="conwrap">
			<?= $func_library->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null) ?>
		</div>
		
		<div class="btnwrap">
			<a href="?<?=$func_library->queryString('idx,w')?>" class="btn">
				<span>목록</span>
				<div class="line">
					<div></div>
					<div></div>
					<div></div>
				</div>
			</a>
		</div>
		
	</div>
</div>