<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
include_once(__DIR__ . '/_common.php');
ini_set("display_errors", 0);
@include_once(__DIR__ . '/JSON.php');

if (!function_exists('json_encode')) {
    function json_encode($data)
    {
        $json = new Services_JSON();
        return ($json->encode($data));
    }
}

@ini_set('gd.jpeg_ignore_warning', 1);

// 사이트 루트 절대 경로 (popup/php 기준 7단계 상위 = www)
$ghPath = '../../../../../../../';
$realRoot = realpath(__DIR__ . '/' . $ghPath);
if ($realRoot === false) {
    $realRoot = realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT'];
}

$ym = date('ym', defined('GH_SERVER_TIME') ? GH_SERVER_TIME : time());
// Windows 경로 호환: 절대 경로 + 끝에 구분자 통일
$data_dir = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realRoot), DIRECTORY_SEPARATOR)
    . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'editor' . DIRECTORY_SEPARATOR . $ym . DIRECTORY_SEPARATOR;
$data_url = '/data/editor/' . $ym . '/';

if (!is_dir($data_dir)) {
    @mkdir($data_dir, 0755, true);
}
if (is_dir($data_dir)) {
    @chmod($data_dir, 0755);
}

if (!function_exists('ft_nonce_is_valid')) {
    include_once(__DIR__ . '/../../../editor.lib.php');
}

$is_editor_upload = false;

if (isset($_GET['_nonce']) && ft_nonce_is_valid($_GET['_nonce'], 'smarteditor')) {
    $is_editor_upload = true;
}

if ($is_editor_upload) {

    require(__DIR__ . '/UploadHandler.php');
    $options = array(
        'upload_dir' => $data_dir,
        'upload_url' => $data_url,
        // 썸네일 비활성화
        'image_versions' => array(),
        // 리사이즈 실패 시에도 업로드 성공 처리 (경로/GD 이슈 회피)
        'is_resize' => false
    );

    $upload_handler = new UploadHandler($options);
} else {
    echo json_encode(array('files' => array('0' => array('error' => '정상적인 업로드가 아닙니다.'))));
    exit;
}
