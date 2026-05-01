<?php
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

//보안서버 체크
if(isset($_SERVER['HTTPS'])){
	define('HTTP_PROTOCOL','https');
	define('COOKIE_SECURE',true);
}else{
	define('HTTP_PROTOCOL','http');
	define('COOKIE_SECURE',false);
}

session_set_cookie_params([
	'lifetime' => 0, // 브라우저 닫으면 만료
	'path' => '/',
	//'domain' => 'your-domain.com',
	'secure' => COOKIE_SECURE,
	'httponly' => true,
	'samesite' => 'Strict'
]);

if (session_status() === PHP_SESSION_NONE) {
	ini_set('session.gc_maxlifetime', 3600); //세션 유지시간 1시간
	@session_start();
}

header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); //지난시간으로 설정
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
header('Expires: 0');




//https로 리다이렉트
/*
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' || $_SERVER['SERVER_PORT'] == 80){
	$httpsUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('Location: ' . $httpsUrl, true, 301);
	exit();
}
*/

@extract($_GET,EXTR_SKIP);
@extract($_POST,EXTR_SKIP);
@extract($_REQUEST,EXTR_SKIP);

/* 관리자 및 Community DB 연결 인스턴스 생성 */
include_once('dbopen.class.php');
$conn = CONN::getInstance();

/* 기능 클래스 모음 */
include_once('func.class.php');
$funcLibrary = new FuncLibrary();

/* 쿼리 클래스 모음 */
include_once('query.class.php');
$queryLibrary = new QueryLibrary($conn,$funcLibrary);
include_once('config.php');


if(isset($_SESSION['language'])){//언어 설정
	include_once(dirname(__FILE__).'/../language/language_'.$_SESSION['language'].'.php');
}else{
	include_once(dirname(__FILE__).'/../language/language_ko.php');
}
?>