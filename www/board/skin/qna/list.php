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

$bindParam = array();
// 원글만 노출 (계층형 답변 제외)
$where = " where b_open='1' and (depth is null or depth = '') ";
if (($keyword ?? '') !== '') {
	$where .= " and (b_subject like :keyword or b_content like :keyword) ";
	$bindParam[] = array('keyword', $keyword, 'like');
}
$orderby = "regdate desc|idx desc";
$listResult = $queryLibrary->getList($where, $bindParam, 'gh_board_' . $bbsid, $orderby, $pg, 10);
$number = $listResult['number'];
$listUrl = './board.php?' . $funcLibrary->queryString('idx,w,b_name,b_password');
?>
<!-- 질문과 답변 -->
<div id="main" class="m00 m<?= (int) $pn ?>0 m<?= (int) $pn ?><?= (int) $sn ?> bbs contact">

	<div class="section section1">
		<div class="innerWrap">
			<div class="bbsTop">
				<div class="total">총 <span><?= (int) ($listResult['listTotal'] ?? 0) ?></span>건</div>
				<div class="right">
					<div class="searchWrap">
						<form name="board_search" method="get">
							<input type="hidden" name="bbsid" value="<?= gh_h((string) $bbsid) ?>">
							<div class="searchBox">
								<input type="text" class="" name="keyword" value="<?= gh_h((string) ($keyword ?? '')) ?>" placeholder="검색어를 입력해 주세요." />
								<button type="submit"><img src="/images/page/search_icon.png" alt=""></button>
							</div>
						</form>
					</div>
					<a href="?bbsid=<?= gh_h((string) $bbsid) ?>&w=a" class="writeBtn">
						<span>글쓰기</span> <img src="/images/page/bbs_plus_icon.png" alt="">
					</a>
				</div>
			</div>

			<ul class="list">
				<?php
				foreach ($listResult['result'] as $d) {
					$regdate = date('Y.m.d', strtotime(substr((string) ($d['regdate'] ?? ''), 0, 10)));
					$postIdx = (int) ($d['idx'] ?? 0);
					$parentIdx = (int) ($d['b_parent'] ?? $postIdx);

					$replyBind = array();
					$replyWhere = "where b_parent = :b_parent and length(ifnull(depth,'')) > 0 and b_open = '1'";
					$replyBind[] = array('b_parent', $parentIdx);
					$hasReply = $queryLibrary->dataTotal($replyWhere, $replyBind, 'gh_board_' . $bbsid) > 0;

					$maskedName = $maskWriterName((string) ($d['b_name'] ?? ''));
					$liClass = $hasReply ? ' class="end"' : '';
					$postMemberId = trim((string) ($d['m_id'] ?? ''));
					$isMine = ($postMemberId !== '' && !empty($userId) && (string) $userId === $postMemberId);
					$isDirect = $isMine || !empty($adminId);
					$viewHref = $isDirect
						? ('?' . $funcLibrary->queryString('idx,w,b_name,b_password') . 'idx=' . $postIdx)
						: '#';
				?>
					<li<?= $liClass ?>>
						<a href="<?= gh_h($viewHref) ?>" data-idx="<?= $postIdx ?>" data-direct="<?= $isDirect ? '1' : '0' ?>">
							<div class="num"><?= (int) $number ?></div>
							<div class="tit"><?= gh_h($funcLibrary->cutString($d['b_subject'] ?? '', 80, '..')) ?> <img class="lock_icon" src="/images/page/contact_lock_icon.png" alt=""> <?php if ($hasReply) { ?><span class="status end"><img src="/images/page/contact_chk_icon.png" alt=""> 답변완료</span><?php } else { ?><span class="status">답변접수</span><?php } ?></div>
							<div class="name"><?= gh_h($maskedName) ?></div>
							<div class="date"><?= gh_h((string) $regdate) ?></div>
						</a>
						</li>
					<?php
					$number--;
				}
					?>
			</ul>

			<div class="paging">
				<?= $funcLibrary->getUserPaging($_config['pageListEa'], $pg, $listResult['totalPage'], $_SERVER['PHP_SELF'] . '?' . $funcLibrary->queryString('pg') . 'pg=') ?>
			</div>
		</div>
	</div>

</div>

<div class="contactDim"></div>
<div class="contactPopup">
	<form name="contact_view" method="post" action="<?= gh_h($listUrl) ?>">
		<input type="hidden" name="bbsid" value="<?= gh_h((string) $bbsid) ?>">
		<input type="hidden" name="idx" id="view_idx" value="">
		<div class="popHead">
			<h3 class="popTit">비공개 게시글</h3>
			<p class="popDesc">게시글을 확인하려면 작성자 이름과 비밀번호를 입력해주세요.</p>
		</div>
		<div class="popForm">
			<div class="field">
				<div class="title">이름</div>
				<input type="text" id="view_name" name="b_name" placeholder="이름을 입력해 주세요." required maxlength="50" autocomplete="name">
			</div>
			<div class="field">
				<div class="title">비밀번호</div>
				<input type="password" id="view_password" name="b_password" placeholder="비밀번호를 입력해 주세요." required minlength="4" maxlength="20" autocomplete="current-password">
				<p class="help">※ 작성 시 입력한 정보를 입력해주세요.</p>
			</div>
		</div>
		<div class="btnWrap">
			<button type="button" class="cancelBtn">취소</button>
			<button type="submit" class="submitBtn">확인</button>
		</div>
	</form>
</div>

<script>
	(function() {
		var dim = document.querySelector('.contactDim');
		var popup = document.querySelector('.contactPopup');
		var form = popup.querySelector('form');
		var idxInput = document.getElementById('view_idx');
		var listLinks = document.querySelectorAll('.contact .list li a');
		var cancelBtn = popup.querySelector('.cancelBtn');

		function openPopup(idx) {
			idxInput.value = String(idx || '');
			dim.classList.add('show');
			popup.classList.add('show');
			var nameInput = document.getElementById('view_name');
			if (nameInput) {
				nameInput.focus();
			}
		}

		function closePopup() {
			dim.classList.remove('show');
			popup.classList.remove('show');
			form.reset();
			idxInput.value = '';
		}

		listLinks.forEach(function(link) {
			link.addEventListener('click', function(e) {
				if (link.getAttribute('data-direct') === '1') {
					return;
				}
				e.preventDefault();
				openPopup(link.getAttribute('data-idx'));
			});
		});

		cancelBtn.addEventListener('click', closePopup);
		dim.addEventListener('click', closePopup);

		form.addEventListener('submit', function(e) {
			if (!idxInput.value) {
				e.preventDefault();
				alert('게시글을 선택해 주세요.');
				return;
			}
			if (!form.b_name.value.trim()) {
				e.preventDefault();
				alert('이름을 입력해 주세요.');
				form.b_name.focus();
				return;
			}
			if (!form.b_password.value) {
				e.preventDefault();
				alert('비밀번호를 입력해 주세요.');
				form.b_password.focus();
			}
		});
	})();
</script>