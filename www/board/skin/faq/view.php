<div class="page bbsView">
	<div class="innerwrap">
		
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th>
					<div class="info">
						<span class="bbsId"><?=$pageDepth3?></span>
						<span class="dot">·</span>
						<span class="date"><?=$regdate2?></span>
					</div>
					<div class="tit">
						<?=$d['title']?>
					</div>
				</th>
			</tr>
			<?php
				//첨부파일
				$bindParam = array();
				$where = " where bbs_idx = :bbs_idx ";
				$bindParam[] = array('bbs_idx', $idx);
				$orderby = "attach_idx asc";
				$listResult = $queryLibrary->getList($where,$bindParam,'tb_bbs_attach',$orderby,1,(int)$boardInfo['b_file'],'attach_idx');
				if($listResult['listTotal'] > 0){
			?>
				<tr>
					<td>
						<div class="file">
							<?php foreach($listResult['result'] as $fileData){?>
								<?php if($updateDate < '2024-01-01'){//데이터 이전 게시물?>
									<a href="./download.php?board=Y&bbsid=<?=$bbsid?>&fileName=<?=$fileData['filename']?>&originalFileName=<?=urlencode($fileData['realfilename'])?>" download><?=$fileData['realfilename']?></a>
								<?php }else{?>
									<a href="./attach_download.php?bbsid=<?=$bbsid?>&idx=<?=$idx?>&attachIdx=<?=$fileData['attach_idx']?>">
										<?=$fileData['realfilename']?>
									</a>
								<?php }?>
							<?php }?>
						</div>
					</td>
				</tr>
			<?php }?>
			<tr>
				<td class="viewcon">
					<?=$d['content']?>
				</td>
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