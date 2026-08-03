<div class="page m44 bbs1">

	<section class="sec1">
		<div class="innerWrap">
			<div class="container">
				<ul class="newList">
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
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$subject_preview = html_entity_decode(
						str_replace('&nbsp;', ' ', (string) ($d['b_subject'] ?? '')),
						ENT_QUOTES | ENT_HTML5,
						'UTF-8'
					);
					$thumb_base = $func_library->safeBoardUploadBasename($d['file_thumb'] ?? '');
					$bid_seg = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $bbsid);
					$thumb_img = $thumb_base !== '' ? '<img src="../data/board/' . $bid_seg . '/' . rawurlencode($thumb_base) . '" alt="">' : '';
					$ext_href = $func_library->safeHrefForUserLink($d['link_url'] ?? null);
					if ($ext_href !== null) {
						$view_link = 'href="' . $ext_href . '" target="_blank" rel="noopener noreferrer"';
					} else {
						$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['idx'] ?? 0) . '"';
					}
				?>
					<li class="listItem">
						<a <?=$view_link?>>
							<div class="itemThumb">
								<?= $thumb_img ?>
							</div>
							<div class="itemTextArea">
								<p class="itemCategory">Press</p>
								<h6 class="itemTitle"><?= gh_h($func_library->cutString($subject_preview, 80, '..')) ?></h6>
								<span class="itemDate"><?= gh_h((string) $regdate) ?></span>
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
	</section>
</div>