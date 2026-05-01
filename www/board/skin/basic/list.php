<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs"> <!-- m41,m42,m51,m52 css 공통 -->

	<div class="page_top pn<?=$pn?> sn<?=$sn?>">
		<div class="page_tit"><?=$dep2?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
			<ul class="list"> <!-- 한 페이지당 리스트 10개 -->
				<!-- 공지 -->
				<?php
					$bindParam = array();
					$where = " where b_notice = '1' and b_open='1' ";
					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$listResult = $queryLibrary->getList($where,$bindParam,'gh_board_'.$bbsid,$orderby,1,10);
					foreach($listResult['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$b_file = explode('|',$d['b_file'] ??= '');
					$b_file_name = explode('|',$d['b_file_name'] ??= '');
					if($d['b_file']){
						$iconFile = 'class="file"';
					}else{
						$iconFile = '';
					}
					if($bbsid == 'notice'){
						$categoryClass = 'cate1';
					}else if($bbsid == 'bidding'){
						$categoryClass = 'cate2';
					}else if($bbsid == 'plan'){
						$categoryClass = 'cate4';
					}else if($bbsid == 'disclosure'){
						$categoryClass = 'cate5';
					}
					$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['idx'].'"';
				?>
					<li>
						<a <?=$viewLink?> <?=$iconFile?>>
							<div class="tit">
								<div class="g">
									<p class="con"><span class="notice <?=$categoryClass?>"><?=$boardInfo['b_name']?></span><?=$funcLibrary->cutString($d['b_subject'],100,'..')?></p>
								</div>
							</div>
							<div class="date"><?=$regdate?></div>
						</a>
					</li>
				<?php }?>
				<?php
					$bindParam = array();
					$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
					if($keyType || $keyword){
						if($keyType == 'all'){
							$where .= " and (b_subject like :keyword or b_content like :keyword) ";
						}else{
							$where .= " and ".$funcLibrary->escapeQuery($keyType)." like :keyword ";
						}
						$bindParam[] = array('keyword', $keyword,'like');
					}

					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$listResult = $queryLibrary->getList($where,$bindParam,'gh_board_'.$bbsid,$orderby,$pg,10);
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
				?>
					<li>
						<a <?=$viewLink?> <?=$iconFile?>>
							<div class="tit">
								<div class="g">
									<p class="con"><?=$funcLibrary->cutString($d['b_subject'],100,'..')?></p>
								</div>
							</div>
							<div class="date"><?=$regdate?></div>
						</a>
					</li>
				<?php }?>
			</ul>
			
			<div class="paging">
				<?=$funcLibrary->getUserPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
			</div>

		</div>
	</div>
</div>