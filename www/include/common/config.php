<?php
//============== 사용자 입력값 sql_injection 및 xss 방어 코드 ================
$param = $_REQUEST;
foreach ($param as $key => $val) {
	if (is_array($val)) {
		for ($i = 0; $i < count($val); $i++) {
			$val[$i] = $funcLibrary->cleanXssTags($val[$i]);
			${$key}[$i] = $val[$i];
		}
	} else {
		$val = $funcLibrary->cleanXssTags($val);
		${$key} = $val;
	}
}
//==========================================================


/*================== 환경변수 =====================*/
define('HOMEPAGE_URL', HTTP_PROTOCOL . '://' . $_SERVER['HTTP_HOST']);
define('ADMIN_DIR', HOMEPAGE_URL . '/admode');
define('HOME_DIR', '/');
define('HOME_DIR_EN', '/en/');

//--------- 로그인 세션 ------------------------------
$adminId = $_SESSION['adminId'] ?? null;
$adminName = $_SESSION['adminName'] ?? null;
$adminLevel = $_SESSION['adminLevel'] ?? null;
$adminSuper = $_SESSION['adminSuper'] ?? null;
$adminAuth = $_SESSION['adminAuth'] ?? null;
//$userIdx = $_SESSION['userIdx'] ?? null;
//$userId = $_SESSION['userId'] ?? null;
//$userLevel = $_SESSION['userLevel'] ?? null;
//$userProfile = $_SESSION['userProfile'] ?? null;
//$firstName = $_SESSION['firstName'] ?? null;
//$lastName = $_SESSION['lastName'] ?? null;

//--------- 로그인 세션 ------------------------------
$_SESSION['language'] ??= 'kr';


$root_path = str_replace('\\', '/', dirname(__FILE__));
$root_path = str_replace('/include/common', '', $root_path);



define('GH_COOKIE_DOMAIN',  '');
define('GH_ESCAPE_FUNCTION', 'sql_escape_string');
define('GH_SERVER_TIME',    time());
define('GH_TIME_YEAR',    date('Y', GH_SERVER_TIME));
define('GH_TIME_YMDHIS',    date('Y-m-d H:i:s', GH_SERVER_TIME));
define('GH_TIME_YMD',       substr(GH_TIME_YMDHIS, 0, 10));
define('GH_TIME_HIS',       substr(GH_TIME_YMDHIS, 11, 8));
define('_GHBOARD_', true);
define('GH_PATH', $root_path);
define('GH_URL', HOMEPAGE_URL);
define('GH_DIR_PERMISSION', 0707);
define('ADMIN_VERSION', 'V3.1'); //관리자 페이지 버전
define('LANGUAGE', $_SESSION['language']);
define('DEVICE', $funcLibrary->deviceCheck());

$gh['admin_img'] = '/images/admin';
$ghpath = '';


//허용확장자
$_config['allowExt'] = 'jpg|jpeg|gif|bmp|png|wmv|mov|avi|mpg|mpeg|asf|mp3|wma|ppt|pptx|xls|xlsx|doc|docx|hwp|alz|zip|rar|rtf|flv|pdf|mp4';
$_config['imgExt'] = 'jpg|jpeg|gif|bmp|png';

//페이징 개수
$_config['pageListEa'] = 5;
$_config['pageListEaMobile'] = 5;

//첨부파일 사이즈
define('THUMB_SIZE', 1); //썸네일 1MB
define('IMG_SIZE', 1); //이미지 1MB
define('FILE_SIZE', 30); //파일 30MB

/*================== 환경변수 끝 ====================*/

$_tel = array(
	'010' => '010',
	'011' => '011',
	'016' => '016',
	'017' => '017',
	'018' => '018',
	'019' => '019',
	'02' => '02',
	'070' => '070',
	'031' => '031',
	'032' => '032',
	'032' => '032',
	'033' => '033',
	'041' => '041',
	'042' => '042',
	'043' => '043',
	'051' => '051',
	'052' => '052',
	'053' => '053',
	'054' => '054',
	'055' => '055',
	'061' => '061',
	'062' => '062',
	'063' => '063',
	'064' => '064'
);

