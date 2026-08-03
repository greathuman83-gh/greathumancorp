<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs"> <!-- m41,m42,m51,m52 css 공통 -->

	<div class="page_top pn<?=$pn?> sn<?=$sn?>">
		<div class="page_tit"><?= gh_h($dep2 ?? '') ?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
			<ul class="list"> <!-- 한 페이지당 리스트 10개 -->
				<!-- 공지 -->
				<?php
					$bind_param = array();
					$where = " where b_notice = '1' and b_open='1' ";
					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$list_result = $query_library->getList($where,$bind_param,'gh_board_'.$bbsid,$orderby,1,10);
					foreach($list_result['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$b_file = explode('|',$d['b_file'] ??= '');
					$b_file_name = explode('|',$d['b_file_name'] ??= '');
					if($d['b_file']){
						$icon_file = 'class="file"';
					}else{
						$icon_file = '';
					}
					if($bbsid == 'notice'){
						$category_class = 'cate1';
					}else if($bbsid == 'bidding'){
						$category_class = 'cate2';
					}else if($bbsid == 'plan'){
						$category_class = 'cate4';
					}else if($bbsid == 'disclosure'){
						$category_class = 'cate5';
					}
					$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['idx'] ?? 0) . '"';
				?>
					<li>
						<a <?=$view_link?> <?=$icon_file?>>
							<div class="tit">
								<div class="g">
									<p class="con"><span class="notice <?= gh_h($category_class) ?>"><?= gh_h($board_info['b_name'] ?? '') ?></span><?= gh_h($func_library->cutString($d['b_subject'] ?? '', 100, '..')) ?></p>
								</div>
							</div>
							<div class="date"><?= gh_h((string) $regdate) ?></div>
						</a>
					</li>
				<?php }?>
				<?php
					$bind_param = array();
					$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
					if($key_type || $keyword){
						if($key_type == 'all'){
							if (($keyword ?? '') !== '') {
								$where .= " and (b_subject like :keyword or b_content like :keyword) ";
								$bind_param[] = array('keyword', $keyword,'like');
							}
						}else{
							$col = $func_library->escapeQuery($key_type);
							if (($keyword ?? '') !== '' && $col !== null && $col !== '') {
								$where .= " and {$col} like :keyword ";
								$bind_param[] = array('keyword', $keyword,'like');
							}
						}
					}

					$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
					$list_result = $query_library->getList($where,$bind_param,'gh_board_'.$bbsid,$orderby,$pg,10);
					$number = $list_result['number'];
					foreach($list_result['result'] as $d){
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
						$icon_file = 'class="file"';
					}else{
						$icon_file = '';
					}

					$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['idx'] ?? 0) . '"';
				?>
					<li>
						<a <?=$view_link?> <?=$icon_file?>>
							<div class="tit">
								<div class="g">
									<p class="con"><?= gh_h($func_library->cutString($d['b_subject'] ?? '', 100, '..')) ?></p>
								</div>
							</div>
							<div class="date"><?= gh_h((string) $regdate) ?></div>
						</a>
					</li>
				<?php }?>
			</ul>
			
			<div class="paging">
				<?=$func_library->getUserPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
			</div>

		</div>
	</div>
</div>