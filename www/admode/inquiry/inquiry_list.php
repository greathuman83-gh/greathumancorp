<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';

$table_name = 'gh_inquiry_table';
?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="page_type" value="<?=$page_type?>">
<input type="hidden" name="menu_code" value="<?=$menu_code?>">
<tr>
	<td width="100%" align="right">
		<select name="key_type" class="input_select">
			<option value="r_name" <?php if($key_type == 'r_name'){?>selected<?php }?>>이름</option>
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
	<td><!-- <button type="button" class="red_btn" onclick="window.location='./inquiry_form.php?<?=$func_library->queryString('pg')?>w=a'">등록</button> --></td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php
	$where = "where 1=1 ";

	if($page_type){
		$where .= " and page_type = :pageType ";
		$bind_param[] = array('pageType', $page_type);
	}

	if($cate){
		$where .= " and category = :cate ";
		$bind_param[] = array('cate', $cate);
	}

	// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
	$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'i_name');

	/*
	if($key_type && $keyword){
		$where = $where." and SUBSTRING_INDEX(SUBSTRING_INDEX(form_value, '|', $key_type),'|',-1)  like :keyword ";
		$bind_param[] = array('keyword', $keyword,'like');
	}*/

	if($start_date && $end_date){
		$where = $where." and (substring(regdate,1,10) BETWEEN :start_date AND :end_date) ";
		$bind_param[] = array('start_date', $start_date);
		$bind_param[] = array('end_date', $end_date);
	}else{
		if($start_date && !$end_date){
			$where = $where." and (substring(regdate,1,10) >= :start_date) ";
			$bind_param[] = array('start_date', $start_date);
		}else if(!$start_date && $end_date){
			$where = $where." and (substring(regdate,1,10) <= :end_date) ";
			$bind_param[] = array('end_date', $end_date);
		}
	}

	$list_result = $query_library->getList($where,$bind_param,$table_name,'',$pg,10);
	$number = $list_result['number'];
	foreach($list_result['result'] as $d){
	$regdate= substr($d['regdate'],0,10);

	if($d['status'] == '1'){
		$status = '<span style="color:blue">확인중</span>';
	}else{
		$status = '<span style="color:red">완료</span>';
	}

	$category_where = " where c_code = :c_code and category = 'inquiry' and depth = '1' ";
	$category_bind_param = array();
	$category_bind_param[] = array('c_code', $d['category']);
	$category_data = $query_library->getData2($category_where,$category_bind_param,'gh_category_table');
?>
<tr class="list col1 ht center" height="30">
	<td><?=$number?></td>
	<td><a href="inquiry_form.php?<?=$func_library->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['r_name']?></a></td>
	<td><?=$d['r_email']?></td>
	<td><?=$status?></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="location.href='./inquiry_form.php?<?=$func_library->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./inquiry_ok.php?<?=$func_library->queryString('pg,idx,w')?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class='line2'></td></tr>
<?php $number--; }?>
</table>
<br>
<table width="95%" align="center">
<tr>
	<td align="center">
		<?=$func_library->getPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
	</td>
</tr>
</table>

<!-- <button type="button" class="search_btn" onclick="xls_down();" style="float:right">엑셀 다운로드</button>
<script type="text/javascript">
	function xls_down(){
		window.location = "./inquiry_excel.php?<?=$func_library->queryString('pg,idx,w')?>";
	}
</script> -->
<?php include_once __DIR__ . '/' . $gh_path . 'include/html/admin_bottom.php'; ?>