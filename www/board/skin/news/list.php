<div class="page m44 bbs1">

	<section class="sec1">
		<div class="innerWrap">
			<div class="container">
				<ul class="newList">
				<?php
					$bindParam = array();
					$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
					if($keyType || $keyword){
						if($keyType == 'all'){
							$where .= " and (title like :keyword or content like :keyword) ";
						}else{
							$where .= " and ".$funcLibrary->escapeQuery($keyType)." like :keyword ";
						}
						$bindParam[] = array('keyword', $keyword,'like');
					}

					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$listResult = $queryLibrary->getList($where,$bindParam,'gh_board_'.$bbsid,$orderby,$pg,6);
					$number = $listResult['number'];
					foreach($listResult['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$thumbImg = $d['file_thumb'] ? '<img src="../data/board/'.$bbsid.'/'.$d['file_thumb'].'" alt="">' : '';
					if($d['link_url']){
						$viewLink = 'href="'.$d['link_url'].'" target="_blank"';
					}else{
						$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['idx'].'"';
					}
				?>
					<li class="listItem">
						<a <?=$viewLink?>>
							<div class="itemThumb">
								<?= $thumbImg ?>
							</div>
							<div class="itemTextArea">
								<p class="itemCategory">Press</p>
								<h6 class="itemTitle"><?=$funcLibrary->cutString($d['b_subject'],80,'..')?></h6>
								<span class="itemDate"><?= $regdate ?></span>
							</div>
						</a>
					</li>
				<?php }?>
				</ul>
				<div class="paging">
					<?=$funcLibrary->getUserPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
				</div>
			</div>
		</div>
	</section>
</div>