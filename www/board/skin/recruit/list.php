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
$list_result = $query_library->getList($where,$bind_param,'gh_board_'.$bbsid,$orderby,$pg,5);
$number = $list_result['number'];
?>
<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> recruit">

	<div class="page_top pn<?= (int) $pn ?> sn<?= (int) $sn ?>">
		<div class="page_tit"><?= gh_h($dep2 ?? '') ?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
		
			<div class="total">
				총 <span><?=number_format($list_result['list_total'])?>건</span>의 채용공고가 있습니다.
			</div>
			<ul class="recruit_list">
				<?php
					foreach($list_result['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$start_date = date('Y.m.d',strtotime(substr($d['b_data1'],0,10)));
					$end_date = date('Y.m.d',strtotime(substr($d['b_data2'],0,10)));

					//Dday 설정
					$d_day = intval((strtotime($d['b_data2'])-strtotime(GH_TIME_YMD)) / 86400);
					if($d['b_data3'] == '1'){//진행중
						if($d_day < 0){//마감됨
							$d_day_text = '서류마감';
							$end_class = 'class="end"';
							$imminent = '';
							$status_text = '마감';
						}else if($d_day == 0){//D-Day
							$d_day_text = 'D-day';
							$end_class = '';
							$imminent = 'imminent';
							$status_text = '채용중';
						}else if($d_day <= 7 && $d_day > 0){//D-Day
							$d_day_text = 'D-'.$d_day;
							$end_class = '';
							$imminent = 'imminent';
							$status_text = '채용중';
						}else{//진행중
							$d_day_text = 'D-'.$d_day;
							$end_class = '';
							$imminent = '';
							$status_text = '채용중';
						}
					}else{
						$d_day_text = '서류마감';
						$end_class = 'class="end"';
						$imminent = '';
						$status_text = '마감';
					}
					$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['idx'] ?? 0) . '"';

					$recruit_type_array = explode('|',$d['b_data5'] ?? '');
				?>
					<li <?=$end_class?>>
						<a <?=$view_link?>>
							<div class="info">
								<div class="tit"><?= gh_h($func_library->cutString($d['b_subject'] ?? '', 100, '..')) ?></div>
								<div class="tag">
									<div class="dday <?= gh_h($imminent ?? '') ?>"><?= gh_h((string) $d_day_text) ?></div>
									<?php
										foreach($recruit_type_array as $key => $val){
											if($val){
												echo '<div>' . gh_h($_recruitType[$val] ?? '') . '</div>';
											}
										}
									?>
								</div>
								<div class="date"><img src="/images/page/m53_date_icon.png" alt=""><?= gh_h((string) $start_date) ?> ~ <?= gh_h((string) $end_date) ?></div>
							</div>
							<div class="state">
								<span><?= gh_h((string) $status_text) ?></span>
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
	
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
	document.querySelectorAll('.recruit_list .tit').forEach(function(el) {
		var text = el.textContent;
		if (text.length > 25) {
			el.textContent = text.substring(0, 25) + '...';
		}
	});
});
</script>