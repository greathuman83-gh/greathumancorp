<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');

$categoryTable = 'gh_category_table';
$tableName = 'gh_portfolio_table';
?>

<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="menuCode" value="<?=$menuCode?>">
<tr>
	<!-- <td align="left"><button type="button" class="black_btn" onclick="window.location='./portfolio_sort.php?<?=$funcLibrary->queryString()?>'">순서관리</button></td> -->
	<td align="right">
		<select name="ccode" class="input_select">
			<option value="">- 전체 -</option>
			<?php
				$bindParam = array();
				$where = "where category = 'portfolio' and depth = '1' ";
				$orderby = "num asc|c_code asc|idx desc";
				$listResult = $queryLibrary->getList($where,'',$categoryTable,$orderby,1,99);
				foreach($listResult['result'] as $cateData){
			?>
				<option value="<?=$cateData['c_code']?>" <?php if($ccode == $cateData['c_code']){?>selected<?php }?>><?=$cateData['c_name']?></option>
			<?php }?>
		</select>
		<select name="keyType" class="input_select">
			<option value="title" <?php if($keyType == 'title'){?>selected<?php }?>>타이틀</option>
		</select>
		<input type="text" name="keyword" value="<?=$keyword?>" class="input_text">
		<button type="submit" class="search_btn">검색</button>
	</td>
</tr>
</form>
</table>
<table cellpadding="0" cellspacing="0" class="adminMenuTable">
<col width="100" align="center"></col>
<!-- <col width="100" align="center"></col> -->
<col width="100" align="center"></col>
<col width="250" align="center"></col>
<col width="250" align="center"></col>
<col></col>
<col width="110" align="center"></col>
<col width="110" align="center"></col>
<tr><td colspan="9" class="line1"></td></tr>
<tr class="bgcol1 bold col1 ht center">
	<td>번호</td>
	<!-- <td>순서</td> -->
	<td>공개여부</td>
	<td>분류</td>
	<td>이미지</td>
	<td>타이틀</td>
	<td>등록일</td>
	<td><button type="button" class="red_btn" onclick="window.location='./portfolio_form.php?<?=$funcLibrary->queryString('w')?>w=a'">등록</button></td>
</tr>
<?php
	$bindParam = array();
	$where = " where 1=1 ";
	
	if($ccode){
		$where .= " and c_code = :ccode";
		$bindParam[] = array('ccode',$ccode);
	}

	if($keyType|| $keyword){
		$where .= " and $keyType like :keyword ";
		$bindParam[] = array('keyword', $keyword,'like');
	}

	$orderby = "num asc|regdate desc|idx desc";
	$listResult = $queryLibrary->getList($where,$bindParam,$tableName,$orderby,$pg,10);
	$number = $listResult['number'];
	foreach($listResult['result'] as $d){
	$regdate= substr($d['regdate'],0,10);

	$categoryWhere = " where c_code = :c_code and category = 'portfolio' and depth = '1' ";
	$categoryBindParam = array();
	$categoryBindParam[] = array('c_code', $d['c_code']);
	$categoryData = $queryLibrary->getData2($categoryWhere,$categoryBindParam,$categoryTable);
	$categoryText = $categoryData['c_name'];
?>
<tr class="list col1 ht center">
	<td><?=$number?></td>
	<!-- <td>
		<form action="./portfolio_ok.php?<?=$funcLibrary->queryString()?>w=oe&idx=<?=$d['idx']?>" method="post">
			<input type="number" name="num" class="input_text" value="<?=$d['num']?>" style="width:50px;" min="1">
			<button type="submit" class="black_icon_btn">변경</button>
		</form>
	</td> -->
	<td>
		<?php if($d['p_open'] == '1'){?>
			<button type="button" class="blue_icon_btn">공개</button>
		<?php }else{?>
			<button type="button" class="gray_icon_btn">비공개</button>
		<?php }?>
	</td>
	<td><?=$categoryText?></td>
	<td class="td2"><?php if($d['thumb_file']){?><img src="<?=$ghPath?>data/portfolio/<?=$d['thumb_file']?>" width="200"><?php }?></td>
	<td class="td2"><a href="./portfolio_form.php?<?=$funcLibrary->queryString('pg,idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['title']?></a></td>
	<td><?=$regdate?></td>
	<td>
		<button type="button" class="black_icon_btn" onclick="window.location='./portfolio_form.php?<?=$funcLibrary->queryString()?>w=u&idx=<?=$d['idx']?>'">수정</button>
		<button type="button" class="gray_icon_btn" onclick="if(confirm('정말 삭제하시겠습니까?'))location.href='./portfolio_ok.php?<?=$funcLibrary->queryString()?>w=d&idx=<?=$d['idx']?>';">삭제</button>
	</td>
</tr>
<tr><td colspan="9" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center">
<tr>
	<td align="center">
		<?=$funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once $ghPath.'include/html/admin_bottom.php'; ?>