<?php
$ghPath = '../';
include_once($ghPath.'include/common/common.php');

if(preg_match('/..\//',urldecode($file_name)) || preg_match('/..\//',urldecode($o_file_name))){
	$funcLibrary->alert('잘못된 경로로 접근하셨습니다.');
}

if($board == 'Y'){
	$fileName = $file_name;
	$original = urldecode($o_file_name);
	$filepath = realpath($ghPath.'data/board/'.$bbsid.'/'.$file_name);
}else{
	$file_name = $file_name;
	$original = urldecode($o_file_name);
	$filepath = realpath($ghPath.'data/'.$bbsid.'/'.$file_name);
}

if(!file_exists($filepath)){
	$funcLibrary->alert('잘못된 경로로 접근하셨습니다.');
}

//Mac NFD 방식-> 윈도우,리눅스 방식으로 정규화
if(class_exists('Normalizer')){
	if(Normalizer::isNormalized($original, Normalizer::FORM_D)){
		$original = Normalizer::normalize($original, Normalizer::FORM_C);
	}
}

$original = iconv('UTF-8','cp949//IGNORE',$original);

if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])){
	header("content-type: doesn/matter");
	header("content-length: ".filesize("$filepath"));
	header("content-disposition: attachment; filename=\"$original\"");
	header("content-transfer-encoding: binary");
}else{
	header("content-type: file/unknown");
	header("content-length: ".filesize("$filepath"));
	header("content-disposition: attachment; filename=\"$original\"");
	header("content-description: php generated data");
}
header("pragma: no-cache");
header("expires: 0");

$fp = fopen("$filepath", "rb");

// 4.00 대체
// 서버부하를 줄이려면 print 나 echo 또는 while 문을 이용한 방법보다는 이방법이...
if(!fpassthru($fp)){
	fclose($fp);
}

flush();
?>