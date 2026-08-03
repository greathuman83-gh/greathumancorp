<?php
$gh_path = '../../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/permission.php';
include_once __DIR__ . '/' . $gh_path . 'include/common/class-db-manager.php';

$robots_path = $gh_path.'robots.txt';

if($robots_content ?? ''){
	$fp = fopen($robots_path,'w+');
	fwrite($fp, $robots_content);
	fclose($fp);
	$func_library->alert('수정되었습니다.','./robots_form.php?'.$func_library->queryString('idx,w'));
}else{
	$func_library->alert('데이터가 없습니다.');
}
include_once __DIR__ . '/' . $gh_path . 'include/common/dbclose.php';
?>