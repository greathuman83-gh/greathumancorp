<div class="page bbs faq">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?=$pageDepth3?></h3>
		<ul class="category cate1" id="categoryTab">
			<li <?php if($bbsid == 'faq1'){?>class="on"<?php }?>><a href="?bbsid=faq1">일반 사용자</a></li>
			<li <?php if($bbsid == 'faq2'){?>class="on"<?php }?>><a href="?bbsid=faq2">개발자</a></li>
		</ul>
		<div class="slideTabs">
			<a class="m_Selected">전체보기</a>
			<ul class="category cate2">
				<li<?php if(!$cate){?> class="on"<?php }?>><a href="?bbsid=<?=$bbsid?>">전체보기</a></li>
				<?php
					//1차 분류
					$bindParam = array();
					$where = "where bbstype = :bbsid and useyn= 'Y' ";
					$bindParam[] = array('bbsid',$bbsid);
					$orderby = "category_idx asc";
					$listResult = $queryLibrary->getList($where,$bindParam,$boardCategoryTable,$orderby,1,99,'category_idx');
					foreach($listResult['result'] as $cateData){
				?>
					<li<?php if($cate == $cateData['category_idx']){?> class="on"<?php }?>><a href="?cate=<?=$cateData['category_idx']?>&bbsid=<?=$bbsid?>#categoryTab"><?=$cateData['categoryname']?></a></li>
				<?php }?>
			</ul>
		</div>

		<ul class="titlearea type2">
			<li class="num">번호</li>
			<li class="cate">분류</li>
			<li class="tit">제목</li>
			<li class="date">등록일</li>
			<li class="hit">조회수</li>
		</ul>
		<ul class="list1 type2">
			<?php
				$bindParam = array();
				$where = " where b_notice = '1' and bbstype = :bbsid and flag = 'Y' ";
				$bindParam[] = array('bbsid', $bbsid);
				$orderby = "b_number desc|rdate desc|bbs_idx desc";
				$listResult = $queryLibrary->getList($where,$bindParam,$boardTableName,$orderby,1,15,'bbs_idx');
				$number = $listResult['number'];
				foreach($listResult['result'] as $d){
				// $regdate= substr($d['rdate'],0,10);
				$regdate= $d['rdate'];
				//게시판분류
				$categoryData = $queryLibrary->getData($d['category'],$boardCategoryTable,'category_idx');

				//첨부파일체크
				$fileBindParam = array();
				$where = "where bbs_idx = :bbs_idx";
				$fileBindParam[] = array('bbs_idx', $d['bbs_idx']);
				$total = $queryLibrary->dataTotal($where,$fileBindParam,$boardFileTable);
				if($total > 0){
					$iconFile = '<span class="file"></span>';
				}else{
					$iconFile = '';
				}

				$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['bbs_idx'].'"';
			?>
				<li class="notice">
					<a <?=$viewLink?>>
						<div class="num"><?=$number?></div>
						<div class="cate"><span class="notice">공지</span></div>
						<div class="tit"><?=$funcLibrary->cutString($d['title'],60,'..')?></div>
						<div class="date"><span class="h_tit">등록일</span><?=$regdate?></div>
						<div class="hit"><span class="h_tit">조회수</span><?=number_format($d['viewcnt'])?></div>
					</a>
				</li>
			<?php $number--;}?>
			<?php
				$bindParam = array();
				$where = " where (b_notice is null or b_notice <> '1' ) and bbstype = :bbsid and flag = 'Y' ";
				$bindParam[] = array('bbsid', $bbsid);
				if($keyType || $keyword){
					if($keyType == 'all'){
						$where .= " and (title like :keyword or content like :keyword) ";
					}else{
						$where .= " and ".$funcLibrary->escapeQuery($keyType)." like :keyword ";
					}
					$bindParam[] = array('keyword', $keyword,'like');
				}

				if($cate){
					$where .= " and category = :category ";
					$bindParam[] = array('category', $cate);
				}
				$orderby = "rdate desc|ref desc|ref_level asc|bbs_idx desc";

				$listResult = $queryLibrary->getList($where,$bindParam,$boardTableName,$orderby,$pg,10,'bbs_idx');
				$number = $listResult['number'];
				foreach($listResult['result'] as $d){
				// $regdate = date('Y.m.d',strtotime(substr($d['rdate'],0,10)));
				$regdate =$d['rdate'];
				//게시판분류
				$categoryData = $queryLibrary->getData($d['category'],$boardCategoryTable,'category_idx');


				//첨부파일체크
				$fileBindParam = array();
				$where = "where bbs_idx = :bbs_idx";
				$fileBindParam[] = array('bbs_idx', $d['bbs_idx']);
				$total = $queryLibrary->dataTotal($where,$fileBindParam,'tb_bbs_attach');
				if($total > 0){
					$iconFile = '<span class="file"></span>';
				}else{
					$iconFile = '';
				}

				$viewLink = 'href="?'.$funcLibrary->queryString('idx,w').'idx='.$d['bbs_idx'].'"';
			?>
				<li class="">
					<a <?=$viewLink?>>
						<div class="num"><span class="h_tit">번호</span><?=$number?></div>
						<div class="cate">[<?=$categoryData['categoryname']?>]</div>
						<div class="tit"><?=$funcLibrary->cutString($d['title'],60,'..')?></div>
						<div class="date"><span class="h_tit">등록일</span><?=$regdate?></div>
						<div class="hit"><span class="h_tit">조회수</span><?=number_format($d['viewcnt'])?></div>
					</a>
				</li>			
				
			<?php $number--;}?>
		</ul>
		<form name="searchForm" method="get">
		<input type="hidden" name="bbsid" value="<?=$bbsid?>">
		<div class="bbs_searchwrap">
			<select name="keyType">
				<option value="all" <?php if($keyType == 'all'){?>selected<?php }?>>전체</option>
				<option value="title" <?php if($keyType == 'title'){?>selected<?php }?>>제목</option>
				<option value="content" <?php if($keyType == 'content'){?>selected<?php }?>>내용</option>
			</select>
			<input type="text" name="keyword" value="<?=$keyword?>" placeholder="검색어 입력">
			<button type="submit"><i></i> <span>검색하기</span></button>
		</div>
		</form>
		<div class="paging">
			<?=$funcLibrary->getUserPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
		</div>
	</div>
</div>
<script src="/include/js/date_utils.js"></script>
<script>
$(function(){
	const utcDateModule = new dateUtils($('.list1 li'));
	utcDateModule.utcDateToLocalDate();
});
</script>