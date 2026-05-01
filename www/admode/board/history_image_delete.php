<?php
$ghPath = '../../';
include_once($ghPath.'include/common/common.php');
include_once($ghPath.'include/common/permission.php');

header('Content-Type: application/json; charset=utf-8');

$uploadDirectory = 'history';
$filename = isset($_POST['filename']) ? trim($_POST['filename']) : '';

// 경로 조작 방지: 파일명만 허용 (디렉터리 구분자 제거)
$filename = basename($filename);
if ($filename === '' || preg_match('/[\/\\\\]/', $filename)) {
	echo json_encode(array('success' => false, 'message' => '잘못된 요청입니다.'));
	exit;
}

$filePath = $ghPath . 'data/' . $uploadDirectory . '/' . $filename;
if (file_exists($filePath) && is_file($filePath)) {
	@unlink($filePath);
}

echo json_encode(array('success' => true));
