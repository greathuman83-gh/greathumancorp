<div class="page m42 bbs">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?= gh_h($page_name ?? '') ?></h3>
		<ul class="board_skin2 ani">
			<?php
				$bind_param = array();
				$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
				if($key_type || $keyword){
					if($key_type == 'all'){
						if (($keyword ?? '') !== '') {
							$where .= " and (title like :keyword or content like :keyword) ";
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
				$list_result = $query_library->getList($where,$bind_param,'gh_board_'.$bbsid,$orderby,$pg,6);
				$number = $list_result['number'];
				foreach($list_result['result'] as $d){
				$regdate = date('d',strtotime(substr($d['regdate'],0,10)));
				$regdate2 = date('Y.m',strtotime(substr($d['regdate'],0,10)));

				$content_preview = html_entity_decode(
					str_replace('&nbsp;', ' ', (string) ($d['b_content'] ?? '')),
					ENT_QUOTES | ENT_HTML5,
					'UTF-8'
				);
				$ext_href = $func_library->safeHrefForUserLink($d['link_url'] ?? null);
				if ($ext_href !== null) {
					$view_link = 'href="' . $ext_href . '" target="_blank" rel="noopener noreferrer"';
				} else {
					$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['idx'] ?? 0) . '"';
				}
			?>
				<li>
					<a <?=$view_link?>>
						<div class="datewrap">
							<div class="num"><?= gh_h((string) $regdate) ?></div>
							<div class="date"><?= gh_h((string) $regdate2) ?></div>
						</div>
						<div class="txtwrap">
							<div class="txts">
								<div class="tit"><?= gh_h($func_library->cutString($d['b_subject'] ?? '', 80, '..')) ?></div>
								<div class="con"><?= gh_h($func_library->cutString($content_preview, 100, '..')) ?></div>
							</div>
							<div class="arr"><i></i></div>
						</div>
					</a>
				</li>
			<?php }?>
		</ul>
		
		<div class="paging">
			<?=$func_library->getUserPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
		</div>
		
	</div>
</div>