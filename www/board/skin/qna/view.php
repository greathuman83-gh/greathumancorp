<?php
$maskWriterName = static function (string $name): string {
	$name = trim($name);
	$len = mb_strlen($name, 'UTF-8');
	if ($len <= 0) {
		return '';
	}
	if ($len === 1) {
		return '*';
	}
	if ($len === 2) {
		return mb_substr($name, 0, 1, 'UTF-8') . '*';
	}
	return mb_substr($name, 0, 1, 'UTF-8')
		. str_repeat('*', $len - 2)
		. mb_substr($name, -1, 1, 'UTF-8');
};

$hasReply = !empty($hasReply) && !empty($replyData);
$maskedName = $maskWriterName((string) ($d['b_name'] ?? ''));
$listHref = './board.php?' . $funcLibrary->queryString('idx,w,b_name,b_password');
$editHref = './board.php?' . $funcLibrary->queryString('w,b_name,b_password') . 'w=u&idx=' . (int) ($d['idx'] ?? 0);
$isPlainContent = (strip_tags((string) ($d['b_content'] ?? '')) === (string) ($d['b_content'] ?? ''));
$viewIdx = (int) ($d['idx'] ?? 0);
$postMemberId = trim((string) ($d['m_id'] ?? ''));
$hasOwnerAuth = !empty($adminId)
	|| ($postMemberId !== '' && !empty($userId) && (string) $userId === $postMemberId)
	|| !empty($_SESSION['board_view_auth'][$bbsid][$viewIdx]);
// 비회원글이면서 해당 글 인증 세션이 없으면 삭제 시 비밀번호 필요
$needDeletePassword = ($postMemberId === '' && !$hasOwnerAuth);
?>
<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs_view contact_view">

	<div class="section section1">
		<div class="innerWrap">
			<div class="titWrap">
				<?php if ($hasReply) { ?>
					<div class="status end"><img src="/images/page/contact_chk_icon.png" alt=""> 답변완료</div>
				<?php } else { ?>
					<div class="status ing">답변접수</div>
				<?php } ?>
				<div class="tit"><?= gh_h((string) ($d['b_subject'] ?? '')) ?></div>
				<div class="infoWrap">
					<div class="left">
						<div class="con">
							등록일 <span class="bar"></span> <?= gh_h((string) $regdate2) ?>
						</div>
						<div class="con">
							작성자 <span class="bar"></span> <?= gh_h($maskedName) ?>
						</div>
					</div>
				</div>
			</div>

			<div class="conWrap">
				<div class="con">
					<?php if ($isPlainContent) { ?>
						<?= nl2br(gh_h((string) ($d['b_content'] ?? ''))) ?>
					<?php } else { ?>
						<?= $funcLibrary->sanitizeBoardHtmlForDisplay($d['b_content'] ?? null) ?>
					<?php } ?>
				</div>

				<?php if (!$hasReply) { ?>
					<div class="btnWrap">
						<a href="<?= gh_h($editHref) ?>" class="editBtn">수정</a>

						<form
							action="./board_ok.php?bbsid=<?= rawurlencode((string) $bbsid) ?>"
							method="post"
							onsubmit="return qnaDeleteSubmit(this);">
							<input type="hidden" name="bbsid" value="<?= gh_h((string) $bbsid) ?>">
							<input type="hidden" name="w" value="d">
							<input type="hidden" name="idx" value="<?= (int) ($d['idx'] ?? 0) ?>">
							<input type="hidden" name="b_password" value="">
							<button type="submit" class="deleteBtn">삭제</button>
						</form>
					</div>
				<?php } ?>
			</div>

			<?php if ($hasReply) { ?>
				<div class="answer">
					<div class="top">
						<div class="titArea">
							<div class="status">답변</div>
							<div class="tit"><?= gh_h((string) ($replyData['b_subject'] ?? '질문에 답변드립니다.')) ?></div>
						</div>
						<div class="date">
							등록일 <span class="bar"></span> <?= gh_h((string) ($replyData['regdate2'] ?? '')) ?>
						</div>
					</div>

					<div class="content">
						<?= $funcLibrary->sanitizeBoardHtmlForDisplay($replyData['b_content'] ?? null) ?>
					</div>
				</div>
			<?php } ?>

			<a class="listBtn" href="<?= gh_h($listHref) ?>"><span>List</span> <img src="/images/page/bbs_view_list_icon.png" alt=""></a>
		</div>
	</div>

</div>

<script>
	function qnaDeleteSubmit(form) {
		if (!confirm('정말 삭제하시겠습니까?')) {
			return false;
		}
		var needPassword = <?= $needDeletePassword ? 'true' : 'false' ?>;
		if (needPassword) {
			var pw = window.prompt('비밀번호를 입력해 주세요.');
			if (!pw) {
				return false;
			}
			form.b_password.value = pw;
		}
		return true;
	}
</script>
