<div class="page bbs faq">
	<div class="innerwrap">
		<h3 class="pageTitle1"><?= gh_h($page_depth3 ?? '') ?></h3>
		<ul class="category cate1" id="categoryTab">
			<li <?php if($bbsid == 'faq1'){?>class="on"<?php }?>><a href="?bbsid=faq1">일반 사용자</a></li>
			<li <?php if($bbsid == 'faq2'){?>class="on"<?php }?>><a href="?bbsid=faq2">개발자</a></li>
		</ul>
		<div class="slideTabs">
			<a class="m_Selected">전체보기</a>
			<ul class="category cate2">
				<li<?php if(!$cate){?> class="on"<?php }?>><a href="?bbsid=<?= gh_h((string) $bbsid) ?>">전체보기</a></li>
				<?php
					//1차 분류
					$bind_param = array();
					$where = "where bbstype = :bbsid and useyn= 'Y' ";
					$bind_param[] = array('bbsid',$bbsid);
					$orderby = "category_idx asc";
					$list_result = $query_library->getList($where,$bind_param,$board_category_table,$orderby,1,99,'category_idx');
					foreach($list_result['result'] as $cate_data){
				?>
					<li<?php if($cate == $cate_data['category_idx']){?> class="on"<?php }?>><a href="?cate=<?= (int)($cate_data['category_idx'] ?? 0) ?>&bbsid=<?= gh_h((string) $bbsid) ?>#categoryTab"><?= gh_h($cate_data['categoryname'] ?? '') ?></a></li>
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
				$bind_param = array();
				$where = " where b_notice = '1' and bbstype = :bbsid and flag = 'Y' ";
				$bind_param[] = array('bbsid', $bbsid);
				$orderby = "b_number desc|rdate desc|bbs_idx desc";
				$list_result = $query_library->getList($where,$bind_param,$board_table_name,$orderby,1,15,'bbs_idx');
				$number = $list_result['number'];
				foreach($list_result['result'] as $d){
				// $regdate= substr($d['rdate'],0,10);
				$regdate= $d['rdate'];
				//게시판분류
				$category_data = $query_library->getData($d['category'],$board_category_table,'category_idx');

				//첨부파일체크
				$file_bind_param = array();
				$where = "where bbs_idx = :bbs_idx";
				$file_bind_param[] = array('bbs_idx', $d['bbs_idx']);
				$total = $query_library->dataTotal($where,$file_bind_param,$board_file_table);
				if($total > 0){
					$icon_file = '<span class="file"></span>';
				}else{
					$icon_file = '';
				}

				$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['bbs_idx'] ?? 0) . '"';
			?>
				<li class="notice">
					<a <?=$view_link?>>
						<div class="num"><?= (int) $number ?></div>
						<div class="cate"><span class="notice">공지</span></div>
						<div class="tit"><?= gh_h($func_library->cutString($d['title'] ?? '', 60, '..')) ?></div>
						<div class="date"><span class="h_tit">등록일</span><?= gh_h((string) $regdate) ?></div>
						<div class="hit"><span class="h_tit">조회수</span><?=number_format($d['viewcnt'])?></div>
					</a>
				</li>
			<?php $number--;}?>
			<?php
				$bind_param = array();
				$where = " where (b_notice is null or b_notice <> '1' ) and bbstype = :bbsid and flag = 'Y' ";
				$bind_param[] = array('bbsid', $bbsid);
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

				if($cate){
					$where .= " and category = :category ";
					$bind_param[] = array('category', $cate);
				}
				$orderby = "rdate desc|ref desc|ref_level asc|bbs_idx desc";

				$list_result = $query_library->getList($where,$bind_param,$board_table_name,$orderby,$pg,10,'bbs_idx');
				$number = $list_result['number'];
				foreach($list_result['result'] as $d){
				// $regdate = date('Y.m.d',strtotime(substr($d['rdate'],0,10)));
				$regdate =$d['rdate'];
				//게시판분류
				$category_data = $query_library->getData($d['category'],$board_category_table,'category_idx');


				//첨부파일체크
				$file_bind_param = array();
				$where = "where bbs_idx = :bbs_idx";
				$file_bind_param[] = array('bbs_idx', $d['bbs_idx']);
				$total = $query_library->dataTotal($where,$file_bind_param,'tb_bbs_attach');
				if($total > 0){
					$icon_file = '<span class="file"></span>';
				}else{
					$icon_file = '';
				}

				$view_link = 'href="?' . $func_library->queryString('idx,w') . 'idx=' . (int)($d['bbs_idx'] ?? 0) . '"';
			?>
				<li class="">
					<a <?=$view_link?>>
						<div class="num"><span class="h_tit">번호</span><?= (int) $number ?></div>
						<div class="cate">[<?= gh_h($category_data['categoryname'] ?? '') ?>]</div>
						<div class="tit"><?= gh_h($func_library->cutString($d['title'] ?? '', 60, '..')) ?></div>
						<div class="date"><span class="h_tit">등록일</span><?= gh_h((string) $regdate) ?></div>
						<div class="hit"><span class="h_tit">조회수</span><?=number_format($d['viewcnt'])?></div>
					</a>
				</li>			
				
			<?php $number--;}?>
		</ul>
		<form name="searchForm" method="get">
		<input type="hidden" name="bbsid" value="<?= gh_h((string) $bbsid) ?>">
		<div class="bbs_searchwrap">
			<select name="key_type">
				<option value="all" <?php if($key_type == 'all'){?>selected<?php }?>>전체</option>
				<option value="title" <?php if($key_type == 'title'){?>selected<?php }?>>제목</option>
				<option value="content" <?php if($key_type == 'content'){?>selected<?php }?>>내용</option>
			</select>
			<input type="text" name="keyword" value="<?= gh_h((string) ($keyword ?? '')) ?>" placeholder="검색어 입력">
			<button type="submit"><i></i> <span>검색하기</span></button>
		</div>
		</form>
		<div class="paging">
			<?=$func_library->getUserPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
		</div>
	</div>
</div>
<script src="/include/js/date-utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const utcDateModule = new dateUtils(document.querySelectorAll('.list1 li'));
	utcDateModule.utcDateToLocalDate();
});
</script>