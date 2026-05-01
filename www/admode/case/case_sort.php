<?php
$ghPath = '../../';
include_once($ghPath . 'include/html/admin_top.php');
?>
<style>
	.caseList {
		cursor: pointer;
	}

	.caseList li {
		display: inline-block;
		height: 45px;
		line-height: 40px;
	}

	.caseList .img {
		width: 60px;
	}

	.caseList.on {
		background: #dddddd;
	}

	.upDown {
		margin: 5px;
		cursor: pointer;
		width: 150px;
	}

	.upDown:hover {
		text-decoration: underline;
	}

	.caseList ul {
		display: flex;
		align-items: center;
		/* 세로 중앙 정렬 */
		gap: 10px;
		/* 이미지와 텍스트 사이 간격 */
	}

	.caseList .img {
		display: flex;
		align-items: center;
		/* 이미지 세로 중앙 정렬 */
	}

	.caseList .title {
		display: flex;
		align-items: center;
		/* 텍스트 세로 중앙 정렬 */
	}
</style>

<!-- <form name="categorySelectForm" method="post" action="?<?= $funcLibrary->queryString() ?>">
<div class="categorySelect">
	<select name="cate" class="input_select">
		<option value="">선택</option>
		<?php
		//1차 분류
		$where = "where depth = 1 and category = 'case' ";
		$orderby = "num asc|c_code asc|idx desc";
		$listResult = $queryLibrary->getList($where, $bindParam, 'gh_category_table', $orderby, 1, 99);
		foreach ($listResult['result'] as $cateData) {
			if ($cate == $cateData['c_code']) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
		?>
			<option value="<?= $cateData['c_code'] ?>" <?= $selected ?>><?= $cateData['c_name'] ?></option>
		<?php } ?>
	</select>
	<button type="submit" class="search_btn">검색</button>
</div>
</form> -->
<?php
$tableName = 'gh_case_table';
$uploadDirectory = 'case';
$bindParam = array();
$where = " where substring(c_code,1,3) = :pageType ";
$bindParam[] = array('pageType', $pageType);
$orderby = "num asc|idx desc";
$listResult = $queryLibrary->getList($where, $bindParam, $tableName, $orderby, 1, 1000);
?>
<br>
<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
	<tr>
		<td>총 제품 수 : <?= $listResult['listTotal']; ?></td>
	</tr>
