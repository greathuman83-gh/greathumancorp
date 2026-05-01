<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');
$boardInfo = $queryLibrary->getBoardInfo($bbsid);
if(!$boardInfo){
	$funcLibrary->alert('없는 게시판 입니다.');
}

$categoryTable = 'gh_category_table'; //카테고리 데이터
?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="bbsid" value="<?=$bbsid?>">
<input type="hidden" name="menuCode" value="<?=$menuCode?>">
<tr>
	<td width="100%" align="right">
		<input type="text" name="startDate" class="input_text date" value="<?=$startDate?>" placeholder="시작일"> ~ 
		<input type="text" name="endDate" class="input_text date" value="<?=$endDate?>" placeholder="종료일">
		<?php if($boardInfo['b_cate']){?>
			<select name="cate" class="input_select">
				<option value="">- 분류 전체 -</option>
				<?php
					//1차 분류
					$where = "where depth = 1 and category = :bbsid";
					$bindParam[] = array('bbsid',$bbsid);
					$orderby = "num asc|c_code asc|idx desc";
					$listResult = $queryLibrary->getList($where,$bindParam,$categoryTable,$orderby,1,99);
					foreach($listResult['result'] as $cateData){
					unset($bindParam);
				?>
					<option value="<?=$cateData['c_code']?>" <?php if($cate == $cateData['c_code']){?>selected<?php }?>><?=$cateData['c_name']?></option>
					<?php
						//2차 분류
						$where2 = "where parent = :parent and substring(c_code,1,3) = :c_code and depth = 2";
						$bindParam[] = array('parent',$cateData['parent']);
						$bindParam[] = array('c_code',$cateData['c_code']);
						$orderby2 = "num asc|c_code asc|idx desc";
						$listResult2 = $queryLibrary->getList($where2,$bindParam,$categoryTable,$orderby2,1,99);
						foreach($listResult2['result'] as $cateData2){
						unset($bindParam);
					?>
						<option value="<?=$cateData2['c_code']?>" <?php if($cate == $cateData2['c_code']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;2차 : <?=$cateData2['c_name']?></option>
						<?php
							//3차 분류
							$where3 = "where parent = :parent and substring(c_code,1,6) = :c_code and depth = 3";
							$bindParam[] = array('parent',$cateData2['parent']);
							$bindParam[] = array('c_code',$cateData2['c_code']);
							$orderby3 = "num asc|c_code asc|idx desc";
							$listResult3 = $queryLibrary->getList($where3,$bindParam,$categoryTable,$orderby3,1,99);
							foreach($listResult3['result'] as $cateData3){
							unset($bindParam);
						?>
							<option value="<?=$cateData3['c_code']?>" <?php if($cate == $cateData3['c_code']){?>selected<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3차 : <?=$cateData3['c_name']?></option>
						<?php }//3차 분류?>
					<?php }//2차 분류?>
				<?php }//1차 분류?>
			</select>
		<?php }?>
		<select name="keyType" class="input_select">
			<option value="b_subject" <?php if($keyType == 'b_subject'){?>selected<?php }?>>타이틀</option>
			<option value="b_name" <?php if($keyType == 'b_name'){?>selected<?php }?>>작성자</option>
			<option value="b_content" <?php if($keyType == 'b_content'){?>selected<?php }?>>내용</option>
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
<?php if($boardInfo['b_type'] == '2'){?>
<col width="150"></col>
<?php }?>
<?php if($boardInfo['b_cate']){?>
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
	<?php if($boardInfo['b_type'] == '2'){?>
	<td>이미지</td>
	<?php }?>
	<?php if($boardInfo['b_cate']){?>
	<td>분류</td>
	<?php }?>
	<td>타이틀</td>
	<?php if($bbsid == 'recruit' || $bbsid == 'event'){?>
	<td>기간 / 상태</td>
	<?php }?>
	<td>글쓴이</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="window.location='./board_form.php?<?=$funcLibrary->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$orderby = "regdate desc|b_parent desc|depth asc|idx desc";
	$where = " where b_notice = 1 ";
	$listResult = $queryLibrary->getList($where,'',"gh_board_".$bbsid,$orderby,1,100);
	foreach($listResult['result'] as $d){
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
		$iconFile = '<span class="file"></span>';
	}else{
		$iconFile = '';
	}

	$categoryWhere = " where c_code = :c_code and category = :bbsid ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', substr($d['b_cate'] ??= '',0,3));
	$categoryBindParam[] = array('bbsid', $bbsid);
	$categoryData1 = $queryLibrary->getData2($categoryWhere,$categoryBindParam,$categoryTable);

	$categoryWhere = " where c_code = :c_code and category = :bbsid ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', $d['b_cate']);
	$categoryBindParam[] = array('bbsid', $bbsid);
	$categoryData2 = $queryLibrary->getData2($categoryWhere,$categoryBindParam,$categoryTable);

	if($d['b_open'] == '1'){
		$openStatus = '<button type="button" class="blue_icon_btn">공개</button>';
	}else{
		$openStatus = '<button type="button" class="gray_icon_btn">비공개</button>';
	}

?>
<tr class="list col1 ht center">
	<td ><span class="notice">공지</span></td>
	<td><?=$openStatus?></td>
	<?php if($boardInfo['b_type'] == '2'){?>
		<td><?php if($d['file_thumb']){?><img src="<?=$ghPath?>/data/board/<?=$bbsid?>/<?=$d['file_thumb']?>" width="120" style="margin:5px;"><?php }?></td>
	<?php }?>
	<?php if($boardInfo['b_cate']){?>
		<td><?=$categoryData1['c_name']?></td>
	<?php }?>
	<td align="left" style="padding-left:5px;"><a href="board_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$subject?></a><?=$iconFile?></td>
	<td><?=$d['b_name']?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='board_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='board_ok.php?<?=$funcLibrary->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php }?>
<?php
	$bindParam = array();
	$where = " where (b_notice is null or b_notice <> '1') ";
	if($keyType || $keyword){
		$where = $where." and $keyType like :keyword";
		$bindParam[] = array('keyword', $keyword,'like');
	}

	if($cate){
		$where = $where." and b_cate=:b_cate ";
		$bindParam[] = array('b_cate', $cate);
	}

	if($startDate&& $endDate){
		$where = $where." and (substring(regdate,1,10) BETWEEN :startDateAND :endDate) ";
		$bindParam[] = array('start_date', $start_date);
		$bindParam[] = array('endDate', $endDate);
	}else{
		if($startDate&& !$endDate){
			$where = $where." and (substring(regdate,1,10) >= :start_date) ";
			$bindParam[] = array('start_date', $start_date);
		}else if(!$startDate&& $endDate){
			$where = $where." and (substring(regdate,1,10) <= :endDate) ";
			$bindParam[] = array('endDate', $endDate);
		}
	}


	$orderby = "list_num desc|regdate desc|b_parent desc|depth asc|idx desc";

	$listResult = $queryLibrary->getList($where,$bindParam,"gh_board_".$bbsid,$orderby,$pg,10);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
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
		$iconFile = '<span class="file"></span>';
	}else{
		$iconFile = '';
	}

	$categoryWhere = " where c_code = :c_code and category = :bbsid ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', substr($d['b_cate'] ??= '',0,3));
	$categoryBindParam[] = array('bbsid', $bbsid);
	$categoryData1 = $queryLibrary->getData2($categoryWhere,$categoryBindParam,$categoryTable);

	$categoryWhere = " where c_code = :c_code and category = :bbsid ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', $d['b_cate']);
	$categoryBindParam[] = array('bbsid', $bbsid);
	$categoryData2 = $queryLibrary->getData2($categoryWhere,$categoryBindParam,$categoryTable);
	//==========채용공고 데이터=============
	if($bbsid == 'recruit' || $bbsid == 'event'){
		$startDate = date("Y.m.d",strtotime(substr($d['b_data1'],0,10)));
		$endDate = date("Y.m.d",strtotime(substr($d['b_data2'],0,10)));
		$dDay = intval((strtotime($d['b_data2'])-strtotime(GH_TIME_YMD)) / 86400);

		if($d['b_data3'] == '1'){//채용중
			if($d['b_data4']){//상시모집
				$dDayText = '채용중';
			}else{//날짜체크
				if($dDay < 0){
					$dDayText = '마감';
				}else if($dDay == 0){
					$dDayText = 'D-Day';
				}else{
					$dDayText = 'D-'.$dDay;
				}
			}
		}else{
			$dDayText = '마감';
		}

		if($d['b_data4']){
			$recruitPeriod = '상시';
		}else{
			$recruitPeriod = $startDate.' ~ '.$endDate;
		}
	}
	//==========채용공고 데이터 끝===============
	if($d['b_open'] == '1'){
		$openStatus = '<button type="button" class="blue_icon_btn">공개</button>';
	}else{
		$openStatus = '<button type="button" class="gray_icon_btn">비공개</button>';
	}
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<td><?=$openStatus?></td>
	<?php if($boardInfo['b_type'] == '2'){?>
		<td class="td2"><?php if($d['file_thumb']){?><img src="<?=$ghPath?>/data/board/<?=$bbsid?>/<?=$d['file_thumb']?>" width="120" style="margin:5px;"><?php }?></td>
	<?php }?>
	<?php if($boardInfo['b_cate']){?>
		<td><?=$categoryData1['c_name'] ?? null?></td>
	<?php }?>
	<td align="left" style="padding-left:5px;"><a href="board_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>"><?=$subject?></a><?=$iconFile?></td>
	<?php if($bbsid == 'recruit' || $bbsid == 'event'){?>
		<td>
			<?=$recruitPeriod?> / <?=$dDayText?>
			<?php if($bbsid == 'event'){?>
				<button type="button" class="green_icon_btn" onclick="window.location='../request/event_list.php?p_idx=<?=$d['idx']?>&menuCode=011002'">신청자보기</button>
			<?php }?>
		</td>
	<?php }?>
	<td><?=$d['b_name']?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='board_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='board_ok.php?<?=$funcLibrary->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center" class="adminMenuTable">
<tr>
	<td align="center">
		<?php echo $funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>