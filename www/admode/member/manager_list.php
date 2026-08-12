<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/html/admin_top.php';
?>

<?php if($admin_super){?>
<table width="100%" class="adminMenuTable">
<form name="fsearch" method="get">
<input type="hidden" name="menu_code" value="<?=$menu_code?>">
<tr>
	<td width="100%" align="right">
		<select name="key_type" class="input_select">
			<option value="a_id" <?php if($key_type == 'a_id'){?>selected<?php }?>><?=$_pageText['아이디']?></option>
			<option value="a_name" <?php if($key_type == 'a_name'){?>selected<?php }?>><?=$_pageText['이름']?></option>
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
	<tr class="bgcol1 bold col1 ht center">
		<td><?=$_pageText['번호']?></td>
		<td><?=$_pageText['타입']?></td>
		<td><?=$_pageText['아이디']?></td>
		<td><?=$_pageText['이름']?></td>
		<td><?=$_pageText['등록일']?></td>
		<td><?php if($admin_super){?><button type="button" class="red_btn" onclick="window.location='./manager_form.php?<?=$func_library->queryString('w')?>w=a'"><?=$_pageText['등록']?></button><?php }?></td>
	</tr>
	<tr><td colspan="12" class='line2'></td></tr>
	<?php
		$where = " where language = :language ";
		$bind_param[] = array('language',LANGUAGE);

		// 검색 컬럼 — escapeQuery 화이트리스트 후 LIKE
		$func_library->appendWhereLike($where, $bind_param, $key_type, $keyword, 'a_id');

		if(!$admin_super){
			$where = $where." and a_id = :admin_id ";
			$bind_param[] = array('admin_id',$admin_id);
		}
		
		$orderby = "super desc|a_level desc|idx desc";
		$list_result = $query_library->getList($where,$bind_param,'gh_admin',$orderby,$pg,10);
		$number = $list_result['number'];
		foreach($list_result['result'] as $d){
		$regdate= substr($d['regdate'],0,10);
	?>
	<tr class="list col1 ht center">
		<td><?=$number?></td>
		<td>
			<?=$_adminLevel[$d['a_level']]?>
		</td>
		<td><a href="./manager_form.php?<?=$func_library->queryString('idx,w')?>w=u&idx=<?=$d['idx']?>"><?=$d['a_id']?></a></td>
		<td><?=$d['a_name']?></td>
		<td><?=$regdate?></td>
		<td>
			<?php if($admin_super){?>
				<a href="./manager_form.php?<?=$func_library->queryString('idx,w')?>w=u&idx=<?=$d['idx']?>"><button type="button" class="black_icon_btn"><?=$_pageText['수정']?></button></a>
				<?php if($d['super'] != 1){?>
					<button type="button" class="gray_icon_btn" onclick="if(confirm('<?=$_pageText['정말 삭제하시겠습니까?']?>'))location.href='./manager_ok.php?<?=$func_library->queryString('idx,w')?>w=d&idx=<?=$d['idx']?>';"><?=$_pageText['삭제']?></button>
				<?php }?>
			<?php }else{?>
				<?php if($admin_id == $d['a_id']){?>
					<button type="button" class="black_icon_btn" onclick="window.location='./manager_form.php?<?=$func_library->queryString('w')?>w=u&idx=<?=$d['idx']?>'"><?=$_pageText['수정']?></button>
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
		<?=$func_library->getPaging($_config['page_list_ea'],$pg,$list_result['total_page'], $_SERVER['PHP_SELF'].'?'.$func_library->queryString('pg').'pg=')?>
	</td>
</tr>
</table>
<?php include_once __DIR__ . '/' . $gh_path . '/include/html/admin_bottom.php'; ?>