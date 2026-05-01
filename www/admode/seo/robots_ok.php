<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');
include_once($ghPath.'include/common/db.class.php');

$robotsPath = $ghPath.'robots.txt';

if($robotsContent ?? ''){
	$fp = fopen($robotsPath,'w+');
	fwrite($fp, $robotsContent);
	fclose($fp);
	$funcLibrary->alert('수정되었습니다.','./robots_form.php?'.$funcLibrary->queryString('idx,w'));
}else{
	$funcLibrary->alert('데이터가 없습니다.');
}
include_once($ghPath.'include/common/dbclose.php');
?>