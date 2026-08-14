<?php
/*================== 환경변수 =====================*/
define('HOMEPAGE_URL', HTTP_PROTOCOL . '://' . $_SERVER['HTTP_HOST']);
define('ADMIN_DIR', HOMEPAGE_URL . '/admode');
define('HOME_DIR', '/'); //홈
define('HOME_DIR_EN', '/en/'); //영문
define('HOME_DIR_CN', '/cn/'); //중문
define('HOME_DIR_JP', '/jp/'); //일문

//--------- 로그인 세션 ------------------------------
$admin_id = $_SESSION['admin_id'] ?? null;
$admin_name = $_SESSION['admin_name'] ?? null;
$admin_level = $_SESSION['admin_level'] ?? null;
$admin_super = $_SESSION['admin_super'] ?? null;
$admin_auth = $_SESSION['admin_auth'] ?? null;
//$user_idx = $_SESSION['userIdx'] ?? null;
//$user_id = $_SESSION['userId'] ?? null;
//$user_level = $_SESSION['userLevel'] ?? null;
//$user_profile = $_SESSION['userProfile'] ?? null;
//$first_name = $_SESSION['firstName'] ?? null;
//$last_name = $_SESSION['lastName'] ?? null;

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
define('ADMIN_VERSION', 'V3.1.4'); //관리자 페이지 버전
define('LANGUAGE', $_SESSION['language']);
define('DEVICE', $func_library->deviceCheck());

$gh['admin_img'] = '/images/admin';
// 호출 페이지가 상대 경로를 이미 넣은 경우 유지 (업로드 경로용)
$gh_path ??= '';


//허용확장자
$_config['allow_ext'] = 'jpg|jpeg|gif|bmp|png|wmv|mov|avi|mpg|mpeg|asf|mp3|wma|ppt|pptx|xls|xlsx|doc|docx|hwp|alz|zip|rar|rtf|flv|pdf|mp4';
$_config['img_ext'] = 'jpg|jpeg|gif|bmp|png';

//페이징 개수
$_config['page_list_ea'] = 5;
$_config['page_list_ea_mobile'] = 5;

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

$_publicationsCategory = array( //Publications 게시판 카테고리
	'001' => 'Publications',
	'002' => 'Conference Abstracts',
	'003' => 'Research Activities',
);

$_inquiryCategory = array( //문의분야 제품
	'001' => 'M4CXR',
	'002' => 'DEEP:NEURO',
	'003' => 'DEEP:CHEST',
	'004' => 'DEEP:LUNG',
	'005' => 'SkyMARU:SECURITY',
	'006' => 'DEEP:SECURITY',
	'007' => 'DEEP:FACTORY',
);

$_inquiryEtc = array( //문의분야 기타
	'001' => '파트너십 문의',
	'002' => 'IR 문의',
	'003' => '기타 문의',
);

$_inquiryReferer = array( //유입경로
	'001' => '포털검색',
	'002' => '언론보도',
	'003' => '전시회',
	'004' => '지인소개',
	'005' => '블로그',
	'006' => 'SNS(페이스북, 링크드인, 인스타그램 등)',
	'007' => 'Youtube',
	'008' => 'DM(뉴스레터, 초대메일 등)',
	'009' => '기타',
);

$_reportType = array( //사이버신문고 제보유형
	'001' => '제보하기',
	'002' => '칭찬하기',
	'003' => 'CP문의 및 위반 사례 신고',
);

$_reportReply = array( //사이버신문고 답변회신
	'001' => '이메일',
	'002' => '휴대폰',
	'003' => '필요없음',
);

$_publicationCategory = array( //Publication 구분
	'001' => '논문',
	'002' => '학회',
);

$_publicationCategoryEn = array( //Publication 구분(영문)
	'001' => 'Journal',
	'002' => 'Conference',
);

// 직원 고용형태 — worker_list/form w_type 매핑
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

//============== 엑심베이 API ===========================
define('EXIMBAY_URL', ''); //엑심베이 url
define('EXIMBAY_URL_KR', ''); //엑심베이 url 국문
define('EXIMBAY_KEY', ''); //엑심베이 KEY
define('EXIMBAY_KEY_KR', ''); //엑심베이 KEY 국문

//============== 전자공시 OpenDart API ===========================
define('DART_KEY', ''); //API KEY

//============== 카카오 API ===========================
define('KAKAO_API_ADMIN_KEY', 'f0ff7905d2c7d8fffa95410012303c7e'); //API ADMINKEY
define('KAKAO_API_JS_KEY', '3d1a84bbf6cfbd19b81cb8017d5e0a2d'); //API JavaScript KEY

//============== 구글 SMTP 정보 ==============
$google_client_id = '';
$google_client_secret = '';
$google_refresh_token = '';

//------------  자주쓰는 쿼리스트링 모음 및 변수 초기화 ------------
$pg ??= 1;
$pn ??= 1;
$sn ??= 1;
$key_type ??= '';
$keyword ??= '';
$start_date ??= '';
$end_date ??= '';
$ccode ??= '';
$bbsid ??= '';
$idx ??= '';
$w ??= '';
$cate ??= '';

$inputs = array();
$where = array();
$bind_param = array();
//-------------------------------------------------------

/*==================== SEO 설정 ========================*/
$meta_tag_data = $query_library->getData(1, 'gh_seo_table');
if (strpos($_SERVER['PHP_SELF'], '/admode') === false) { //프론트 페이지에서 동작
	$og_tag_data = $query_library->getData(2, 'gh_seo_table');
}
/*==================== SEO 설정 ========================*/
