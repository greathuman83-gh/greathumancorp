<?php
$ghPath = '../../';
include_once($ghPath.'include/html/admin_top.php');
?>

<?php if($adminSuper){?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="menuCode" value="<?=$menuCode?>">
<tr>
	<td width="100%" align="right">
		<select name="keyType" class="input_select">
			<option value="a_id" <?php if($keyType == 'a_id'){?>selected<?php }?>><?=$_pageText['아이디']?></option>
			<option value="a_name" <?php if($keyType == 'a_name'){?>selected<?php }?>><?=$_pageText['이름']?></option>
		</select>
		<input type="text" name="keyword" value="<?=$keyword?>" class="input_text">
		<button type="submit" class="search_btn"><?=$_pageText['검색']?></button>
	</td>
</tr>
</form>
</table>
<?php }?>

<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<col width="80" align="center"></col>
	<col width="200" align="center"></col>
	<col></col>
	<col width="150" align="center"></col>
	<col width="120" align="center"></col>
	<col width="110" align="center"></col>
	<tr><td colspan="12" class="line1"></td></tr>
	<tr class="bgcol1 bold ht center">
		<td><?=$_pageText['번호']?></td>
		<td><?=$_pageText['타입']?></td>
		<td><?=$_pageText['아이디']?></td>
		<td><?=$_pageText['이름']?></td>
		<td><?=$_pageText['등록일']?></td>
		<td><?php if($adminSuper){?><button type="button" class="red_btn" onclick="window.location='./manager_form.php?<?=$funcLibrary->queryString('w')?>w=a'"><?=$_pageText['등록']?></button></a><?php }?></td>
	</tr>
	<tr><td colspan="12" class='line2'></td></tr>
	<?php
		$where = " where language = :language ";
		$bindParam[] = array('language',LANGUAGE);

		if($keyType || $keyword){
			$where = $where." and $keyType like :keyword ";
			$bindParam[] = array('keyword', $keyword,'like');
		}

		if(!$adminSuper){
			$where = $where." and a_id = :adminId ";
			$bindParam[] = array('adminId',$adminId);
		}
		
		$orderby = "super desc|a_level desc|idx desc";
		$listResult = $queryLibrary->getList($where,$bindParam,'gh_admin',$orderby,$pg,10);
		$number = $listResult['number'];
		foreach($listResult['result'] as $d){
		$regdate= substr($d['regdate'],0,10);
	?>
	<tr class="col2 ht center">
		<td><?=$number?></td>
		<td>
			<?=$_adminLevel[$d['a_level']]?>
		</td>
		<td><a href="./manager_form.php?<?=$funcLibrary->queryString('idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['a_id']?></a></td>
		<td><?=$d['a_name']?></td>
		<td><?=$regdate?></td>
		<td>
			<?php if($adminSuper){?>
				<a href="./manager_form.php?<?=$funcLibrary->queryString('idx,w')?>w=u&idx=<?=$d['idx']?>"><button type="button" class="black_icon_btn"><?=$_pageText['수정']?></button></a>
				<?php if($d['super'] != 1){?>
					<button type="button" class="gray_icon_btn" onclick="if(confirm('<?=$_pageText['정말 삭제하시겠습니까?']?>'))location.href='./manager_ok.php?<?=$funcLibrary->queryString('idx,w')?>w=d&idx=<?=$d['idx']?>';"><?=$_pageText['삭제']?></button>
				<?php }?>
			<?php }else{?>
				<?php if($adminId == $d['a_id']){?>
					<button type="button" class="black_icon_btn" onclick="window.location='./manager_form.php?<?=$funcLibrary->queryString('w')?>w=u&idx=<?=$d['idx']?>'"><?=$_pageText['수정']?></button>
				<?php }?>
			<?php }?>
		</td>
	</tr>
	<tr><td colspan="12" class="line2"></td></tr>
<?php $number--;}?>
</table>
<br>
<table width="95%" align="center" class="adminMenuTable">
<tr>
	<td align="center">
		<?=$funcLibrary->getPaging($_config['pageListEa'],$pg,$listResult['totalPage'], $_SERVER['PHP_SELF'].'?'.$funcLibrary->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once($ghPath.'/include/html/admin_bottom.php'); ?>