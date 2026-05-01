<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$tableName = 'gh_inquiry_table';
?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="pageType" value="<?=$pageType?>">
<input type="hidden" name="menuCode" value="<?=$menuCode?>">
<tr>
	<td width="100%" align="right">
		<select name="keyType" class="input_select">
			<option value="r_name" <?php if($keyType == 'r_name'){?>selected<?php }?>>이름</option>
		</select>
		<input type="text" name="keyword" value="<?=$keyword?>" class="input_text">
		<button type="submit" class="search_btn">검색</button>
	</td>
</tr>
</form>
</table>

<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="100" align="center"></col>
<col width="150"></col>
<col></col>
<col width="100"></col>
<col width="100"></col>
<col width="110" align="center"></col>
<tr><td colspan="9" class="line1"></td></tr>
<tr class='bgcol1 bold col1 ht center'>
	<td>번호</td>
	<td>이름</td>
	<td>이메일</td>
	<td>상태</td>
	<td>등록일</td>
	<td><!-- <button type="button" class="red_btn" onclick="window.location='./inquiry_form.php?<?=$funcLibrary->queryString('pg')?>w=a'">등록</button> --></td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php
	$where = "where 1=1 ";

	if($pageType){
		$where .= " and page_type = :pageType ";
		$bindParam[] = array('pageType', $pageType);
	}

	if($cate){
		$where .= " and category = :cate ";
		$bindParam[] = array('cate', $cate);
	}

	if($keyType || $keyword){
		$where = $where." and $keyType like :keyword ";
		$bindParam[] = array('keyword', $keyword,'like');
	}

	/*
	if($keyType && $keyword){
		$where = $where." and SUBSTRING_INDEX(SUBSTRING_INDEX(form_value, '|', $keyType),'|',-1)  like :keyword ";
		$bindParam[] = array('keyword', $keyword,'like');
	}*/

	if($startDate && $endDate){
		$where = $where." and (substring(regdate,1,10) BETWEEN :startDate AND :endDate) ";
		$bindParam[] = array('startDate', $startDate);
		$bindParam[] = array('endDate', $endDate);
	}else{
		if($startDate && !$endDate){
			$where = $where." and (substring(regdate,1,10) >= :startDate) ";
			$bindParam[] = array('startDate', $startDate);
		}else if(!$startDate && $endDate){
			$where = $where." and (substring(regdate,1,10) <= :endDate) ";
			$bindParam[] = array('endDate', $endDate);
		}
	}

	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,'',$pg,10);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
	$regdate= substr($d['regdate'],0,10);

	if($d['status'] == '1'){
		$status = '<span style="color:blue">확인중</span>';
	}else{
		$status = '<span style="color:red">완료</span>';
	}

	$categoryWhere = " where c_code = :c_code and category = 'inquiry' and depth = '1' ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', $d['category']);
	$categoryData = $queryLibrary->getData2($categoryWhere,$categoryBindParam,'gh_category_table');
?>
<tr class="list col1 ht center" height="30">
	<td><?=$number?></td>
	<td><a href="inquiry_form.php?<?=$funcLibrary->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['r_name']?></a></td>
	<td><?=$d['r_email']?></td>
	<td><?=$status?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="location.href='./inquiry_form.php?<?=$funcLibrary->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./inquiry_ok.php?<?=$funcLibrary->queryString('pg,idx,w')?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class='line2'></td></tr>
<?php $number--; }?>
</table>
<br>
<table width="95%" align="center">
<tr>
	<td align="center">
		<?=$funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>

<!-- <button type="button" class="search_btn" onclick="xls_down();" style="float:right">엑셀 다운로드</button>
<script type="text/javascript">
	function xls_down(){
		window.location = "./inquiry_excel.php?<?=$funcLibrary->queryString('pg,idx,w')?>";
	}
</script> -->
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>