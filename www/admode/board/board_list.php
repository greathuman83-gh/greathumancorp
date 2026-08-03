<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
$board_info = $query_library->getBoardInfo($bbsid);
if(!$board_info){
	$func_library->alert('없는 게시판 입니다.');
}

$category_table = 'gh_category_table'; //카테고리 데이터
?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="bbsid" value="<?=$bbsid?>">
<input type="hidden" name="menu_code" value="<?=$menu_code?>">
<tr>
	<td width="100%" align="right">
		<input type="text" name="start_date" class="input_text date" value="<?= gh_h($start_date) ?>"> ~
		<input type="text" name="end_date" class="input_text date" value="<?= gh_h($end_date) ?>">
		<?php if($board_info['b_cate']){?>
			<select name="cate" class="input_select">
				<option value="">- 분류 전체 -</option>
				<?php
					//1차 분류
					$where = "where depth = 1 and category = :bbsid";
					$bind_param[] = array('bbsid',$bbsid);
					$orderby = "num asc|c_code asc|idx desc";
					$list_result = $query_library->getList($where,$bind_param,$category_table,$orderby,1,99);
					foreach($list_result['result'] as $cate_data){
					unset($bind_param);
				?>
					<option value="<?=$cate_data['c_code']?>" <?php if($cate == $cate_data['c_code']){?>selected<?php }?>><?=$cate_data['c_name']?></option>
					<?php
						//2차 분류
						$where2 = "where parent = :parent and substring(c_code,1,3) = :c_code and depth = 2";
						$bind_param[] = array('parent',$cate_data['parent']);
						$bind_param[] = array('c_code',$cate_data['c_code']);
						$orderby2 = "num asc|c_code asc|idx desc";
						$list_result2 = $query_library->getList($where2,$bind_param,$category_table,$orderby2,1,99);
						foreach($list_result2['result'] as $cate_data2){
						unset($bind_param);
					?>
						<option value="<?=$cate_data2['c_code']?>" <?php if($cate == $cate_data2['c_code']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;2차 : <?=$cate_data2['c_name']?></option>
						<?php
							//3차 분류
							$where3 = "where parent = :parent and substring(c_code,1,6) = :c_code and depth = 3";
							$bind_param[] = array('parent',$cate_data2['parent']);
							$bind_param[] = array('c_code',$cate_data2['c_code']);
							$orderby3 = "num asc|c_code asc|idx desc";
							$list_result3 = $query_library->getList($where3,$bind_param,$category_table,$orderby3,1,99);
							foreach($list_result3['result'] as $cate_data3){
							unset($bind_param);
						?>
							<option value="<?=$cate_data3['c_code']?>" <?php if($cate == $cate_data3['c_code']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3차 : <?=$cate_data3['c_name']?></option>
						<?php }//3차 분류?>
					<?php }//2차 분류?>
				<?php }//1차 분류?>
			</select>
		<?php }?>
		<select name="key_type" class="input_select">
			<option value="b_subject" <?php if($key_type == 'b_subject'){?>selected<?php }?>>타이틀</option>
			<option value="b_name" <?php if($key_type == 'b_name'){?>selected<?php }?>>작성자</option>
			<option value="b_content" <?php if($key_type == 'b_content'){?>selected<?php }?>>내용</option>
		</select>
		<input type="text" name="keyword" value="<?=$keyword?>" class="input_text">
		<button type="submit" class="search_btn">검색</button>
	</td>
</tr>
</form>
</table>

<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="80" align="center"></col>
<col width="80"></col>
<?php if($board_info['b_type'] == '2'){?>
<col width="150"></col>
<?php }?>
<?php if($board_info['b_cate']){?>
<col width="200"></col>
<?php }?>
<col></col>
<?php if($bbsid == 'recruit' || $bbsid == 'event'){?>
<col width="400"></col>
<?php }?>
<col width="110"></col>
<col width="110"></col>
<col width="110" align="center"></col>
<tr><td colspan="10" class="line1"></td></tr>
<tr class="bgcol1 bold col1 ht center">
	<td>번호</td>
	<td>공개여부</td>
	<?php if($board_info['b_type'] == '2'){?>
	<td>이미지</td>
	<?php }?>
	<?php if($board_info['b_cate']){?>
	<td>분류</td>
	<?php }?>
	<td>타이틀</td>
	<?php if($bbsid == 'recruit' || $bbsid == 'event'){?>
	<td>기간 / 상태</td>
	<?php }?>
	<td>글쓴이</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="window.location='./board_form.php?<?=$func_library->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
	$where = " where b_notice = 1 ";
	$list_result = $query_library->getList($where,'',"gh_board_".$bbsid,$orderby,1,100);
	foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
	$reply = '';
	if(strlen($d['depth'] ?? '') >0){
		for($i=0;$i<strlen($d['depth']);$i++){
			$reply .= '&nbsp;';
		}
		$reply .= '└ ';
	}
	$subject = $reply.$d['b_subject'];
	if($d['b_file']){
		$icon_file = '<span class="file"></span>';
	}else{
		$icon_file = '';
	}

	$category_where = " where c_code = :c_code and category = :bbsid ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', substr($d['b_cate'] ??= '',0,3));
	$category_bind_param[] = array('bbsid', $bbsid);
	$category_data1 = $query_library->getData2($category_where,$category_bind_param,$category_table);

	$category_where = " where c_code = :c_code and category = :bbsid ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', $d['b_cate']);
	$category_bind_param[] = array('bbsid', $bbsid);
	$category_data2 = $query_library->getData2($category_where,$category_bind_param,$category_table);

	if($d['b_open'] == '1'){
		$open_status = '<button type="button" class="blue_icon_btn">공개</button>';
	}else{
		$open_status = '<button type="button" class="gray_icon_btn">비공개</button>';
	}

?>
<tr class="list col1 ht center">
	<td ><span class="notice">공지</span></td>
	<td><?=$open_status?></td>
	<?php if($board_info['b_type'] == '2'){?>
		<td><?php if($d['file_thumb']){?><img src="/data/board/<?= gh_h($bbsid) ?>/<?= gh_h($d['file_thumb']) ?>" width="120" style="margin:5px;"><?php }?></td>
	<?php }?>
	<?php if($board_info['b_cate']){?>
		<td><?=$category_data1['c_name']?></td>
	<?php }?>
	<td align="left" style="padding-left:5px;"><a href="board_form.php?<?=$func_library->queryString()?>w=u&idx=<?=(int)$d['idx']?>"><?= gh_h($subject) ?></a><?=$icon_file?></td>
	<td><?= gh_h($d['b_name'] ?? '') ?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='board_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='board_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php }?>
<?php
	$bind_param = array();
	$where = " where (b_notice is null or b_notice <> '1') ";
	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
	$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'b_subject');

	if($cate){
		$where = $where." and b_cate=:b_cate ";
		$bind_param[] = array('b_cate', $cate);
	}

	if($start_date&& $end_date){
		$where = $where." and (substring(regdate,1,10) BETWEEN :start_date AND :end_date) ";
		$bind_param[] = array('start_date', $start_date);
		$bind_param[] = array('end_date', $end_date);
	}else{
		if($start_date&& !$end_date){
			$where = $where." and (substring(regdate,1,10) >= :start_date) ";
			$bind_param[] = array('start_date', $start_date);
		}else if(!$start_date&& $end_date){
			$where = $where." and (substring(regdate,1,10) <= :end_date) ";
			$bind_param[] = array('end_date', $end_date);
		}
	}


	$orderby = "list_num desc|regdate desc|b_parent desc|depth asc|idx desc";

	$list_result = $query_library->getList($where,$bind_param,"gh_board_".$bbsid,$orderby,$pg,10);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);
	$reply = '';
	if(strlen($d['depth'] ??= '') >0){
		for($i=0;$i<strlen($d['depth']);$i++){
			$reply .= '&nbsp;';
		}
		$reply .= '└ ';
	}
	$subject = $reply.$d['b_subject'];
	if($d['b_file']){
		$icon_file = '<span class="file"></span>';
	}else{
		$icon_file = '';
	}

	$category_where = " where c_code = :c_code and category = :bbsid ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', substr($d['b_cate'] ??= '',0,3));
	$category_bind_param[] = array('bbsid', $bbsid);
	$category_data1 = $query_library->getData2($category_where,$category_bind_param,$category_table);

	$category_where = " where c_code = :c_code and category = :bbsid ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', $d['b_cate']);
	$category_bind_param[] = array('bbsid', $bbsid);
	$category_data2 = $query_library->getData2($category_where,$category_bind_param,$category_table);
	//==========채용공고 데이터=============
	if($bbsid == 'recruit' || $bbsid == 'event'){
		$start_date = date("Y.m.d",strtotime(substr($d['b_data1'],0,10)));
		$end_date = date("Y.m.d",strtotime(substr($d['b_data2'],0,10)));
		$d_day = intval((strtotime($d['b_data2'])-strtotime(GH_TIME_YMD)) / 86400);

		if($d['b_data3'] == '1'){//채용중
			if($d['b_data4']){//상시모집
				$d_day_text = '채용중';
			}else{//날짜체크
				if($d_day < 0){
					$d_day_text = '마감';
				}else if($d_day == 0){
					$d_day_text = 'D-Day';
				}else{
					$d_day_text = 'D-'.$d_day;
				}
			}
		}else{
			$d_day_text = '마감';
		}

		if($d['b_data4']){
			$recruit_period = '상시';
		}else{
			$recruit_period = $start_date.' ~ '.$end_date;
		}
	}
	//==========채용공고 데이터 끝===============
	if($d['b_open'] == '1'){
		$open_status = '<button type="button" class="blue_icon_btn">공개</button>';
	}else{
		$open_status = '<button type="button" class="gray_icon_btn">비공개</button>';
	}
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<td><?=$open_status?></td>
	<?php if($board_info['b_type'] == '2'){?>
		<td class="td2"><?php if($d['file_thumb']){?><img src="/data/board/<?= gh_h($bbsid) ?>/<?= gh_h($d['file_thumb']) ?>" width="120" style="margin:5px;"><?php }?></td>
	<?php }?>
	<?php if($board_info['b_cate']){?>
		<td><?=$category_data1['c_name'] ?? null?></td>
	<?php }?>
	<td align="left" style="padding-left:5px;"><a href="board_form.php?<?=$func_library->queryString()?>w=u&idx=<?=(int)$d['idx']?>"><?= gh_h($subject) ?></a><?=$icon_file?></td>
	<?php if($bbsid == 'recruit' || $bbsid == 'event'){?>
		<td>
			<?=$recruit_period?> / <?=$d_day_text?>
			<?php if($bbsid == 'event'){?>
				<button type="button" class="green_icon_btn" onclick="window.location='../request/event_list.php?p_idx=<?=$d['idx']?>&menu_code=011002'">신청자보기</button>
			<?php }?>
		</td>
	<?php }?>
	<td><?= gh_h($d['b_name'] ?? '') ?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='board_form.php?<?=$func_library->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='board_ok.php?<?=$func_library->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center" class="adminMenuTable">
<tr>
	<td align="center">
		<?php echo $func_library->getPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>