</table>
<form name="fwrite" method="post" action="./case_sort_ok.php?<?= $funcLibrary->queryString() ?>w=u&tableName=<?= $tableName ?>" onsubmit="return formSubmit(this);" enctype="multipart/form-data" style="margin:0px;">
	<table width="100%" cellpadding="0" cellspacing="0" class="adminMenuTable">
		<col width="500">
		</col>
		<col>
		</col>
		<tr>
			<td colspan="10" class="line1"></td>
		</tr>
		<tr class="list col1">
			<td class="td2" valign="top">
				<div style="overflow-y:scroll;height:500px;">
					<?php
					$i = 1;
					foreach ($listResult['result'] as $d) {
						if (($d['p_open'] ??= null) != '1') {
							$_style = 'style="color:gray;"';
							$_openText = '';
							//$_openText = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- [비공개]';
						} else {
							$_style = '';
							$_openText = '';
						}
					?>
						<div class="caseList" <?= $_style ?>>
							<input type="hidden" name="idx[]" value="<?= $d['idx'] ?>">
							<ul>
								<li class="img"><?php if ($d['thumb_file']) { ?><img src="<?= $ghPath ?>data/case/<?= $d['thumb_file'] ?>" width="50"><?php } ?></li>
								<li class="title" style="vertical-align:middle;">[<?= $i ?>]<?= $_openText ?> <?= $d['title'] ?></li>
							</ul>
						</div>
					<?php $i++;
					} ?>
				</div>
			</td>
			<td class="td2" valign="top">
				<div class="upDown" data-direction="up" data-number="max">↑ &nbsp;<strong>맨위로이동</strong></div>
				<div class="upDown" data-direction="up" data-number="20">↑ &nbsp;위로이동(+20)</div>
				<div class="upDown" data-direction="up" data-number="10">↑ &nbsp;위로이동(+10)</div>
				<div class="upDown" data-direction="up" data-number="5">↑ &nbsp;위로이동(+5)</div>
				<div class="upDown" data-direction="up" data-number="2">↑ &nbsp;위로이동(+2)</div>
				<div class="upDown" data-direction="up" data-number="1">↑ &nbsp;위로이동(+1)</div>
				<br>
				<div class="upDown" data-direction="down" data-number="1">↓ &nbsp;아래로이동(-1)</div>
				<div class="upDown" data-direction="down" data-number="2">↓ &nbsp;아래로이동(-2)</div>
				<div class="upDown" data-direction="down" data-number="5">↓ &nbsp;아래로이동(-5)</div>
				<div class="upDown" data-direction="down" data-number="10">↓ &nbsp;아래로이동(-10)</div>
				<div class="upDown" data-direction="down" data-number="20">↓ &nbsp;아래로이동(-20)</div>
				<div class="upDown" data-direction="down" data-number="max">↓ &nbsp;<strong>맨아래로이동</strong></div>
			</td>
		</tr>
		<tr>
			<td colspan="9" class="line2"></td>
		</tr>
	</table>
	<p align="center" style="margin-top:30px;width:980px;">
		<button type="submit" class="red_btn">확 인</button>
		<button type="button" class="gray_btn" onclick="javascript:window.location='./case_sort.php?<?= $funcLibrary->queryString() ?>'">취 소</button>
	</p>
</form>
<br>
<script type="text/javascript">
	function formSubmit() {
		if (!window.confirm("위 순서대로 수정하시겠습니까?")) {
			return false;
		}
	}

	$(function() {

		$(document).on("click", ".caseList", function() { //포트폴리오 선택
			var number = $(".caseList").index(this);
			$(".caseList").removeClass("on");
			$(this).addClass("on");
			//console.log(number)
		});

		$(".upDown").click(function() { //순서 변경
			if ($(".caseList.on").length == 0) {
				alert("제품을 선택해 주세요.");
				return;
			}

			var selectIndex = $(".caseList").index($(".caseList.on")); //선택된 포트폴리오
			var direction = $(this).data("direction"); //up or down
			var number = $(this).data("number"); //이동 수치
			var totalNumber = $(".caseList").length - 1; //마지막 포트폴리오 index

			if (direction == "up") { //위로 이동
				if (number == "max") { //맨위로 
					$(".caseList:eq(0)").before($(".caseList:eq(" + selectIndex + ")"));
				} else {
					moveNumber = selectIndex - number; //기준 
					if (moveNumber <= 0) { //이동할 숫자가 0보다 작으면 맨 위로
						$(".caseList:eq(0)").before($(".caseList:eq(" + selectIndex + ")"));
					} else {
						$(".caseList:eq(" + moveNumber + ")").before($(".caseList:eq(" + selectIndex + ")"));
					}
				}
			} else {
				if (number == "max") { //아래로 이동
					$(".caseList:eq(" + totalNumber + ")").after($(".caseList:eq(" + selectIndex + ")"));
				} else {
					moveNumber = selectIndex + number; //기준 
					if (moveNumber > totalNumber) { //이동할 숫자가 총 갯수보다 크면 맨 아래로
						$(".caseList:eq(" + totalNumber + ")").after($(".caseList:eq(" + selectIndex + ")"));
					} else {
						$(".caseList:eq(" + moveNumber + ")").after($(".caseList:eq(" + selectIndex + ")"));
					}
				}
			}
		});
	});
</script>
<?php include_once $ghPath . 'include/html/admin_bottom.php'; ?>