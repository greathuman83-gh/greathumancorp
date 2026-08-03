<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs">
	<div class="pgTit ani innerwrap">
		<h3><?= gh_h($dep2 ?? '') ?></h3>
	</div>
	<section class="sec1 ani">
		<div class="innerwrap">
			<div class="bbsView">
					<div class="titlearea">
						<div class="tit"><?= gh_h($d['b_subject'] ?? '') ?></div>
						<div class="date"><?= gh_h((string) $regdate2) ?></div>
					</div>
					<div class="viewcon">
						<?= $func_library->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null) ?>
					</div>
					<div class="btnList">
						<a href="?<?=$func_library->queryString('idx,w')?>">
							<span>List Back</span>
							<div class="menu">
								<div></div>
								<div></div>
								<div></div>
							</div>
						</a>
					</div>
			</div>
		</div>
	</section>
</div>