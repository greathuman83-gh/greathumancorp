<?php
if($d['b_notice'] == '1'){
	if($bbsid == 'notice'){
		$categoryClass = 'cate1';
	}else if($bbsid == 'bidding'){
		$categoryClass = 'cate2';
	}else if($bbsid == 'plan'){
		$categoryClass = 'cate4';
	}else if($bbsid == 'disclosure'){
		$categoryClass = 'cate5';
	}
}else{
	$categoryClass = '';
}
?>
<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs_view"> <!-- m41,m42,m51,m52 css 공통 -->

	<div class="page_top pn<?=$pn?> sn<?=$sn?>">
		<div class="page_tit"><?=$dep2?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
			<div class="titwrap">
				<div class="tit"><?php if($categoryClass){?><span class="notice <?=$categoryClass?>"><?=$boardInfo['b_name']?></span><?php }?><?=$d['b_subject']?></div>
				<div class="date"><?=$regdate2?></div>
			</div>
		</div>
	</div>
	
	<div class="section section2">
		<div class="innerwrap">
			<div class="conwrap">
				<?php
					if(substr($d['editdate'],0,10) < '2025-03-01'){//리뉴얼 전 DB 마이그레이션
						echo nl2br($d['b_content']);
					}else{
						echo $d['b_content'];
					}
				?>
			</div>
			<?php if(array_filter($b_file) != [] ){?>
				<div class="filewrap">
					<?php for($i=0;$i<count((array)$b_file);$i++){?>
						<a href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?></a>
					<?php }?>
				</div>
			<?php }?>
			<div class="btnwrap">
				<a href="board.php?<?=$funcLibrary->queryString('idx,w')?>" class="btn"><span>목록으로</span><i class="tab_icon"></i></a>
			</div>
		</div>
	</div>
</div>