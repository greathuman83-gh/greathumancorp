<div class="page bbs_view">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?=$pageName?></h3>
		
		<div class="titwrap">
			<div class="tit"><?=$d['b_subject']?></div>
			<div class="date"><?=$regdate2?></div>
		</div>
		<?php if(array_filter($b_file ?? array()) != [] ){?>
			<div class="filewrap">
				<?php for($i=0;$i<count($b_file);$i++){?>
					<dl>
						<dt><img src="/images/page/file_icon.png" alt="">첨부파일</dt>
						<dd><a href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a></dd>
					</dl>
				<?php }?>
			</div>
		<?php }?>
		<div class="conwrap">
			<?=$d['b_content']?>
		</div>
		
		<div class="btnwrap">
			<a href="?<?=$funcLibrary->queryString('idx,w')?>" class="btn">
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