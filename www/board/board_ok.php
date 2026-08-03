<?php
$ghPath = '../';
include_once __DIR__ . '/' . $ghPath . 'include/common/common.php';
include_once __DIR__ . '/' . $ghPath . 'include/common/db.class.php';

if (!$bbsid) {
	$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
}

$bbsidSafe = $funcLibrary->escapeQuery((string) $bbsid);
if ($bbsidSafe === null) {
	$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
}
$bbsid = $bbsidSafe;
$idx = (int) ($idx ?? 0);
$w = (string) ($w ?? '');

$boardInfo = $queryLibrary->getBoardInfo($bbsid);
if (!$boardInfo) {
	$funcLibrary->alert('존재하지 않는 게시판입니다.');
}

$userId = $userId ?? null;
$isMember = ($userId !== null && $userId !== '');
$b_password = (string) ($b_password ?? '');
$b_password_confirm = (string) ($b_password_confirm ?? '');
$tableName = 'gh_board_' . $bbsid;
// POST 전용 요청(삭제 등)은 $_GET이 비어 있으므로 bbsid는 항상 명시
$extraQs = $funcLibrary->queryString('idx,w,bbsid,b_password,b_password_confirm,b_name,b_subject,b_content');
$listUrl = './board.php?bbsid=' . rawurlencode((string) $bbsid);
if ($extraQs !== '') {
	$listUrl .= '&' . rtrim($extraQs, '&');
}

$validatePasswordLength = function (string $password) use ($funcLibrary): void {
	$len = mb_strlen($password, 'UTF-8');
	if ($len < 4 || $len > 20) {
		$funcLibrary->alert('비밀번호는 4자 이상 20자 이하로 입력해 주세요.');
	}
};

// board.php 글쓰기 권한과 동일
if ($w === 'a' || $w === 'u') {
	if ($boardInfo['b_write']) {
		if (!$isMember) {
			$funcLibrary->alert(
				'로그인 하신 후 이용하실 수 있습니다.',
				'../member/login.php?r_url=' . urlencode("/board/board.php?bbsid=$bbsid")
			);
		}
	}
}

$realRoot = realpath(__DIR__ . '/' . $ghPath);
$absoluteBoardPath = $realRoot . "/data/board/$bbsid";

$DB = new DBManager($conn);

/**
 * 본인 글 수정/삭제 권한
 * - 회원글: 로그인 아이디 세션($userId)과 m_id 일치
 * - 비회원글: 열람 인증 세션(board_view_auth)이 해당 글과 일치하거나 비밀번호 확인
 */
$checkOwnerPermission = function (array $d) use ($funcLibrary, $userId, $isMember, $b_password, $adminId, $validatePasswordLength, $bbsid, $idx): void {
	if ($adminId) {
		return;
	}

	$postMemberId = trim((string) ($d['m_id'] ?? ''));
	if ($postMemberId !== '') {
		if (!$isMember || (string) $userId !== $postMemberId) {
			$funcLibrary->alert('해당 글을 수정하실 권한이 없습니다.');
		}
		return;
	}

	// 비회원글: 열람 인증 세션이 해당 글과 일치하면 허용
	if (!empty($_SESSION['board_view_auth'][$bbsid][$idx])) {
		return;
	}

	if ($b_password === '') {
		$funcLibrary->alert('비밀번호를 입력해 주세요.');
	}
	$validatePasswordLength($b_password);
	$postPassword = (string) ($d['b_password'] ?? '');
	if ($postPassword === '' || !hash_equals($postPassword, hash('sha256', $b_password))) {
		$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
	}
};

