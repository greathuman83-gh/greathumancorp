<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class FuncLibrary
{
	function __construct() {}

	/* 파일 업로드 */
	public function uploadFile(string $value = '', string $fileExtention = '', string $location = '')
	{
		global $_FILES, $_config;

		$realDocumentRoot = realpath(__DIR__ . '/../../');

		$itemFile = $_FILES[$value]['tmp_name'];
		$itemFileName = $_FILES[$value]['name'];
		$itemFileSize = $_FILES[$value]['size'];
		$itemFileType = $_FILES[$value]['type'];
		//$itemFileName = strtolower($itemFileName);


		if ($itemFileSize > 0 && $itemFile) {
			if (!is_uploaded_file($itemFile)) $this->alert("정상적인 방법으로 업로드 해주세요");

			// 확장자 체크 생략 (기존 코드 유지)
			$temp = explode('.', $itemFileName);
			$uploadCheck = strtolower(end($temp));

			// 폴더 생성 로직: DOCUMENT_ROOT 대신 실제 물리 경로 사용
			$dataDir = $realDocumentRoot . '/data';
			if (!is_dir($dataDir)) {
				@mkdir($dataDir, 0707, true); // true 추가해서 하위 폴더까지 한 번에 생성
				@chmod($dataDir, 0707);
			}

			// $location이 상대 경로(../../)로 넘어왔을 경우를 대비해 치환
			if ($location && strpos($location, '..') !== false) {
				// 상대 경로 기호를 실제 루트 경로로 변환하거나, 
				// 호출하는 곳에서부터 절대 경로를 넘겨주도록 수정해야 함
				$location = str_replace('../../', $realDocumentRoot . '/', $location);
			} else {
				$location = ($location) ? $location : $dataDir . '/';
			}

			// 최종 폴더 생성 확인
			if (!is_dir($location)) {
				@mkdir($location, 0707, true);
				@chmod($location, 0707);
			}

			$randomName = $this->getRandomString('az09', 10);
			$finishFileName = $value . '_' . time() . $randomName . '.' . $uploadCheck;

			// 파일 이동 실행
			if (!move_uploaded_file($itemFile, $location . '/' . $finishFileName)) {
				// 실패 시 실제 경로 출력해서 확인해보기
				echo "실패 경로: " . $location . '/' . $finishFileName;
				exit;
			}

			$filePath = $location . '/' . $finishFileName;
			@chmod($filePath, 0707);

			$inputdata['filename'] = $finishFileName;
			$inputdata['original_file_name'] = $itemFileName;
			$inputdata['locate'] = $filePath;
			$inputdata['file_type'] = $temp[1];
			$inputdata['file_size'] = $itemFileSize;
		}

		return $inputdata ?? '';
	}

	/* 멀티 파일 업로드 */
	public function uploadMultiFiles(string $value = '', string $fileExtention = '', string $location = '', int $count = 0)
	{
		global $_FILES, $_config;

		$realDocumentRoot = realpath(__DIR__ . '/../../');

		$itemFile = $_FILES[$value]['tmp_name'][$count];
		$itemFileName = $_FILES[$value]['name'][$count];
		$itemFileSize = $_FILES[$value]['size'][$count];
		$itemFileType = $_FILES[$value]['type'][$count];
		//$itemFileName = strtolower($itemFileName);

		if ($itemFileSize > 0 && $itemFile) {

			if (!is_uploaded_file($itemFile)) $this->alert('정상적인 방법으로 업로드 해주세요');

			if ($itemFileSize > 0) {
				//확장자 검사
				$fileExtention = ($fileExtention) ? $fileExtention : $_config['allow_ext'];
				if ($fileExtention) {
					$temp = explode('.', $itemFileName);
					$extensionPoint = count($temp) - 1;
					$uploadCheck = strtolower($temp[$extensionPoint]);
					if (strpos($fileExtention, $uploadCheck) === false) $this->alert("업로드는 $fileExtention 확장자만 가능합니다");
				}

				// 데이터 기본 폴더 생성 (절대 경로 사용 및 하위 폴더 생성 옵션 true)
				$dataDir = $realDocumentRoot . '/data';
				if (!is_dir($dataDir)) {
					@mkdir($dataDir, 0707, true);
					@chmod($dataDir, 0707);
				}

				// 저장 위치 설정 로직 (상대 경로 기호 포함 시 절대 경로로 치환)
				if ($location && strpos($location, '..') !== false) {
					$location = str_replace('../../', $realDocumentRoot . '/', $location);
				} else {
					$location = ($location) ? $location : $dataDir . '/';
				}

				// 실제 업로드 폴더 생성
				if (!is_dir($location)) {
					@mkdir($location, 0707, true);
					@chmod($location, 0707);
				}

				$randomName = $this->getRandomString('az09', 10); //랜덤 파일명 생성
				$finishFileName = $value . '_' . time() . $randomName . '.' . $uploadCheck;
				$FileCnt = 0;
				while (file_exists($location . '/' . $finishFileName)) // 화일명이 중복되지 않을때 까지 반복
				{
					$FileCnt++;
					$finishFileName = $value . '_' . time() . $randomName . '_' . $FileCnt . '.' . $uploadCheck;
				}

				if (!move_uploaded_file($itemFile, $location . '/' . $finishFileName)) {
					// 디버깅 필요 시 아래 주석 해제하여 경로 확인 가능함
					// echo $itemFile." -> ".$location.'/'.$finishFileName; 
					$this->alert("파일업로드가 제대로 되지 않았습니다");
				}
				$filePath = $location . '/' . $finishFileName;
				@chmod($filePath, 0707);
			}
			$inputdata['filename'] = $finishFileName;
			$inputdata['original_file_name'] = $itemFileName;
			$inputdata['locate'] = $filePath;
			$inputdata['file_type'] = $temp[1] ?? $uploadCheck;
			$inputdata['file_size'] = $itemFileSize;
		}
		return $inputdata;
	}


	/* 관리자 페이징 */
	public function getPaging(int $wirtePages, int $currentPage, int $totalPage, string $url, string $add = "")
	{
		//페이지 길이, 현재페이지, 총 페이지, URL,추가 파라미터
		$str = '';
		if ($currentPage > 1) {
			$str .= "<a href='" . $url . "1{$add}'>처음</a>";
			//$str .= "[<a href='" . $url . ($currentPage-1) . "'>이전</a>]";
		}

		$startPage = (((int)(($currentPage - 1) / $wirtePages)) * $wirtePages) + 1;
		$endPage = $startPage + $wirtePages - 1;

		if ($endPage >= $totalPage) $endPage = $totalPage;

		if ($startPage > 1) $str .= " &nbsp;<a href='" . $url . ($startPage - 1) . "{$add}'>이전</a>";

		if ($totalPage > 1) {
			for ($k = $startPage; $k <= $endPage; $k++) {
				if ($currentPage != $k)
					$str .= " &nbsp;<a href='$url$k{$add}'><span>$k</span></a>";
				else
					$str .= " &nbsp;<b>$k</b> ";
			}
		}

		if ($totalPage > $endPage) $str .= " &nbsp;<a href='" . $url . ($endPage + 1) . "{$add}'>다음</a>";

		if ($currentPage < $totalPage) {
			//$str .= "[<a href='$url" . ($currentPage+1) . "'>다음</a>]";
			$str .= " &nbsp;<a href='$url$totalPage{$add}'>맨끝</a>";
		}
		$str .= "";

		return $str;
	}

	/* 프론트 오피스 페이징 */
	public function getUserPaging(int $writePages, int  $currentPage, int  $totalPage, string $url = '', string $add = '')
	{
		//페이지 길이, 현재페이지, 총 페이지, URL,추가 파라미터
		$str = '';

		if ($currentPage > 1) {
			$str .= '<a href="' . $url . "1" . $add . '"  class="page_begin"></a> ';
		} else {
			$str .= ' ';
		}

		$startPage = (((int)(($currentPage - 1) / $writePages)) * $writePages) + 1;
		$endPage = $startPage + $writePages - 1;

		if ($endPage >= $totalPage) $endPage = $totalPage;

		if ($startPage > 1) {
			$str .= '<a href="' . $url . ($startPage - 1) . $add . '" class="page_prev"></a> ';
		} else {
			$str .= ' ';
		}

		if ($totalPage > 1) {
			for ($k = $startPage; $k <= $endPage; $k++) {
				if ($currentPage != $k)
					$str .= '<a href="' . $url . $k . $add . '">' . $k . '</a> ';
				else
					$str .= '<strong>' . $k . '</strong> ';
			}
		} else {
			$str .= '<strong>1</strong>';
		}

		if ($totalPage > $endPage) {
			$str .= ' <a href="' . $url . ($endPage + 1) . $add . '" class="page_next"></a>';
		} else {
			$str .= ' ';
		}

		if ($currentPage < $totalPage) {
			$str .= ' <a href="' . $url . $totalPage . $add . '" class="page_end"></a> ';
		} else {
			$str .= ' ';
		}

		$str .= '';

		return $str;
	}

	/* 배열값전송용 페이징 */
	public function getArrayPaging(int $writePages, int  $currentPage, int  $totalPage, string $url, string $add = '')
	{
		//페이지 길이, 현재페이지, 총 페이지, URL,추가 파라미터
		$str = '';

		if ($currentPage > 1) {
			$str .= '<a data-pg="1" class="arrayPaging page_begin"></a> ';
		} else {
			$str .= ' ';
		}

		$startPage = (((int)(($currentPage - 1) / $writePages)) * $writePages) + 1;
		$endPage = $startPage + $writePages - 1;

		if ($endPage >= $totalPage) $endPage = $totalPage;

		if ($startPage > 1) {
			$str .= '<a data-pg="' . ($startPage - 1) . '" class="arrayPaging page_prev"></a> ';
		} else {
			$str .= ' ';
		}

		if ($totalPage > 1) {
			for ($k = $startPage; $k <= $endPage; $k++) {
				if ($currentPage != $k)
					$str .= '<a class="arrayPaging" data-pg="' . $k . '">' . $k . '</a> ';
				else
					$str .= '<strong>' . $k . '</strong> ';
			}
		} else {
			$str .= '<strong>1</strong>';
		}

		if ($totalPage > $endPage) {
			$str .= ' <a data-pg="' . ($endPage + 1) . '" class="arrayPaging page_next"></a>';
		} else {
			$str .= ' ';
		}

		if ($currentPage < $totalPage) {
			$str .= ' <a data-pg="' . $totalPage . '" class="arrayPaging page_end"></a> ';
		} else {
			$str .= ' ';
		}

		$str .= '';
		return $str;
	}


	/* 랜덤문자생성 */
	public function getRandomString(string $type = '', int $len = 10)
	{
		$lowercase = 'abcdefghijklmnopqrstuvwxyz';
		$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numeric = '0123456789';
		$special = '!@#-';
		$key = '';
		$token = '';
		if ($type == '') {
			$key = $lowercase . $uppercase . $numeric;
		} else {
			if (strpos($type, '09') > -1) $key .= $numeric; //숫자
			if (strpos($type, 'az') > -1) $key .= $lowercase; //소문자
			if (strpos($type, 'AZ') > -1) $key .= $uppercase; //대문자
			if (strpos($type, '$') > -1) $key .= $special; //특수문자
		}

		for ($i = 0; $i < $len; $i++) {
			$token .= $key[mt_rand(0, strlen($key) - 1)];
		}
		return $token;
	}

	/* 쿼리스트링 가져오기 */
	public function queryString(string $exception = '')
	{
		$param = $_GET;
		$qstring = '';
		if ($exception) $exception = explode(',', $exception);

		foreach ($param as $key => $val) {
			if ($exception) {
				if (in_array($key, $exception)) { //쿼리스트링에서 특정 값 제거
					continue;
				}
			}
			if (is_array($val)) {
				continue;
			}
			$val = $this->cleanXssTags((string) $val);
			$qstring .= rawurlencode((string) $key) . '=' . rawurlencode($val) . '&';
		}

		return $qstring;
	}

	/* 경고창 및 리다이렉트 */
	public function gotoUrl(string $url)
	{
		echo "<script type='text/javascript'> location.replace('$url'); </script>";
		exit;
	}

	public function alert(string $msg = '', string $url = '', string $close = '')
	{
		if (!$msg) $msg = 'no message.';

		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
		echo "<script type='text/javascript'>alert('$msg');";
		if ($close)
			echo 'self.close();';
		if (!$url)
			echo 'history.go(-1);';
		echo '</script>';
		if ($url)
			$this->gotoUrl($url);
		exit;
	}


	/*================= XSS(문자열 정리) · SQL 식별자 화이트리스트 ========================*/

	/** 테이블/컬럼 등 식별자만 허용 (영문·숫자·언더스코어) */
	public function escapeQuery(?string $str): ?string
	{
		if ($str === null || $str === '') {
			return null;
		}
		$s = trim(rawurldecode($str));
		return preg_match('/^[a-zA-Z0-9_]+$/', $s) ? $s : null;
	}

	/**
	 * ORDER BY 한 조각 (예: idx DESC, rand()). 파이프(|)로 여러 개 넘기기 전 각각 검증에 사용.
	 */
	public function sanitizeOrderBySegment(string $segment): string
	{
		$p = trim($segment);
		if ($p === '') {
			return '';
		}
		if (strcasecmp($p, 'rand()') === 0) {
			return 'rand()';
		}
		if (!preg_match('/^([a-zA-Z_][a-zA-Z0-9_]*(?:\*[0-9]+)?)(\s+(ASC|DESC))?$/i', $p, $m)) {
			return '';
		}
		$dir = isset($m[3]) ? ' ' . strtoupper($m[3]) : '';

		return $m[1] . $dir;
	}

	/** 예: idx desc|num asc */
	public function sanitizeOrderByPipeList(string $orderby, string $default = 'idx desc'): string
	{
		$orderby = trim($orderby);
		if ($orderby === '') {
			return $default;
		}
		$parts = explode('|', $orderby);
		$out = [];
		foreach ($parts as $part) {
			$s = $this->sanitizeOrderBySegment($part);
			if ($s !== '') {
				$out[] = $s;
			}
		}

		return $out !== [] ? implode('|', $out) : $default;
	}

	/**
	 * SELECT 컬럼 목록(프로젝트에서 쓰는 패턴 위주). 알 수 없는 표현은 * 로 폴백.
	 */
	public function sanitizeSelectColumnExpr(string $column): string
	{
		$c = trim($column);
		if ($c === '' || $c === '*') {
			return '*';
		}
		if (preg_match('/^count\(\*\)\s+as\s+[a-zA-Z_][a-zA-Z0-9_]*$/i', $c)) {
			return $c;
		}
		if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $c)) {
			return $c;
		}
		$parts = preg_split('/\s*,\s*/', $c);
		$clean = [];
		foreach ($parts as $p) {
			$p = trim($p);
			if ($p === '' || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $p)) {
				return '*';
			}
			$clean[] = $p;
		}

		return implode(', ', $clean);
	}

	/**
	 * 검색어가 있을 때 escapeQuery를 통과한 컬럼명에만 LIKE 조건을 붙인다.
	 */
	public function appendWhereLike(string &$where, array &$bind_param, ?string $key_type, ?string $keyword, string $default_column): void
	{
		if ($keyword === null || $keyword === '') {
			return;
		}
		$col = $this->escapeQuery(($key_type !== null && $key_type !== '') ? $key_type : $default_column);
		if ($col === null || $col === '') {
			return;
		}
		$where .= " and {$col} like :keyword";
		$bind_param[] = ['keyword', $keyword, 'like'];
	}

	/**
	 * 위험한 HTML 태그 제거 후 HTML 속성/본문에 넣기 전 인코딩. DB 저장값은 가능하면 출력 시점에 한 번 더 컨텍스트에 맞게 인코딩할 것.
	 */
	public function cleanXssTags(string $str): string
	{
		$str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

		return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	/** @deprecated 블랙리스트 기반 필터는 우회·오탐이 많아 제거됨. 호환용으로 입력을 그대로 반환한다. */
	public function sqlInjection(string $str): string
	{
		return $str;
	}

	public function decodeTags(string $str)
	{ //entity 기호로 변환
		$str = str_replace('&lt;', '<', $str);
		$str = str_replace('&gt;', '>', $str);
		$str = str_replace('&#39;', "'", $str);
		$str = str_replace('&quot;', '"', $str);
		$str = str_replace('&amp;', '&', $str);
		return $str;
	}
	/*================= XSS · SQL 식별자 처리 끝 =======================*/


	/* 문자열 자르기 */
	public function cutString(string $str = '', int $len = 0, string $suffix = '..')
	{
		// 한글 한글자(2byte, 유니코드 3byte)는 길이 2, 공란.영숫자.특수문자는 길이 1
		$str = strip_tags($this->decodeTags($str));
		$s = mb_substr($str, 0, $len);
		$cnt = 0;
		for ($i = 0; $i < mb_strlen($s); $i++)
			if (ord($s[$i]) > 127)
				$cnt++;
		$s = mb_substr($s, 0, $len - ($cnt % 2));
		if (mb_strlen($s) >= mb_strlen($str))
			$suffix = "";
		return $s . $suffix;
	}

	/**
	 * 사용자 단에서 a 태그 href로 쓸 URL만 허용 (javascript:/data: 등 차단).
	 */
	public function safeHrefForUserLink(?string $url): ?string
	{
		if ($url === null || ($url = trim($url)) === '') {
			return null;
		}
		if (preg_match('/^\s*(javascript|vbscript|data)\s*:/iu', $url)) {
			return null;
		}
		if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $url)) {
			return null;
		}
		if (preg_match('#^(https?:)?//#iu', $url) || preg_match('#^mailto:#iu', $url)) {
			return htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}
		if ($url !== '' && ($url[0] === '/' || $url[0] === '?' || $url[0] === '#')) {
			return htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}
		return null;
	}

	/** 첨부 저장 파일명 등 (경로 제거, basename만) */
	public function safeBoardUploadBasename(?string $name): string
	{
		if ($name === null || $name === '') {
			return '';
		}
		$n = basename(str_replace("\0", '', (string) $name));

		return str_replace(['/', '\\'], '', $n);
	}

	/**
	 * 사용자 단 게시 본문(에디터 HTML): 자바스크립트와 iframe만 제거한다.
	 *
	 * 스타일 등 나머지 마크업은 그대로 유지한다. strip_tags 방식은 에디터가 만든
	 * 깨진 속성(예: style 값 안의 따옴표로 따옴표 짝이 어긋난 태그)에서 태그 경계를
	 * 잘못 잡아 본문이 통째로 삭제될 수 있으므로, DOMDocument로 관대하게 파싱한 뒤
	 * script/iframe 태그와 이벤트 핸들러(on*)·javascript: 스킴만 걸러 재직렬화한다.
	 */
	public function sanitizeBoardHtmlForDisplay(?string $html): string
	{
		if ($html === null || $html === '') {
			return '';
		}

		// 통째로 제거할 태그 (자바스크립트 / iframe)
		$blockedTags = ['script', 'iframe'];

		$prevState = libxml_use_internal_errors(true);
		$doc = new DOMDocument('1.0', 'UTF-8');
		// UTF-8 보존 + html/body 자동 삽입 방지를 위해 루트 div로 감싼다.
		$wrapped = '<?xml encoding="UTF-8"><div id="gh-sanitize-root">' . $html . '</div>';
		$loaded = $doc->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();
		libxml_use_internal_errors($prevState);

		// 파싱 실패 시 원본을 그대로 반환한다.
		if (!$loaded) {
			return $html;
		}

		$xpath = new DOMXPath($doc);
		$root = $xpath->query('//*[@id="gh-sanitize-root"]')->item(0);
		if ($root === null) {
			return '';
		}

		// 노드 제거 중 안전하도록 요소 목록을 먼저 배열로 고정한다.
		$elements = iterator_to_array($doc->getElementsByTagName('*'));
		foreach ($elements as $el) {
			if ($el === $root) {
				continue;
			}
			$tag = strtolower($el->nodeName);
			// script / iframe 은 내용까지 통째로 제거한다.
			if (in_array($tag, $blockedTags, true)) {
				if ($el->parentNode !== null) {
					$el->parentNode->removeChild($el);
				}
				continue;
			}
			if ($el->hasAttributes()) {
				// 라이브 NamedNodeMap이므로 역순으로 제거한다.
				for ($i = $el->attributes->length - 1; $i >= 0; $i--) {
					/** @var DOMAttr $attr */
					$attr = $el->attributes->item($i);
					$name = strtolower($attr->name);
					// 이벤트 핸들러(on*) 속성은 자바스크립트이므로 제거한다.
					if (strncmp($name, 'on', 2) === 0) {
						$el->removeAttribute($attr->name);
						continue;
					}
					// href/src 의 javascript:/vbscript: 스킴은 무력화한다.
					if (($name === 'href' || $name === 'src') &&
						preg_match('/^\s*(javascript|vbscript)\s*:/i', trim($attr->value))
					) {
						$el->setAttribute($attr->name, '#');
					}
				}
			}
		}

		// 루트 래퍼 내부만 직렬화한다.
		$out = '';
		foreach ($root->childNodes as $child) {
			$out .= $doc->saveHTML($child);
		}

		return trim($out);
	}



	/* 디바이스 체크 */
	public function deviceCheck()
	{
		if (preg_match('/iPhone|iPod|iPad|Android|android|dream|Windows Phone|mobile|Opera Mini|Windows CE|blackberry|webOS|incognito|webmate|nokia|bada|SKT|LGTelecom/', $_SERVER['HTTP_USER_AGENT'])) {
			return 'mobile';
		} else {
			return 'pc';
		}
	}

	/* 관리자 페이지 접근 권한 체크 */
	public function adminAuthor(string $menuCode, ?string $super, string $adminAuth = '')
	{
		if (($super ??= '') != '1') { //슈퍼관리자가 아닐 경우 페이지 접근권한 체크
			$adminAuth = explode('|', $adminAuth);
			if (!in_array($menuCode, $adminAuth)) {
				$this->alert('해당 메뉴에 접근하실 권한이 없습니다.', '../main.php');
			}
		}
	}

	//phpmailer smtp 메일 발송
	public function smtpSend(
		string $smtp = "",
		string $id = "",
		string $pw = "",
		int $port = 465,
		$smtpSecure = "",
		string $body = "",
		string $subject = "",
		string $send_email = "",
		string $send_name = "",
		string $receive_email = "",
		string $receive_name = "",
		$path = "",
		$file_name = "",
		string $cc = "",
		int $error = 0
	) {
		require_once __DIR__ . '/../plugin/vendor/autoload.php';
		$mail = new PHPMailer(true);

		try {
			$mail->CharSet = 'utf-8';
			$mail->Encoding = 'base64';
			$mail->isHTML(true);

			if ($smtp) {
				$mail->isSMTP();
				$mail->Host = $smtp;
				$mail->Port = $port;
				$mail->SMTPAuth = true;
				$mail->Username = $id;
				$mail->Password = $pw;

				if ($smtpSecure === 'ssl') {
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
				} elseif ($smtpSecure === 'tls') {
					$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				}

				if ($error > 0) {
					$mail->SMTPDebug = $error; // 1 or 2
				}
			}

			$mail->setFrom($send_email, $send_name);
			$mail->addReplyTo($send_email, $send_name);
			$mail->Subject = $subject;
			$mail->Body = $body;

			// 참조(Cc)
			foreach (explode(',', $cc) as $ccAddr) {
				$ccAddr = trim($ccAddr);
				if (!empty($ccAddr)) {
					$mail->addCC($ccAddr);
				}
			}

			// 첨부파일
			if (!empty($path)) {
				$file_path_array = explode('|', $path);
				$file_name_array = explode('|', $file_name);
				for ($i = 0; $i < count($file_path_array); $i++) {
					$filePath = trim($file_path_array[$i]);
					$fileName = $file_name_array[$i] ?? basename($filePath);
					if (!empty($filePath)) {
						$mail->addAttachment($filePath, $fileName);
					}
				}
			}

			// 수신자 처리
			$receive_email_array = explode(',', $receive_email);
			$receive_name_array = explode(',', $receive_name);
			foreach ($receive_email_array as $key => $address) {
				$mail->clearAddresses(); // 새로 보낼 때 초기화
				$mail->addAddress(trim($address), $receive_name_array[$key]);

				if (!$mail->send()) {
					echo 'Mailer Error: ' . $mail->ErrorInfo;
					exit;
				}
			}
		} catch (Exception $e) {
			echo 'Mailer Exception: ' . $mail->ErrorInfo;
			exit;
		}
	}

	/* 구글 리캡챠 */
	public function googleRecaptcha(string $secretCode = '', string $key = '')
	{

		$gg_response = $secretCode;

		// 구글 리캡차 시작
		$ch = curl_init();
		$secretKey = $key;

		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt(
			$ch,
			CURLOPT_POSTFIELDS,
			"secret=$secretKey&response=$gg_response&remoteip=" . $_SERVER['REMOTE_ADDR']
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);
		$server_output = json_decode($server_output);

		curl_close($ch);
		// 구글 리캡차 끝

		if ($server_output->success == false) {
			$this->alert('자동등록방지를 체크해 주세요.');
		}
	}

	/* 인코딩 */
	public function toRFC2231(string $str)
	{
		$bytes = mb_convert_encoding($str, 'UTF-8');
		$sb = "utf-8''";
		for ($i = 0; $i < strlen($bytes); $i++) {
			$b = ord($bytes[$i]);

			if ($b >= 0x20 && $b <= 0x7E) {
				if ($b == 0x22 || $b == 0x25 || $b == 0x5C) {
					$sb .= '\\';
				}
				$sb .= $bytes[$i];
			} else {
				$sb .= '%' . strtoupper(dechex($b));
			}
		}
		return $sb;
	}

	/* UTC 시간대 변경 */
	public function utcToTime(string $utcDate, string $timezone = 'KST')
	{
		//$datetime = new DateTime($utcDate);
		//$datetime->setTimezone(new DateTimeZone($timezone));
		//$changeDate = $datetime->format('Y-m-d H:i:s');
		$changeDate = date('Y-m-d H:i:s', strtotime($utcDate . "+9 hours"));
		return $changeDate;
	}


	public function getAge(string $birthday)
	{ //나이 구하기
		$nowYear = date('Y');
		$nowMonth = date('m');
		$nowDay = date('d');

		$birthdayArray = explode('-', $birthday);

		$year = sprintf('%04d', $birthdayArray[0]);
		$month = sprintf('%02d', $birthdayArray[1]);
		$day = sprintf('%02d', $birthdayArray[2]);

		//(월, 일, 년)
		if (checkdate($month, $day, $year) !== true) {
			$age = '';
		} else {
			//만 나이 계산. (올해 생일이 아직 오지 않았다면 -1)
			$age = (($nowMonth . '-' . $nowDay) < ($month . '-' . $day)) ? $nowYear - $year - 1 : $nowYear - $year;
		}
		return $age;
	}

	/* 엑심베이 fgkey 생성  */
	public function fgkeyGenerate(string $jsonData, string $orderId = '', int $price = 1, string $buyer = '', $email = '', string $returnUrl = '', string $statusUrl = '', $currency = 'USD', $language = 'EN')
	{
		$url = EXIMBAY_URL . '/v1/payments/ready';
		$data = json_decode($jsonData, true);

		// 에러 핸들링
		if (json_last_error() !== JSON_ERROR_NONE) {
			die('Invalid JSON data: ' . json_last_error_msg());
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic ' . base64_encode(EXIMBAY_KEY) . ''));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		$response  = curl_exec($ch);



		$result = json_decode($response);
		curl_close($ch);

		if ($result->fgkey ?? '') {
			return $result->fgkey;
		} else {
			return false;
		}
	}

	/* 엑심베이 fgkey 생성  */
	public function fgkeyGenerateKr(string $jsonData, string $orderId = '', int $price = 1, string $buyer = '', $email = '', string $returnUrl = '', string $statusUrl = '', $currency = 'USD', $language = 'EN')
	{
		$url = EXIMBAY_URL_KR . '/v1/payments/ready';
		$data = json_decode($jsonData, true);

		// 에러 핸들링
		if (json_last_error() !== JSON_ERROR_NONE) {
			die('Invalid JSON data: ' . json_last_error_msg());
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic ' . base64_encode(EXIMBAY_KEY_KR) . ''));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		$response  = curl_exec($ch);



		$result = json_decode($response);
		curl_close($ch);

		if ($result->fgkey ?? '') {
			return $result->fgkey;
		} else {
			return false;
		}
	}


	/* 전자공시 OpenDart API - 공시 리스트 */
	public function dartNoticeList(int $pg = 1, string $language = 'kr')
	{
		if (!is_numeric($pg)) {
			$pg = 1;
		}
		$year = date('Y') - 3;
		$params = array(
			'lang' => 'en',
			'crtfc_key' => DART_KEY,
			'corp_code' => '01203659',
			'bgn_de' => $year . '0101', //시작일
			'page_no' => $pg,
			'page_count' => 10,
		);

		if ($language == 'kr') {
			$url = 'https://opendart.fss.or.kr/api/list.json' . '?' . http_build_query($params, '', '&');
		} else {
			$url = 'https://engopendart.fss.or.kr/engapi/list.json' . '?' . http_build_query($params, '', '&');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"); // UA 위장
		// 실행
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Curl error: ' . curl_error($ch);
			return $arrayData['list'] = array();
		} else {
			$arrayData = json_decode($response, true);
			return $arrayData;
			/*
			echo "<pre>";
			print_r($arrayData); // 배열로 출력
			echo "</pre>";
			*/
		}
		curl_close($ch);
	}

	/* 전자공시 OpenDart API - 단일회사 전체 재무제표 */
	public function dartFinanceAll(int $year = 0)
	{
		if ($year == 0) {
			$year = date('Y') - 1;
		}
		$url = 'https://opendart.fss.or.kr/api/fnlttSinglAcntAll.json?crtfc_key=' . DART_KEY . '&corp_code=01344831&bsns_year=' . (int)$year . '&reprt_code=11011&fs_div=OFS';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"); // UA 위장

		// 실행
		$response = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Curl error: ' . curl_error($ch);
		} else {
			$arrayData = json_decode($response, true);
			return $arrayData;
			/*
			echo "<pre>";
			print_r($arrayData); // 배열로 출력
			echo "</pre>";
			*/
		}
		curl_close($ch);
	}
}
