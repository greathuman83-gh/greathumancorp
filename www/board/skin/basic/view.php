<?php
$regdate = date('d.m.Y',strtotime(substr($d['regdate'],0,10)));
?>
<div id="main" class="m00 m<?=$pn?>0 m<?=$pn?><?=$sn?> bbs">
	<div class="section section1">
		<div class="innerwrap">
			<table class="bbsView" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tit">
						<div class="title"><?=$d['b_subject']?></div>
						<div class="date"><?=$regdate?></div>
					</td>
				</tr>
				<?php if(array_filter($b_file) != [] ){?>
					<tr>
						<td class="file">
							<?php for($i=0;$i<count((array)$b_file);$i++){?>
								<a href="./download.php?board=Y&bbsid=<?=$bbsid?>&file_name=<?=$b_file[$i]?>&o_file_name=<?=urlencode($b_file_name[$i])?>" download><?=$b_file_name[$i]?><i></i></a>
							<?php }?>
						</td>
					</tr>
				<?php }?>
				<tr>
					<td class="con"><?=$d['b_content']?></td>
				</tr>
				
			</table>
			<div class="view_paging">
				<a class="page_prev" <?php if($prevData){?>href="?<?=$funcLibrary->queryString('idx,w')?>&idx=<?=$prevData['bbs_idx']?>" <?php }?>>
					<div class="tit">이전글</div>
					<div class="arr"><img src="<?=NCP_GCDN_URL?>/images/page/view_paging_prev.png" alt=""></div>
					<div class="page_tit"><?php if($prevData){?><?=$funcLibrary->cutString($prevData['title'],80,'..')?><?php }else{?>이전글이 없습니다.<?php }?></div>
				</a>
				<a class="page_next" <?php if($nextData){?>href="?<?=$funcLibrary->queryString('idx,w')?>&idx=<?=$nextData['bbs_idx']?>" <?php }?>>
					<div class="tit">다음글</div>
					<div class="arr"><img src="<?=NCP_GCDN_URL?>/images/page/view_paging_next.png" alt=""></div>
					<div class="page_tit"><?php if($nextData){?><?=$funcLibrary->cutString($nextData['title'],80,'..')?><?php }else{?>다음글이 없습니다.<?php }?></div>
				</a>
			</div>
			<div class="btn_list">
				<a href="?<?=$funcLibrary->queryString('idx,w')?>">목록으로 가기 <i></i></a>
			</div>

		</div>
	</div>
</div>