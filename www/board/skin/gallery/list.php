<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs">
	<div class="pgTit ani innerwrap">
		<h3><?= gh_h($dep2 ?? '') ?></h3>
	</div>
	<section class="sec1 ani">
		<div class="innerwrap">
			<ul class="list1">
				<?php
					$bind_param = array();
					$where = " where (b_notice is null or b_notice <> '1' ) and b_open='1' ";
					if($keyword){
						$where .= " and (b_subject like :keyword or b_content like :keyword) ";
						$bind_param[] = array('keyword', $keyword,'like');
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
					$content_preview = html_entity_decode(
						str_replace('&nbsp;', ' ', (string) ($d['b_content'] ?? '')),
						ENT_QUOTES | ENT_HTML5,
						'UTF-8'
					);
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
					$thumb_base = $func_library->safeBoardUploadBasename($d['file_thumb'] ?? '');
					$bid_seg = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $bbsid);
					$thumb_img = $thumb_base !== '' ? '<img src="../data/board/' . $bid_seg . '/' . rawurlencode($thumb_base) . '" alt="">' : '';
				?>
					<li>
						<a <?=$view_link?>>
							<div class="thumb"><?= $thumb_img ?></div>
							<div class="tit"><?= gh_h($func_library->cutString($subject_preview, 70, '..')) ?></div>
							<div class="txt"><?= gh_h($func_library->cutString($content_preview, 80, '..')) ?></div>
							<div class="date"><?= gh_h((string) $regdate) ?></div>
						</a>
					</li>
				<?php }?>
			</ul>
			<div class="paging">
				<?=$func_library->getUserPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
			</div>

		</div>
	</section>
</div>