if ($w !== 'd') {
	$b_name = trim((string) ($b_name ?? ''));
	$b_subject = trim((string) ($b_subject ?? ''));
	$b_content = trim((string) ($b_content ?? ''));

	if ($b_name === '' || $b_subject === '' || $b_content === '') {
		$funcLibrary->alert('필수 항목을 입력해 주세요.');
	}

	$inputs['b_open'] = '1';
	$inputs['b_cate'] = $b_cate ?? null;
	$inputs['b_subject'] = $b_subject;
	$inputs['b_content'] = $b_content;
	$inputs['b_name'] = $b_name;
	$inputs['b_notice'] = null;

	if ($boardInfo['b_secret']) {
		$inputs['b_secret'] = '1';
	} else {
		$inputs['b_secret'] = $b_secret ?? null;
	}

	$inputs['b_data1'] = $b_data1 ?? null;
	$inputs['b_data2'] = $b_data2 ?? null;
	$inputs['b_data3'] = $b_data3 ?? null;
	$inputs['b_data4'] = $b_data4 ?? null;
	$inputs['b_data5'] = $b_data5 ?? null;
	$inputs['link_url'] = $link_url ?? null;

	//================= 파일 첨부 시작 =========================
	if ($del_file_thumb ??= null) {
		if (!empty($old_file_thumb) && file_exists($absoluteBoardPath . '/' . $old_file_thumb)) {
			@unlink($absoluteBoardPath . '/' . $old_file_thumb);
		}
		$inputs['file_thumb'] = '';
	}

	if ($_FILES['file_thumb'] ??= null) {
		$file = $_FILES['file_thumb']['tmp_name'];
		$file_size = $_FILES['file_thumb']['size'];
		if ($file && $file_size > 0) {
			if (!empty($old_file_thumb) && file_exists($absoluteBoardPath . '/' . $old_file_thumb)) {
				@unlink($absoluteBoardPath . '/' . $old_file_thumb);
			}
			$mfile = $funcLibrary->uploadFile('file_thumb', '', $absoluteBoardPath);
			$inputs['file_thumb'] = $mfile['filename'];
		}
	}

	$insert_file = '';
	$insert_file_name = '';
	if ($w !== 're' && $boardInfo['b_file']) {
		for ($i = 0; $i < count((array) ($_FILES['b_file']['name'] ?? [])); $i++) {
			$b_file_name = $_FILES['b_file']['name'][$i];
			if ($b_file_name == '') {
				if (${'del_file' . $i} ??= null) {
					$oName = $o_name[$i] ?? '';
					if ($oName !== '' && file_exists($absoluteBoardPath . '/' . $oName)) {
						@unlink($absoluteBoardPath . '/' . $oName);
					}
					$b_file_01[$i] = '';
					$b_file_01_name[$i] = '';
				} else {
					if ($w == 'u') {
						$b_file_01[$i] = $o_name[$i] ??= '';
						$b_file_01_name[$i] = $o_ori_name[$i] ??= '';
					} else {
						$b_file_01[$i] = '';
						$b_file_01_name[$i] = '';
					}
				}
			} else {
				if ($_FILES['b_file']['size'][$i] > 0) {
					$mfile = $funcLibrary->uploadMultiFiles('b_file', '', $absoluteBoardPath, $i);
					$oName = $o_name[$i] ?? '';
					if ($oName !== '' && file_exists($absoluteBoardPath . '/' . $oName)) {
						@unlink($absoluteBoardPath . '/' . $oName);
					}
					if ($mfile['filename']) {
						$b_file_01[$i] = $mfile['filename'];
						$b_file_01_name[$i] = $mfile['originalFileName'];
					}
				}
			}

			if ($b_file_01[$i] ?? '') {
				$insert_file .= $b_file_01[$i] . '|';
				$insert_file_name .= $b_file_01_name[$i] . '|';
			}
		}

		$insert_file = substr($insert_file, 0, -1);
		$insert_file_name = substr($insert_file_name, 0, -1);
		if ($insert_file == '|') {
			$insert_file = '';
			$insert_file_name = '';
		}

		$inputs['b_file'] = $insert_file;
		$inputs['b_file_name'] = $insert_file_name;
	}
	//================= 파일 첨부 끝 =========================
}

