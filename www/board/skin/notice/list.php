<div class="page m42 bbs">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?=$pageName?></h3>
		<ul class="board_skin2 ani">
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
				$regdate = date('d',strtotime(substr($d['regdate'],0,10)));
				$regdate2 = date('Y.m',strtotime(substr($d['regdate'],0,10)));

				if($d['link_url']){
					$viewLink = 'href="'.$d['link_url'].'" target="_blank"';
				}else{
					$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['idx'].'"';
				}
			?>
				<li>
					<a <?=$viewLink?>>
						<div class="datewrap">
							<div class="num"><?=$regdate?></div>
							<div class="date"><?=$regdate2?></div>
						</div>
						<div class="txtwrap">
							<div class="txts">
								<div class="tit"><?=$funcLibrary->cutString($d['b_subject'],80,'..')?></div>
								<div class="con"><?=$funcLibrary->cutString($d['b_content'],100,'..')?></div>
							</div>
							<div class="arr"><i></i></div>
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