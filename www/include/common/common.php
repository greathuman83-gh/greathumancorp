<?php
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

//보안서버 체크
if (isset($_SERVER['HTTPS'])) {
	define('HTTP_PROTOCOL', 'https');
	define('COOKIE_SECURE', true);
} else {
	define('HTTP_PROTOCOL', 'http');
	define('COOKIE_SECURE', false);
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

// SSL·민감 페이지 캐싱 금지 (브라우저·프록시 공통)
// 주의: Cache-Control은 한 번만 설정해야 함
header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, pre-check=0, post-check=0');
header('Pragma: no-cache');




//https로 리다이렉트
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' || $_SERVER['SERVER_PORT'] == 80) {
	$https_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header('Location: ' . $https_url, true, 301);
	exit();
}




// 브라우저 Accept-Language의 최우선(q·목록 순) 언어에 따라 로케일 리다이렉트
// ko → / , ja → /jp/ , 그 외 → /en/  (수동 선택 쿠키 manual_lang 있으면 생략)
if (!function_exists('gh_parse_accept_language_primary')) {
	function gh_parse_accept_language_primary(string $header): ?string
	{
		$header = trim($header);
		if ($header === '') {
			return null;
		}
		$parts = preg_split('/\s*,\s*/', $header);
		$candidates = [];
		foreach ($parts as $i => $part) {
			if (!preg_match('/^([a-zA-Z*]+(?:[-_][a-zA-Z0-9]+)*)(?:\s*;\s*q\s*=\s*([0-9.]+))?$/i', $part, $m)) {
				continue;
			}
			$q = isset($m[2]) ? (float) $m[2] : 1.0;
			$tag = strtolower(str_replace('_', '-', $m[1]));
			$candidates[] = ['q' => $q, 'idx' => $i, 'tag' => $tag];
		}
		if ($candidates === []) {
			return null;
		}
		usort($candidates, static function ($a, $b) {
			if ($a['q'] === $b['q']) {
				return $a['idx'] <=> $b['idx'];
			}
			return $b['q'] <=> $a['q'];
		});
		$primary = $candidates[0]['tag'];
		if ($primary === '*' || $primary === '') {
			return null;
		}
		$segments = explode('-', $primary);
		return strtolower($segments[0]);
	}
}
if (!function_exists('gh_strip_locale_path_prefix')) {
	function gh_strip_locale_path_prefix(string $path): string
	{
		if (preg_match('#^/jp(?=/|$)#', $path)) {
			$rest = substr($path, strlen('/jp'));
			return ($rest === '' || $rest === '/') ? '/' : $rest;
		}
		if (preg_match('#^/en(?=/|$)#', $path)) {
			$rest = substr($path, strlen('/en'));
			return ($rest === '' || $rest === '/') ? '/' : $rest;
		}
		return $path === '' ? '/' : $path;
	}
}
if (!function_exists('gh_build_locale_path')) {
	function gh_build_locale_path(string $locale, string $stripped): string
	{
		if ($stripped === '' || $stripped === '/') {
			if ($locale === 'ko') {
				return '/';
			}
			if ($locale === 'ja') {
				return '/jp/';
			}
			return '/en/';
		}
		if ($locale === 'ko') {
			return $stripped;
		}
		if ($locale === 'ja') {
			return '/jp' . $stripped;
		}
		return '/en' . $stripped;
	}
}
if (!function_exists('gh_locale_from_path')) {
	function gh_locale_from_path(string $path): string
	{
		if (preg_match('#^/jp(?=/|$)#', $path)) {
			return 'ja';
		}
		if (preg_match('#^/en(?=/|$)#', $path)) {
			return 'en';
		}
		return 'ko';
	}
}


/* // 수동 언어 선택이 있으면 쿠키로 저장해 자동 언어 리다이렉트를 비활성화
$manual_lang = $_GET['lang'] ?? '';
if (in_array($manual_lang, ['ko', 'jp', 'en'], true)) {
	setcookie('manual_lang', $manual_lang, [
		'expires' => time() + (60 * 60 * 24 * 30),
		'path' => '/',
		'secure' => COOKIE_SECURE,
		'httponly' => true,
		'samesite' => 'Strict'
	]);
	$_COOKIE['manual_lang'] = $manual_lang;
}

$accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$request_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$request_path = $request_path === '' ? '/' : $request_path;

if (
	empty($_COOKIE['manual_lang'])
	&& strpos($request_path, '/admode/') !== 0
) {
	$primary = gh_parse_accept_language_primary($accept_language);
	if ($primary !== null) {
		if ($primary === 'ko') {
			$target_locale = 'ko';
		} elseif ($primary === 'ja') {
			$target_locale = 'ja';
		} else {
			$target_locale = 'en';
		}
		$current_locale = gh_locale_from_path($request_path);
		if ($target_locale !== $current_locale) {
			$stripped = gh_strip_locale_path_prefix($request_path);
			$new_path = gh_build_locale_path($target_locale, $stripped);
			$query = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
			$loc_url = 'https://' . $_SERVER['HTTP_HOST'] . $new_path;
			if ($query !== null && $query !== '') {
				$loc_url .= '?' . $query;
			}
			header('Location: ' . $loc_url, true, 301);
			exit();
		}
	}
}
 */



/**
 * GET과 POST만 병합(동일 키는 POST가 우선). $_REQUEST는 cookies 등이 섞여 전역 변수 오염이 생길 수 있어 사용하지 않음.
 * 다차원 배열은 array_replace_recursive로 병합한다. 문자열은 NUL 바이트만 제거한다.
 */
if (!function_exists('gh_strip_null_bytes_recursive')) {
	function gh_strip_null_bytes_recursive(mixed $v): mixed
	{
		if (is_array($v)) {
			return array_map('gh_strip_null_bytes_recursive', $v);
		}

		return is_string($v) ? str_replace("\0", '', $v) : $v;
	}
}

$_GH_MERGED_REQUEST = gh_strip_null_bytes_recursive(array_replace_recursive($_GET, $_POST));
extract($_GH_MERGED_REQUEST, EXTR_SKIP);

/* 특수 문자를 HTML 엔티티로 변환 */
if (!function_exists('gh_h')) {
	function gh_h(?string $v): string
	{
		return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}
}

/* 관리자 및 Community DB 연결 인스턴스 생성 */
include_once __DIR__ . '/class-conn.php';
/** @var PDO $conn */
$conn = CONN::getInstance();

/* 기능 클래스 모음 */
include_once __DIR__ . '/class-func-library.php';
/** @var FuncLibrary $func_library */
$func_library = new FuncLibrary();

/* 쿼리 클래스 모음 */
include_once __DIR__ . '/class-query-library.php';
/** @var QueryLibrary $query_library */
$query_library = new QueryLibrary($conn, $func_library);
include_once __DIR__ . '/config.php';


if (isset($_SESSION['language'])) { //언어 설정
	include_once __DIR__ . '/../language/language_' . $_SESSION['language'] . '.php';
} else {
	include_once __DIR__ . '/../language/language_ko.php';
}