if ($w === 'a') {
	$inputs['regdate'] = date('Y-m-d H:i:s');
	$inputs['editdate'] = date('Y-m-d H:i:s');

	if ($isMember) {
		// 회원: 비밀번호 없이 m_id에 로그인 아이디 저장
		$inputs['m_id'] = $userId;
		$inputs['b_password'] = '';
	} else {
		// 비회원: 비밀번호 필수
		if ($b_password === '') {
			$funcLibrary->alert('비밀번호를 입력해 주세요.');
		}
		$validatePasswordLength($b_password);
		if ($b_password !== $b_password_confirm) {
			$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
		}
		$inputs['m_id'] = '';
		$inputs['b_password'] = hash('sha256', $b_password);
	}

	if (!$DB->insertInto($tableName, $inputs)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	}

	$idx = (int) $conn->lastInsertId();
	$sql = "update $tableName set b_parent = :b_parent, list_num = :b_parent where idx = :idx";
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':b_parent', $idx);
	$stmt->bindParam(':idx', $idx);
	$stmt->execute();

	$funcLibrary->alert('등록되었습니다.', $listUrl);
} else if ($w === 'u') {
	if ($idx <= 0) {
		$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
	}

	$d = $queryLibrary->getData($idx, $tableName);
	if (!$d) {
		$funcLibrary->alert('존재하지 않는 게시글입니다.');
	}

	// 계층형 게시판: 답변 달린 글은 수정 불가
	if (!empty($boardInfo['b_reply'])) {
		$parentIdx = (int) ($d['b_parent'] ?? $d['idx'] ?? 0);
		$depthLen = strlen((string) ($d['depth'] ?? ''));
		$replyBind = [];
		$replyWhere = "where b_parent = :b_parent and depth like :depth and length(depth) > :depth_len";
		$replyBind[] = array('b_parent', $parentIdx, 'and', '');
		$replyBind[] = array('depth', (string) ($d['depth'] ?? ''), 'and', 'like');
		$replyBind[] = array('depth_len', $depthLen, 'and', '');
		if ($queryLibrary->dataTotal($replyWhere, $replyBind, $tableName) > 0) {
			$funcLibrary->alert('답변이 등록된 글은 수정할 수 없습니다.', $listUrl);
		}
	}

	$checkOwnerPermission($d);

	$postMemberId = trim((string) ($d['m_id'] ?? ''));
	$inputs['editdate'] = date('Y-m-d H:i:s');
	unset($inputs['regdate'], $inputs['m_id'], $inputs['b_password']);

	// 비회원글: 비밀번호 입력 시 새 비밀번호로 변경 (권한은 세션/기존 비밀번호로 이미 확인)
	if ($postMemberId === '' && $b_password !== '') {
		$validatePasswordLength($b_password);
		if ($b_password !== $b_password_confirm) {
			$funcLibrary->alert('비밀번호가 일치하지 않습니다.');
		}
		$inputs['b_password'] = hash('sha256', $b_password);
	}

	$where = [];
	$where[] = array('idx', $idx, 'and');
	if (!$DB->updateSet($tableName, $inputs, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	}

	$funcLibrary->alert('수정되었습니다.', './board.php?' . $funcLibrary->queryString('w'));
} else if ($w === 'd') {
	if ($idx <= 0) {
		$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
	}

	$d = $queryLibrary->getData($idx, $tableName);
	if (!$d) {
		$funcLibrary->alert('존재하지 않는 게시글입니다.');
	}

	$checkOwnerPermission($d);

	$depth_len = strlen($d['depth'] ??= '');
	$bindParam = [];
	$where = "where b_parent = :b_parent and depth like :depth and length(depth) > :depth_len";
	$bindParam[] = array('b_parent', $d['b_parent'], 'and', '');
	$bindParam[] = array('depth', $d['depth'], 'and', 'like');
	$bindParam[] = array('depth_len', $depth_len, 'and', '');
	$total = $queryLibrary->dataTotal($where, $bindParam, $tableName);

	if ($total > 0) {
		$msg = !empty($boardInfo['b_reply'])
			? '답변이 등록된 글은 삭제할 수 없습니다.'
			: '삭제하려는 게시글에 답변이 존재합니다.\\n답변을 삭제하시고 다시 시도해 주세요.';
		$funcLibrary->alert($msg, $listUrl);
	}

	$where = [];
	$where[] = array('idx', $idx, '');

	if (!$DB->delete_db($tableName, $where)) {
		$funcLibrary->alert('문제가 발생하였습니다.');
	}

	$b_file = explode('|', $d['b_file'] ??= '');
	for ($i = 0; $i < count((array) $b_file); $i++) {
		if ($b_file[$i] !== '' && file_exists($absoluteBoardPath . '/' . $b_file[$i])) {
			@unlink($absoluteBoardPath . '/' . $b_file[$i]);
		}
	}
	if (!empty($d['file_thumb']) && file_exists($absoluteBoardPath . '/' . $d['file_thumb'])) {
		@unlink($absoluteBoardPath . '/' . $d['file_thumb']);
	}

	$funcLibrary->alert('삭제 되었습니다.', $listUrl);
} else {
	$funcLibrary->alert('잘못된 방법으로 접근하셨습니다.');
}

include_once __DIR__ . '/' . $ghPath . 'include/common/dbclose.php';
