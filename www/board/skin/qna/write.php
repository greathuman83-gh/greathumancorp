<?php
$isUpdate = ($w === 'u');
$isMember = !empty($userId);
// 등록: 비회원만 비밀번호 / 수정: 비회원글(m_id 없음)만 비밀번호 변경 필드
if ($isUpdate) {
    $needPassword = (trim((string) ($d['m_id'] ?? '')) === '');
} else {
    $needPassword = !$isMember;
}
$formAction = './board_ok.php?' . $funcLibrary->queryString();
$cancelUrl = './board.php?bbsid=' . rawurlencode((string) $bbsid);
$listQuery = $funcLibrary->queryString('w,idx');
if ($listQuery !== '') {
    $cancelUrl = './board.php?' . $listQuery;
}
?>
<div id="main" class="m00 m<?= $pn ?>0 m<?= $pn ?><?= $sn ?> bbs contact_write">

    <div class="section section1">
        <div class="innerWrap">
            <form name="fwrite" method="post" action="<?= gh_h($formAction) ?>" onsubmit="return formSubmit();">
                <input type="hidden" name="bbsid" value="<?= gh_h((string) $bbsid) ?>">
                <input type="hidden" name="w" value="<?= gh_h((string) $w) ?>">
                <?php if ($isUpdate) { ?>
                    <input type="hidden" name="idx" value="<?= (int) $idx ?>">
                <?php } ?>

                <div class="formBox">
                    <div class="row row2">
                        <div class="field">
                            <div class="title">이름 <span class="req">*</span></div>
                            <input type="text" id="b_name" name="b_name" value="<?= gh_h((string) ($d['b_name'] ?? '')) ?>" placeholder="이름을 입력해 주세요." required maxlength="50">
                        </div>
                        <div class="field">
                            <div class="title">제목 <span class="req">*</span></div>
                            <input type="text" id="b_subject" name="b_subject" value="<?= gh_h((string) ($d['b_subject'] ?? '')) ?>" placeholder="제목을 입력해 주세요." required maxlength="200">
                        </div>
                    </div>
                    <?php if ($needPassword) { ?>
                        <div class="row row2 rowPw">
                            <div class="field">
                                <div class="title">비밀번호<?= $isUpdate ? '' : ' <span class="req">*</span>' ?></div>
                                <input type="password" id="b_password" name="b_password" placeholder="<?= $isUpdate ? '변경할 비밀번호를 입력해 주세요.' : '비밀번호를 입력해 주세요.' ?>" <?= $isUpdate ? '' : ' required' ?> minlength="4" maxlength="20" autocomplete="new-password">
                                <p class="help">※ <?= $isUpdate ? '변경하지 않으려면 비워 두세요.' : '수정 및 삭제 시 사용됩니다.' ?> (4~20자)</p>
                            </div>
                            <div class="field">
                                <div class="title">비밀번호 확인<?= $isUpdate ? '' : ' <span class="req">*</span>' ?></div>
                                <input type="password" id="b_password_confirm" name="b_password_confirm" placeholder="<?= $isUpdate ? '변경할 비밀번호를 다시 입력해 주세요.' : '비밀번호를 다시 입력해 주세요.' ?>" <?= $isUpdate ? '' : ' required' ?> minlength="4" maxlength="20" autocomplete="new-password">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="field">
                            <div class="title">내용 <span class="req">*</span></div>
                            <textarea id="b_content" name="b_content" placeholder="질문 내용을 입력해 주세요." required><?= gh_h((string) ($d['b_content'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="btnWrap">
                    <a href="<?= gh_h($cancelUrl) ?>" class="cancelBtn">취소</a>
                    <button type="submit" class="submitBtn"><?= $isUpdate ? '수정' : '등록' ?></button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    var qnaWriteSubmitting = false;

    function formSubmit() {
        if (qnaWriteSubmitting) {
            return false;
        }

        var f = document.fwrite;
        var isUpdate = <?= $isUpdate ? 'true' : 'false' ?>;

        if (!f.b_name.value.trim()) {
            alert('이름을 입력해 주세요.');
            f.b_name.focus();
            return false;
        }
        if (!f.b_subject.value.trim()) {
            alert('제목을 입력해 주세요.');
            f.b_subject.focus();
            return false;
        }
        if (f.b_password) {
            var pw = f.b_password.value;
            var pwConfirm = f.b_password_confirm ? f.b_password_confirm.value : '';
            // 수정: 비워 두면 기존 비밀번호 유지 / 등록: 필수
            if (!isUpdate || pw || pwConfirm) {
                if (!pw) {
                    alert(isUpdate ? '변경할 비밀번호를 입력해 주세요.' : '비밀번호를 입력해 주세요.');
                    f.b_password.focus();
                    return false;
                }
                if (pw.length < 4 || pw.length > 20) {
                    alert('비밀번호는 4자 이상 20자 이하로 입력해 주세요.');
                    f.b_password.focus();
                    return false;
                }
                if (pw !== pwConfirm) {
                    alert('비밀번호가 일치하지 않습니다.');
                    f.b_password_confirm.focus();
                    return false;
                }
            }
        }
        if (!f.b_content.value.trim()) {
            alert('내용을 입력해 주세요.');
            f.b_content.focus();
            return false;
        }

        qnaWriteSubmitting = true;
        var submitBtn = f.querySelector('.submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        return true;
    }
</script>