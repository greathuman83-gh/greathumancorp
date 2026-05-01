<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs">
	<div class="pgTit ani innerwrap">
		<h3><?=$dep2?></h3>
	</div>
	<section class="sec1 ani">
		<div class="innerwrap">
			<div class="bbsView">
					<div class="titlearea">
						<div class="tit"><?=$d['b_subject']?></div>
						<div class="date"><?=$regdate2?></div>
					</div>
					<div class="viewcon">
						<?=$d['b_content']?>
					</div>
					<div class="btnList">
						<a href="?<?=$funcLibrary->queryString('idx,w')?>">
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