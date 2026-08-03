<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$table_name = 'gh_category_table';
$upload_directory = 'category';
$cate = 'case';
$DB = new DBManager($conn);

if ($w != 'd') {

	if (isset($del_file1)) {
		@unlink($gh_path . "data/$upload_directory/" . $del_file1);
		$inputs['file1'] = '';
	}


	if (isset($_FILES['file1'])) {
		$file = $_FILES['file1']['tmp_name'];
		$file_size = $_FILES["file1"]['size'];
		if ($file && $file_size > 0) {
			@unlink($gh_path . "data/$upload_directory/$old_file1");
			$mfile = $func_library->uploadFile('file1', '', $gh_path . 'data/' . $upload_directory);
			$inputs['file1'] = $mfile['filename'];
		}
	}

	$inputs['c_name'] = $c_name ?? null;
	$inputs['c_open'] = $c_open ?? null;
	$inputs['c_text1'] = $c_text1 ?? null;
}

if ($w == 'a') {
	$inputs['category'] = $cate;
	$inputs['regdate'] = date('Y-m-d H:i:s');

	if ($depth == 1) {
		$bind_param[] = array('cate', $cate);
		$d = $query_library->getDataCustom($table_name, "c_code desc", "c_code", "where category =:cate and depth=1", $bind_param);
		unset($bind_param);

		$c_code = $d ? $d['c_code'] + 1 : 1;
		$c_code = sprintf("%03d", $c_code);
	} else {
		$count = ($depth - 1) * 3;
		$bind_param[] = array('parent', $parent);
		$bind_param[] = array('ccode', $ccode);
		$bind_param[] = array('depth', $depth);

		$d = $query_library->getDataCustom($table_name, "c_code desc", "c_code", "where parent=:parent and substring(c_code,1,$count) = :ccode and depth=:depth", $bind_param);
		$c_code = $d ? substr($d['c_code'], $count, 3) + 1 : 1;
		$c_code = sprintf("%03d", $c_code);
		$c_code = $ccode . $c_code;
		$inputs['parent'] = $parent;
	}

	$inputs['c_code'] = $c_code;
	$inputs['depth'] = $depth;


	if (!$DB->insertInto($table_name, $inputs)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		if ($depth == 1) {
			$idx = $conn->lastInsertId();
			$sql = "update $table_name set parent = :parent where idx = :idx";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':parent', $idx);
			$stmt->bindParam(':idx', $idx);
			$stmt->execute();
		}

		$func_library->alert('등록되었습니다.', './category_list.php?' . $func_library->queryString('idx,w'));
	}
} else if ($w == 'u') {
	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($table_name, $inputs, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('수정되었습니다.', './category_list.php?' . $func_library->queryString('idx,w'));
	}
} else if ($w == 'd') {
	$d = $query_library->getData($idx, $table_name);

	//======== 하위 카테고리 확인 ===========
	$count = $d['depth'] * 3;

	$bind_param[] = array('parent', $d['parent']);
	$bind_param[] = array('c_code', $d['c_code']);
	$bind_param[] = array('depth', $d['depth'] + 1);

	$total = $query_library->getDataCustom($table_name, '', "count(*) as total", "where parent=:parent and substring(c_code,1,$count) = :c_code and depth=:depth", $bind_param);

	if ($total['total'] > 0) {
		$func_library->alert('하위 카테고리가 존재합니다.\\n하위 카테고리 삭제 후 다시 시도해 주세요.');
	}
	unset($bind_param);
	//===============================

	//======== 등록된 제품 확인 ============
	/*
	$bind_param[] = array('c_code',$d['c_code']);
	
	$total = $query_library->getDataCustom("gh_product_table","","count(*) as total","where c_code = :c_code",$bind_param);
	
	if($total['total'] >0){
		$func_library->alert("해당 브랜드에 등록된 제품이 존재합니다..\\n등록된 제품을 삭제하거나 변경하시고 시도해 주세요.");
		exit;
	}
	unset($bind_param);
	*/
	//===============================

	@unlink($gh_path . "data/$upload_directory/" . $d['file1']);
	$where[] = array('idx', $idx);
	if (!$DB->delete_db($table_name, $where)) {
		$func_library->alert('문제가 발생하였습니다.');
	} else {
		$func_library->alert('삭제 되었습니다.', './category_list.php?' . $func_library->queryString('idx,w'));
	}
}

include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
