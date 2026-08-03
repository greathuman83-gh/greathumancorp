<?php
$gh_path = '../';
include_once __DIR__ . '/' . $gh_path . 'include/common/common.php';

$board       = isset($board) ? (string)$board : '';
$bbsid       = isset($bbsid) ? (string)$bbsid : '';
$file_name   = isset($file_name) ? (string)$file_name : '';
$o_file_name = isset($o_file_name) ? (string)$o_file_name : '';

// 게시판 식별자(bbsid)는 영숫자/언더스코어/하이픈만 허용해 경로 조작을 차단한다.
if($bbsid === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $bbsid)){
	$func_library->alert('잘못된 경로로 접근하셨습니다.');
}

// 실제 다운로드 파일명은 basename만 사용해 경로 구분자를 제거한다.
$file_name = $func_library->safeBoardUploadBasename($file_name);
if($file_name === ''){
	$func_library->alert('잘못된 경로로 접근하셨습니다.');
}

// 표시용 파일명은 헤더 인젝션 방지를 위해 제어문자/따옴표를 제거한다.
$original = urldecode($o_file_name);
$original = str_replace(array("\r", "\n", "\0", '"'), '', $original);

if($board == 'Y'){
	$base_dir = realpath(__DIR__ . '/' . $gh_path.'data/board/'.$bbsid);
}else{
	$base_dir = realpath(__DIR__ . '/' . $gh_path.'data/'.$bbsid);
}

if($base_dir === false){
	$func_library->alert('잘못된 경로로 접근하셨습니다.');
}

$filepath = realpath($base_dir . '/' . $file_name);

// 파일 존재 여부와 함께 허용된 base 디렉토리 내부 경로인지 검증한다.
if($filepath === false || !is_file($filepath) || strpos($filepath, $base_dir . DIRECTORY_SEPARATOR) !== 0){
	$func_library->alert('잘못된 경로로 접근하셨습니다.');
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