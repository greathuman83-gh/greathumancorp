<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs">
	<div class="pgTit ani innerwrap">
		<h3><?=$dep2?></h3>
	</div>
	<section class="sec1 ani">
		<div class="innerwrap">
			<ul class="list1">
				<?php
					$bindParam = array();
					$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
					if($keyword){
						$where .= " and (b_subject like :keyword or b_content like :keyword) ";
						$bindParam[] = array('keyword', $keyword,'like');
					}

					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$listResult = $queryLibrary->getList($where,$bindParam,'gh_board_'.$bbsid,$orderby,$pg,6);
					$number = $listResult['number'];
					foreach($listResult['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$reply = '';
					if(strlen($d['depth'] ??= 0) >0){
						for($i=0;$i<strlen($d['depth']);$i++){
							$reply .= '&nbsp;';
						}
						$reply .= '└ ';
					}
					$b_file = explode('|',$d['b_file'] ??= '');
					$b_file_name = explode('|',$d['b_file_name'] ??= '');
					if($d['b_file']){
						$iconFile = 'class="file"';
					}else{
						$iconFile = '';
					}
					$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['idx'].'"';

					$thumbImg = $d['file_thumb'] ? '<img src="../data/board/'.$bbsid.'/'.$d['file_thumb'].'" alt="">' : '';
				?>
					<li>
						<a <?=$viewLink?>>
							<div class="thumb"><?= $thumbImg ?></div>
							<div class="tit"><?=$funcLibrary->cutString($d['b_subject'],70,'..')?></div>
							<div class="txt"><?=$funcLibrary->cutString($d['b_content'],80,'..')?></div>
							<div class="date"><?=$regdate?></div>
						</a>
					</li>
				<?php }?>
			</ul>
			<div class="paging">
				<?=$funcLibrary->getUserPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
			</div>

		</div>
	</section>
</div>