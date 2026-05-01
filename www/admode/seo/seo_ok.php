<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$DB = new DBManager($conn);

$tableName = 'gh_seo_table';
$uploadDirectory = 'seo';

if($del_file1 ??= null){
	@unlink($ghPath."data/$uploadDirectory/$old_file1");
	$inputs['file1'] = '';
}

if($_FILES['file1'] ??= null){
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if($file && $file_size>0){
		@unlink($ghPath."data/$uploadDirectory/$old_file1");
		$mfile = $funcLibrary->uploadFile('file1','',$ghPath."data/$uploadDirectory");
		$inputs['file1'] = $mfile['filename'];
	}
}

$inputs['title'] = $title ?? null;
$inputs['meta_description'] = $meta_description ?? null;
$inputs['meta_keywords'] = $meta_keywords ?? null;
$inputs['title_en'] = $title_en ?? null;
$inputs['meta_description_en'] = $meta_description_en ?? null;
$inputs['meta_keywords_en'] = $meta_keywords_en ?? null;
$inputs['title_jp'] = $title_jp ?? null;
$inputs['meta_description_jp'] = $meta_description_jp ?? null;
$inputs['meta_keywords_jp'] = $meta_keywords_jp ?? null;
$inputs['og_use'] = $og_use ?? null;
$inputs['og_title'] = $og_title ?? null;
$inputs['og_description'] = $og_description ?? null;
$inputs['og_url'] = $og_url ?? null;
$inputs['og_type'] = $og_type ?? null;
$inputs['og_locale'] = $og_locale ?? null;
$inputs['og_sitename'] = $og_sitename ?? null;
$inputs['og_title_en'] = $og_title_en ?? null;
$inputs['og_description_en'] = $og_description_en ?? null;
$inputs['og_url_en'] = $og_url_en ?? null;
$inputs['og_locale_en'] = $og_locale_en ?? null;
$inputs['og_sitename_en'] = $og_sitename_en ?? null;
$inputs['og_title_jp'] = $og_title_jp ?? null;
$inputs['og_description_jp'] = $og_description_jp ?? null;
$inputs['og_url_jp'] = $og_url_jp ?? null;
$inputs['og_locale_jp'] = $og_locale_jp ?? null;
$inputs['og_sitename_jp'] = $og_sitename_jp ?? null;
$inputs['og_image_width'] = $og_image_width ?? null;
$inputs['og_image_height'] = $og_image_height ?? null;

if($w == 'u'){
	$where[] = array('idx', $idx,'and');
	if(!$DB->updateSet($tableName,$inputs,$where)){
		$funcLibrary->alert('문제가 발생하였습니다.');
	}else{
		$funcLibrary->alert('수정되었습니다.','./seo_form.php?'.$funcLibrary->queryString());
	}

}
include_once($ghPath.'include/common/dbclose.php');
?>