$_mobile = array(
	'' => '선택',
	'010' => '010',
	'011' => '011',
	'016' => '016',
	'017' => '017',
	'018' => '018',
	'019' => '019',
);

$_email = array(
	'' => '직접입력',
	'naver.com' => 'naver.com',
	'hanmail.net' => 'hanmail.net',
	'daum.net' => 'daum.net',
	'gmail.com' => 'gmail.com',
	'nate.com' => 'nate.com',
	'hotmail.com' => 'hotmail.com',
);

$_emailEn = array(
	'' => 'Direct Input',
	'naver.com' => 'naver.com',
	'hanmail.net' => 'hanmail.net',
	'nate.com' => 'nate.com',
	'gmail.com' => 'gmail.com',
);


$_week = array(
	0 => '일',
	1 => '월',
	2 => '화',
	3 => '수',
	4 => '목',
	5 => '금',
	6 => '토',
);

$_area = array(
	1 => '서울',
	2 => '경기',
	3 => '부산',
	4 => '대구',
	5 => '인천',
	6 => '광주',
	7 => '대전',
	8 => '울산',
	9 => '강원',
	10 => '충북',
	11 => '충남',
	12 => '세종',
	13 => '전북',
	14 => '전남',
	15 => '경북',
	16 => '경남',
	17 => '제주',
);

$_month_en = array(
	'01' => 'JAN',
	'02' => 'FEB',
	'03' => 'MAR',
	'04' => 'APR',
	'05' => 'MAY',
	'06' => 'JUN',
	'07' => 'JUL',
	'08' => 'AUG',
	'09' => 'SEP',
	'10' => 'OCT',
	'11' => 'NOV',
	'12' => 'DEC',
);

$_workerType = array(
	1 => '정규직',
	2 => '계약직',
	3 => '프리랜서',
);

/*================= SMTP 세팅 =============================*/
define('SMTP_HOST', '');
define('SMTP_ID', '');
define('SMTP_PASSWORD', ''); //앱용 비밀번호
define('SMTP_PORT', '587');
define('SMTP_AUTH', 'tls');
/*================= SMTP 세팅 끝 ============================*/

//============== 구글 리캡차 정보 ==============
define('GOOGLE_SITE_KEY', '');
define('GOOGLE_SECRET_KEY', '');

//============== 커리어넷 학교정보 API Key ==============
define('CAREERKEY', ''); //커리어넷 학교정보 API Key

//============== NCP MAPS API Key ==============
define('NCP_MAPS_CLIENTID', ''); //NCP MAPS 클라이언트 아이디
define('NCP_MAPS_KEY', ''); //NCP MAPS 시크릿키

//============== 전자공시 OpenDart API Key ==============
define('DART_KEY', ''); //전자공시 OpenDart API Key

//============== 구글 SMTP 정보 ==============
$googleClientId = '';
$googleClientSecret = '';
$googleRefreshToken = '';

//------------  자주쓰는 쿼리스트링 모음 및 변수 초기화 ------------
$pg ??= 1;
$pn ??= 1;
$sn ??= 1;
$keyType ??= '';
$keyword ??= '';
$startDate ??= '';
$endDate ??= '';
$ccode ??= '';
$bbsid ??= '';
$idx ??= '';
$w ??= '';
$cate ??= '';

$inputs = array();
$where = array();
$bindParam = array();
//-------------------------------------------------------

/*==================== SEO 설정 ========================*/
$metaTagData = $queryLibrary->getData(1, 'gh_seo_table');
if (strpos($_SERVER['PHP_SELF'], '/admode') === false) { //프론트 페이지에서 동작
	$ogTagData = $queryLibrary->getData(2, 'gh_seo_table');
}
/*==================== SEO 설정 ========================*/
