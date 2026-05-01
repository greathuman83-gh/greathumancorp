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
$listResult = $queryLibrary->getList($where,$bindParam,'gh_board_'.$bbsid,$orderby,$pg,5);
$number = $listResult['number'];
?>
<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> recruit">

	<div class="page_top pn<?=$pn?> sn<?=$sn?>">
		<div class="page_tit"><?=$dep2?></div>
	</div>

	<div class="section section1">
		<div class="innerwrap">
		
			<div class="total">
				총 <span><?=number_format($listResult['listTotal'])?>건</span>의 채용공고가 있습니다.
			</div>
			<ul class="recruit_list">
				<?php
					foreach($listResult['result'] as $d){
					$regdate = date('Y.m.d',strtotime(substr($d['regdate'],0,10)));
					$startDate = date('Y.m.d',strtotime(substr($d['b_data1'],0,10)));
					$endDate = date('Y.m.d',strtotime(substr($d['b_data2'],0,10)));

					//Dday 설정
					$dDay = intval((strtotime($d['b_data2'])-strtotime(GH_TIME_YMD)) / 86400);
					if($d['b_data3'] == '1'){//진행중
						if($dDay < 0){//마감됨
							$dDayText = '서류마감';
							$endClass = 'class="end"';
							$imminent = '';
							$statusText = '마감';
						}else if($dDay == 0){//D-Day
							$dDayText = 'D-day';
							$endClass = '';
							$imminent = 'imminent';
							$statusText = '채용중';
						}else if($dDay <= 7 && $dDay > 0){//D-Day
							$dDayText = 'D-'.$dDay;
							$endClass = '';
							$imminent = 'imminent';
							$statusText = '채용중';
						}else{//진행중
							$dDayText = 'D-'.$dDay;
							$endClass = '';
							$imminent = '';
							$statusText = '채용중';
						}
					}else{
						$dDayText = '서류마감';
						$endClass = 'class="end"';
						$imminent = '';
						$statusText = '마감';
					}
					$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['idx'].'"';

					$recruitTypeArray = explode('|',$d['b_data5'] ?? '');
				?>
					<li <?=$endClass?>>
						<a <?=$viewLink?>>
							<div class="info">
								<div class="tit"><?=$funcLibrary->cutString($d['b_subject'],100,'..')?></div>
								<div class="tag">
									<div class="dday <?=$imminent ?? ''?>"><?=$dDayText?></div>
									<?php
										foreach($recruitTypeArray as $key => $val){
											if($val){
												echo '<div>'.$_recruitType[$val].'</div>';
											}
										}
									?>
								</div>
								<div class="date"><img src="/images/page/m53_date_icon.png" alt=""><?=$startDate?> ~ <?=$endDate?></div>
							</div>
							<div class="state">
								<span><?=$statusText?></span>
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
	
</div>

<script>
$(function(){
	

	$('.recruit_list .tit').each(function() {
		var text = $(this).text();
		if (text.length > 25) {
			$(this).text(text.substring(0, 25) + '...');
		}
	});
})
</script>