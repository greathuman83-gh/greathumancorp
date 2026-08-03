<div class="page bbs1_view m44">

	<section class="sec1" id="pageTop">
		<div class="innerWrap">
			<div class="container">
				<div class="contentHead">
					<h6 class="contentTitle"><?= gh_h($d['b_subject'] ?? '') ?></h6>
					<span class="contentDate"><?= gh_h((string) $regdate2) ?></span>
				</div>
				<div class="contentBody">
					<?= $func_library->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null) ?>
				</div>
				<?php if(array_filter($b_file ?? array()) != [] ){?>
					<div class="contentFile">
						<?php for($i=0;$i<count($b_file);$i++){
							$fn = $func_library->safeBoardUploadBasename($b_file[$i] ?? '');
							if ($fn === '') { continue; }
							$ofn = (string)($b_file_name[$i] ?? '');
						?>
							<!-- 
							<dl>
								<dt><img src="/images/page/icon_file.png" alt="">첨부파일</dt>
								<dd><a href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a></dd>
							</dl>
							-->
							<a class="fileLink" href="./download.php?board=Y&bbsid=<?= gh_h((string) $bbsid) ?>&file_name=<?= rawurlencode($fn) ?>&o_file_name=<?= rawurlencode($ofn) ?>" download>
								<img class="fileIcon" src="/images/page/icon_file.png" alt="">
								<p class="fileTitle"><?= gh_h($ofn !== '' ? $ofn : $fn) ?></p>
							</a>

						<?php }?>
					</div>
				<?php }?>
				<div class="contentLink">
					<a class="btnMore" href="?<?=$func_library->queryString('idx,w')?>">
						<span>List</span><i></i>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>