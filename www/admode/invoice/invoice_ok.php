<?php
$ghPath = '../../';
include_once($ghPath . 'include/common/common.php');
include_once($ghPath . 'include/common/permission.php');
include_once($ghPath . 'include/common/db.class.php');


$DB = new DBManager($conn);

$tableName = 'gh_invoice_table';
$uploadDirectory = 'invoice';

if ($delete_file1 ??= null) {
	@unlink($ghPath . "data/$uploadDirectory/$old_file1");
	$inputs['file1'] = '';
	$inputs['file1_name'] = '';
}

if ($_FILES['file1'] ??= null) {
	$file = $_FILES['file1']['tmp_name'];
	$file_size = $_FILES['file1']['size'];
	if ($file && $file_size > 0) {
		@unlink($ghPath . "data/$uploadDirectory/$old_file1");
		$mfile = $funcLibrary->uploadFile('file1', '', $ghPath . "data/$uploadDirectory");
		$inputs['file1'] = $mfile['filename'];
		$inputs['file1_name'] = $mfile['originalFileName'];

		//$resizeData = image_resize($ghPath.'data/board/$bbsid/',$mfile['filename'],397,235,$mfile['img_type'],1,95);
	}
}





$inputs['title'] = $title ?? null;
$inputs['content'] = $content ?? null;
$inputs['i_company'] = $i_company ?? null;
if ($i_price == '' || $i_price == null) {
	$inputs['i_price'] = 0;
} else {
	$inputs['i_price'] = $i_price;
}
$inputs['i_date'] = $i_date ?? null;


if ($w == 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');
	$inputs['category'] = $pageType;

	if (!$DB->insertInto($tableName, $inputs)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('등록되었습니다.', './invoice_list.php?' . $funcLibrary->queryString('idx,w'));
	}
} else if ($w == 'u') {

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('수정되었습니다.', './invoice_form.php?' . $funcLibrary->queryString());
	}
} else if ($w == 'd') {
	$d = $queryLibrary->getData($idx, $tableName);

	$where[] = array('idx', $idx);
	if (!$DB->delete_db($tableName, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		if (!empty($d['file1'])) {
			@unlink($ghPath . "data/$uploadDirectory/" . $d['file1']);
		}

		$funcLibrary->alert('삭제 되었습니다.', './invoice_list.php?' . $funcLibrary->queryString('idx,w'));
	}
} else if ($w == 'oe') { //순서 변경
	$inputs = array();
	$inputs['num'] = $num;

	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	} else {
		$funcLibrary->alert('수정되었습니다.', './invoice_list.php?' . $funcLibrary->queryString('idx,w'));
	}
}
include_once($ghPath . 'include/common/dbclose.php');
