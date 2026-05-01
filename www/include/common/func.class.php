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
		$itemFile = $_FILES[$value]['tmp_name'];
		$itemFileName = $_FILES[$value]['name'];
		$itemFileSize = $_FILES[$value]['size'];
		$itemFileType = $_FILES[$value]['type'];
		//$itemFileName = strtolower($itemFileName);

		if ($itemFileSize > 0 && $itemFile) {

			if (!is_uploaded_file($itemFile)) $this->alert("정상적인 방법으로 업로드 해주세요");

			if ($itemFileSize > 0) {
				//확장자 검사
				$fileExtention = ($fileExtention) ? $fileExtention : $_config['allowExt'];
				if ($fileExtention) {
					$temp = explode('.', $itemFileName);
					$extensionPoint = count($temp) - 1;
					$uploadCheck = strtolower($temp[$extensionPoint]);
					if (strpos($fileExtention, $uploadCheck) === false) $this->alert("업로드는 $fileExtention 확장자만 가능합니다");
				}
				@mkdir($_SERVER['DOCUMENT_ROOT'] . '/data/', 0707);
				@chmod($_SERVER['DOCUMENT_ROOT'] . '/data/', 0707);
				$location = ($location) ? $location : $_SERVER['DOCUMENT_ROOT'] . '/data/';
				@mkdir($location, 0707);
				@chmod($location, 0707);

				$randomName = $this->getRandomString('az09', 10); //랜덤 파일명 생성
				$finishFileName = $value . '_' . time() . $randomName . '.' . $uploadCheck;
				$FileCnt = 0;
				while (file_exists($location . '/' . $finishFileName)) // 화일명이 중복되지 않을때 까지 반복
				{
					$FileCnt++;
					$finishFileName = $value . '_' . time() . $randomName . '_' . $FileCnt . '.' . $uploadCheck;
				}

				if (!move_uploaded_file($itemFile, $location . '/' . $finishFileName)) {
					//echo $itemFile." -> ".$location.'/'.$finishFileName;
					$this->alert('파일업로드가 제대로 되지 않았습니다');
				}
				$filePath = $location . '/' . $finishFileName;
				@chmod($filePath, 0707);
			}
			$inputdata['filename'] = $finishFileName;
			$inputdata['originalFileName'] = $itemFileName;
			$inputdata['locate'] = $filePath;
			$inputdata['fileType'] = $temp[1];
			$inputdata['fileSize'] = $itemFileSize;
		}
		return $inputdata;
	}

	/* 멀티 파일 업로드 */
	function uploadMultiFiles(string $value = '', string $fileExtention = '', string $location = '', int $count = 0)
	{
		global $_FILES, $_config;
		$itemFile = $_FILES[$value]['tmp_name'][$count];
		$itemFileName = $_FILES[$value]['name'][$count];
		$itemFileSize = $_FILES[$value]['size'][$count];
		$itemFileType = $_FILES[$value]['type'][$count];
		//$itemFileName = strtolower($itemFileName);

		if ($itemFileSize > 0 && $itemFile) {

			if (!is_uploaded_file($itemFile)) $this->alert('정상적인 방법으로 업로드 해주세요');

			if ($itemFileSize > 0) {
				//확장자 검사
				$fileExtention = ($fileExtention) ? $fileExtention : $_config['allowExt'];
				if ($fileExtention) {
					$temp = explode('.', $itemFileName);
					$extensionPoint = count($temp) - 1;
					$uploadCheck = strtolower($temp[$extensionPoint]);
					if (strpos($fileExtention, $uploadCheck) === false) $this->alert("업로드는 $fileExtention 확장자만 가능합니다");
				}
				@mkdir($_SERVER['DOCUMENT_ROOT'] . '/data/', 0707);
				@chmod($_SERVER['DOCUMENT_ROOT'] . '/data/', 0707);
				$location = ($location) ? $location : $_SERVER['DOCUMENT_ROOT'] . '/data/';
				@mkdir($location, 0707);
				@chmod($location, 0707);

				$randomName = $this->getRandomString('az09', 10); //랜덤 파일명 생성
				$finishFileName = $value . '_' . time() . $randomName . '.' . $uploadCheck;
				$FileCnt = 0;
				while (file_exists($location . '/' . $finishFileName)) // 화일명이 중복되지 않을때 까지 반복
				{
					$FileCnt++;
					$finishFileName = $value . '_' . time() . $randomName . '_' . $FileCnt . '.' . $uploadCheck;
				}

				if (!move_uploaded_file($itemFile, $location . '/' . $finishFileName)) {
					//echo $itemFile." -> ".$location.'/'.$finishFileName;
					$this->alert("파일업로드가 제대로 되지 않았습니다");
				}
				$filePath = $location . '/' . $finishFileName;
				@chmod($filePath, 0707);
			}
			$inputdata['filename'] = $finishFileName;
			$inputdata['originalFileName'] = $itemFileName;
			$inputdata['locate'] = $filePath;
			$inputdata['fileType'] = $temp[1];
			$inputdata['fileSize'] = $itemFileSize;
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
	public function getUserPaging(int $writePages, int  $currentPage, int  $totalPage, string $url, string $add = '')
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
			$str .= '<a href="' . $url . ($startPage - 1) . $add . '" class="prev arrow"></a> ';
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
			$str .= ' <a href="' . $url . ($endPage + 1) . $add . '" class="next arrow"></a>';
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

	public function getUserPaging2(int $writePages, int  $currentPage, int  $totalPage, string $url = '', string $add = '')
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
			$val = $this->cleanXssTags($val);
			$qstring .= "$key=$val&";
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


	/*================= XSS 및 sql injection 관련 태그 제거 ========================*/
	public function cleanXssTags(string $str)
	{
		$str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', rawurldecode($str));
		$str = htmlentities($str, ENT_QUOTES, 'UTF-8');
		$str = $this->sqlInjection($str);
		return $str;
	}

	public function sqlInjection(string $str)
	{
		return preg_replace("/( select| union| declare| insert| update| delete| drop|)/i", "", rawurldecode($str));
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

	/* 쿼리 escape */
	public function escapeQuery(string $str)
	{
		if (!$str) {
			return null;
		}
		$escapeQuery = str_replace('>', '', rawurldecode($str));
		$escapeQuery = str_replace('<', '', $escapeQuery);
		$escapeQuery = str_replace(',', '', $escapeQuery);
		$escapeQuery = str_replace('=', '', $escapeQuery);
		$escapeQuery = str_replace("'", '', $escapeQuery);
		$escapeQuery = str_replace('"', '', $escapeQuery);
		$escapeQuery = str_replace(' ', '', $escapeQuery);
		$escapeQuery = str_replace(';', '', trim($escapeQuery));
		$escapeQuery = htmlentities($escapeQuery, ENT_QUOTES, 'UTF-8');
		$escapeQuery = $this->sqlInjection($escapeQuery);
		return $escapeQuery;
	}
	/*================= XSS 및 sql injection 관련 태그 제거 끝 =======================*/


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
	public function adminAuthor(string $menuCode, $super, $adminAuth = '')
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


	public function getAge($birthday)
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
