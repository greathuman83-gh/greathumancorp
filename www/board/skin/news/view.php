<div class="page bbs1_view m44">

	<section class="sec1" id="pageTop">
		<div class="innerWrap">
			<div class="container">
				<div class="contentHead">
					<h6 class="contentTitle"><?=$d['b_subject']?></h6>
					<span class="contentDate"><?= $regdate2 ?></span>
				</div>
				<div class="contentBody">
					<?=$d['b_content']?>
				</div>
				<?php if(array_filter($b_file ?? array()) != [] ){?>
					<div class="contentFile">
						<?php for($i=0;$i<count($b_file);$i++){?>
							<!-- 
							<dl>
								<dt><img src="/images/page/icon_file.png" alt="">첨부파일</dt>
								<dd><a href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a></dd>
							</dl>
							-->
							<a class="fileLink" href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download>
								<img class="fileIcon" src="/images/page/icon_file.png" alt="">
								<p class="fileTitle"><?=$b_file_name[$i]?></p>
							</a>

						<?php }?>
					</div>
				<?php }?>
				<div class="contentLink">
					<a class="btnMore" href="?<?=$funcLibrary->queryString('idx,w')?>">
						<span>List</span><i></i>
					</a>
				</div>
			</div>
		</div>
	</section>
</div>