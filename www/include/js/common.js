
function chkRegPattern(pType, strInput,min,max) {//패턴 검증 정규식
    var regPattern;
    var arrResult;

    if (pType != undefined && strInput != undefined) {
        switch (pType) {
            case 'id': regPattern = /^[A-Za-z]{1}[A-Za-z0-9_]{3,11}$/; break;//첫글자는 영문, 영대소문자나숫자 or 특수문자중 _ 만 허용
            case 'num': regPattern = /^[0-9]+$/; break; //숫자만 입력
            case 'han': regPattern = /^[가-힣]+[가-힣]$/; break;
            case 'eng': regPattern = /^[a-zA-Z]+[a-zA-Z]$/; break;
            case 'ju1': regPattern = /^([\d]{2})(0[1-9]{1}|1[0-2]{1})(0[1-9]{1}|[1-2][\d]{1}|3[0-1]{1})$/; break;
            case 'ju2': regPattern = /^([1-8]{1})([\d]{6})$/; break;
            case 'id': regPattern = /^([a-zA-Z]{1})([\w-]{5,14})/; break;
            case 'pwd': regPattern = /^[\w]{6,15}/; break;
            case 'ans': regPattern = /^[가-힣\w\s-]{2,20}/; break;
            case 'mail1': regPattern = /^[a-z0-9_+.-]+$/; break;
            case 'mail2': regPattern = /^[\w.-]+\.[a-zA-Z]{2,5}/; break;
            case 'mail': regPattern = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i; break; //이메일 풀 필드
            case 'con1': regPattern = /^[0]{1}[1-7]{1}[\d]{0,1}/; break;
            case 'con2': regPattern = /^[1-9]{1}[\d]{2,3}/; break;
            case 'con3': regPattern = /^[\d]{4}/; break;
            case 'tel': regPattern = /^\d{2,3}-\d{3,4}-\d{4}$/; break; //일반 전화번호 정규식
            case 'mobile': regPattern = /^\d{3}-\d{3,4}-\d{4}$/; break; //모바일 정규식

            default: return false; break;
        }
        result = regPattern.test(strInput);
        return result;
        //result = strInput.match(regPattern);
        /*result = regPattern.exec(strInput);
        if ( result.split(",")(0) == strInput){return true;
        }else{return false;}*/
    } else {
        return false;
    }
}

function CheckPass(str,mode,min,max){//패스워드 검증 정규식
	var eng = str.search(/[a-z]/ig);
	var num = str.search(/[0-9]/g);
	var spe = str.search(/[`~!@@#$%^&*|₩₩₩"₩";:₩/?]/gi);
	if(mode==1){//숫자만
		if(str.length >= min && str.length <= max && eng > -1){
			return 1;
		}
	}else if(mode==2){//영문 숫자 조합
		if(str.length >= min && str.length <= max && eng > -1 && num > -1 ){
			return 1;
		}
	}else{//영문,숫자,특수문자 조합
		if(str.length >= min && str.length <= max && eng > -1 && num > -1 && spe > -1 ){
			return 1;
		}
	}
}

var checkPasswordValidate = function (val) {//연속된 숫자 금지
    return validatePassword(val, {
        length: [6, Infinity],
        lower: 0,
        upper: 0,
        numeric: 0,
        special: 0,
        badWords: ["password"],
        badSequenceLength: 4
    });
};

// a 태그에서 onclick 이벤트를 사용하지 않기 위해
function win_open(url, name, option)
{
	var popup = window.open(url, name, option);
	popup.focus();
}



//링크
function move_location(on){
	location=on;
}

//입력값 길이 검사
function check_value(on,str,n) {
	str_value=on.value;
	str_value=str_value.replace(/ /g,"");
	str_value=str_value.replace(/\r\n/g,"");

	if (str_value.length==0) {
		alert(str + " 입력하세요.");
		on.value="";
		on.focus();
		return false;
	} else if (str_value.length<n) {
		alert(str + " " + n + "글자 이상 입력하세요.");
		on.focus();
		return false;
	}
}

//날짜 형식 검사 : 2006-07-08
function check_date(date_year,date_month,date_day) {
	if (
			((date_month=="02")&&(date_day>28))
			||((date_month=="04")&&(date_day>30)) || ((date_month=="06")&&(date_day>30))
			||((date_month=="09")&&(date_day>30)) || ((date_month=="11")&&(date_day>30))
		) {
		alert("잘못된 날짜입니다. 다시 선택하세요.");
		return false;
	}
}



//이메일 검사
function verify_email(on) {
	var reg = /^[A-Za-z0-9_\-]+([.][A-Za-z0-9_\-]+)*[@][A-Za-z0-9_\-]+([.][A-Za-z0-9_\-]+)+$/;
	return reg.test(on);
}

//영문,숫자만 쓰기 경고~!
function check()
{
 var form = document.form1;
 var str = form.m_id.value;
 for(var i=0; i<str.length; i++)
 {
  if(((str.charCodeAt(i) >= 48 && str.charCodeAt(i) <=57) || (str.charCodeAt(i) >=65 && str.charCodeAt(i) <= 90) || (str.charCodeAt(i) >= 97 && str.charCodeAt(i) <= 122)))
  {

  }
  else {   alert("영문이나 숫자만 쓸수 있습니다.");
   form.m_id.value = "";
   return false;} // 처리
 }
}

//한글,영문만 쓰기 경고~!
function check2()
{
 var form = document.form1;
 var str = form.m_nick.value;
 for(var i=0; i<str.length; i++)
 {
  if(((str.charCodeAt(i)<65)||((str.charCodeAt(i)<=127)&&
	(str.charCodeAt(i)>122)))&&((str.charCodeAt(i))<48||(str.charCodeAt(i))>57))
  {
 alert("한글이나 영어만 쓸수 있습니다.");
   form.m_nick.value = "";
   return false;
  }
  else {   } // 처리
 }
}


//이미지 파일 확장자 체크
function imgFileCheck(file,max = 3){
	let file_path = file.value;
	let fileSize = file.files[0].size;
	let reg = /(.*?)\.(jpg|pdf|jpeg|png|gif)$/i;
	// 허용되지 않은 확장자일 경우
	if (file_path != "" && (file_path.match(reg) == null || reg.test(file_path) == false)) {
		file.value = '';
		alert("이미지 및 PDF 파일만 업로드 가능합니다.");
		return;
	}

	//용량 체크
	let maxSize = max * 1024 * 1024; // x MB 사이즈 제한
	// 파일 크기 제한 확인
	if (fileSize > maxSize) {
		file.value = '';
		alert('파일 첨부 사이즈는 '+max+'MB 이내로 가능합니다.');
		return;
	}
}

//첨부 파일 확장자 체크
function attachFileCheck(file,max = 10){
	let file_path = file.value;
	let fileSize = file.files[0].size;
	let reg = /(.*?)\.(jpg|jpeg|gif|bmp|png|wmv|mov|avi|mpg|mpeg|asf|mp3|wma|ppt|pptx|xls|xlsx|doc|docx|hwp|alz|zip|rar|rtf|flv|pdf|mp4)$/i;
	// 허용되지 않은 확장자일 경우
	if (file_path != "" && (file_path.match(reg) == null || reg.test(file_path) == false)) {
		file.value = '';
		alert("허용된 확장자 파일만 업로드 가능합니다.");
		return;
	}
	
	//용량체크
	let maxSize = max * 1024 * 1024; // x MB 사이즈 제한
	// 파일 크기 제한 확인
	if (fileSize > maxSize) {
		file.value = '';
		alert('파일 첨부 사이즈는 '+max+'MB 이내로 가능합니다.');
		return;
	}
}


function login_chk(form){
  if ($("#m_id").val()==""){
      alert("이메일 주소를 입력해 주세요.");
	  $("#m_id").focus();
	  return false;
  }

  if ($("#m_pwd").val()==""){
      alert("비밀번호를 입력해 주세요.");
	  $("#m_pwd").focus();
	  return false;
  }
}


function find_id(form){
  if ($("#m_name").val()==""){
      alert("이름을 입력해 주세요.");
	  $("#m_name").focus();
	  return false;
  }

  if ($("#m_mobile").val()==""){
      alert("연락처를 입력해 주세요.");
	  $("#m_mobile").focus();
	  return false;
  }
}

function find_pwd(form){
  if ($("#m_name").val()==""){
      alert("이름을 입력해 주세요.");
	  $("#m_name").focus();
	  return false;
  }

  if ($("#m_id").val()==""){
      alert("이메일 주소를 입력해 주세요.");
	  $("#m_id").focus();
	  return false;
  }
}


// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format(obj)
{
	let data = String(obj);
	let regx = new RegExp(/(-?\d+)(\d{3})/);
	let bExists = data.indexOf(".", 0);//0번째부터 .을 찾는다.
	let strArr = data.split('.');
	while (regx.test(strArr[0])) {//문자열에 정규식 특수문자가 포함되어 있는지 체크
		//정수 부분에만 콤마 달기 
		strArr[0] = strArr[0].replace(regx, "$1,$2");//콤마추가하기
	}
	if (bExists > -1) {
		//. 소수점 문자열이 발견되지 않을 경우 -1 반환
		data = strArr[0] + "." + strArr[1];
	} else { //정수만 있을경우 //소수점 문자열 존재하면 양수 반환 
		data = strArr[0];
	}
	return data;//문자열 반환
}

//콤마 풀기
function uncomma(str) {
	str = "" + str.replace(/,/gi, ''); // 콤마 제거 
	str = str.replace(/(^\s*)|(\s*$)/g, ""); // trim()공백,문자열 제거 
	return (new Number(str));//문자열을 숫자로 반환
}
//input box 콤마달기
function inputNumberFormat(obj) {
	obj.value = comma(obj.value);
}
//input box 콤마풀기 호출
function uncomma_call(){
	let input_value = document.getElementById('input1');
	input_value.value = uncomma(input_value.value);
}


function limit_text(max,id){
	var count = $("#"+id+"").val().length;
	if(count > max){
		alert("메시지는 공백 포함 "+max+"글자 까지만 작성하실 수 있습니다.");
		var strlen = $("#"+id+"").val().length-1;
		var con_val = $("#"+id+"").val();
		var input_val = con_val.substring(0, strlen);
		count = strlen;
		$("."+id+"").text(strlen);
		$("#"+id+"").val(input_val);
	}
	$("."+id+"").text(count);
}

//======== 아이디 중복 체크 =============================
function idConfirmReset(){
	$("#idConfirm").val("2");
}

function idCheck(){
	let m_id = $("#memberID").val();
	m_id = String(m_id);
	let pattern = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i; //이메일형태
	if (pattern.test(m_id) == false){
		$("#idConfirm").val("2");
		alert('이메일 주소를 올바르게 입력해 주세요.');
		//$(".id_chk_txt").html("<font style='color:red'>아이디의 첫 글자는 영문으로 입력해 주세요.</font>");
		//$(".id_chk_txt").fadeIn(300);
		return;
	}

	/*
	if (!chkRegPattern("id",m_id)){
		$("#idConfirm").val("2");
		alert('아이디는 4~16자리의 영문, 숫자와 특수기호 _만 사용하실 수 있습니다.');
		//$(".id_chk_txt").html("<font style='color:red'>아이디는 4~16자리의 영문, 숫자와 특수기호 _만 사용하실 수 있습니다.</font>");
		//$(".id_chk_txt").fadeIn(300);
		return;
	}
	*/

	$.ajax({
		type : "POST" //"POST", "GET"
		, async : true //true, false
		, url : "/member/id_chk.php" //Request URL
		, dataType : "Json" //전송받을 데이터의 타입
								   //"xml", "html", "script", "json" 등 지정 가능
								   //미지정시 자동 판단
		, timeout : 30000 //제한시간 지정
		, cache : false  //true, false
		, data : "m_id="+m_id //서버에 보낼 파라메터
		, contentType: "application/x-www-form-urlencoded; charset=UTF-8"
		, error : function(request, status, error) {
		 //통신 에러 발생시 처리
		}
		, success : function(response, status, request) {
		 //통신 성공시 처리
			if (response['result']=="N"){
				$("#idConfirm").val("2");
				alert("해당 아이디는 사용하실 수 없습니다.");
				//$(".id_chk_txt").html("<font style='color:red'>사용불가</font>");
				//$(".id_chk_txt").fadeIn(300);
				return;
			}else if(response['result']=="Y"){
				$("#idConfirm").val("1");
				alert("사용 가능한 아이디 입니다.");
				//$(".id_chk_txt").html("<font style='color:blue'>사용가능</font>");
				//$(".id_chk_txt").fadeIn(300);
				return;
			}
		}
		, beforeSend: function() {
		 //통신을 시작할때 처리
		
		}
		, complete: function() {
		 //통신이 완료된 후 처리
		
		}
	});	
}
//======== 아이디 중복 체크 끝=============================


//연락처 자동 하이픈처리
const autoHyphen = (target) => {
	target.value = target.value
	.replace(/[^0-9]/g, '')
	.replace(/^(\d{2,3})(\d{3,4})(\d{4})$/, `$1-$2-$3`);
}


$(function(){

	/*============== 관리자 메뉴 =============*/
	var adminMobileBreakPoint = 1024;
	var $adminMenuList = $('.admin_menu_depth1>ul>li');
	var $mobileMenuToggle = $('.mobile-gnb-toggle');

	function isMobileAdminMenu() {
		return window.innerWidth <= adminMobileBreakPoint;
	}

	function closeMobileAdminMenu() {
		$('body').removeClass('admin-mobile-open');
		$mobileMenuToggle.attr('aria-expanded', 'false');
		$('.admin_menu_depth2').stop(true, true).hide();
	}

	function setupAdminListMobileCards() {
		var isMobile = isMobileAdminMenu();
		var $listTables = $('#contents .contents > table.adminMenuTable').filter(function() {
			return $(this).find('tr.bgcol1, tr.bold.col1').length > 0;
		});
		var hasActiveCard = false;

		$listTables.each(function() {
			var $table = $(this);
			var $headerRow = $table.find('tr.bgcol1, tr.bold.col1').first();
			var $actionBox = $table.prevAll('.mobile-card-actions').first();
			var labels = [];
			var $listRows = $table.find('tr[class*="list"]');
			var $emptyRows = $table.find('tr').filter(function() {
				var $row = $(this);
				if ($row.is($headerRow) || $row.is('[class*="list"]')) {
					return false;
				}
				if ($row.find('td.line1, td.line2, td.line3').length > 0) {
					return false;
				}
				var $tds = $row.children('td');
				return $tds.length > 0 && $tds.length <= 2 && $tds.filter('[colspan]').length > 0;
			});

			$headerRow.children('th,td').each(function() {
				labels.push($.trim($(this).text()).replace(/\s+/g, ' '));
			});

			$table.removeClass('mobile-card-table');
			$table.find('tr.mobile-card-row').removeClass('mobile-card-row');
			$table.find('tr.mobile-card-empty').removeClass('mobile-card-empty');
			$table.find('tr[class*="list"] td').removeAttr('data-label');
			$table.find('tr.mobile-card-empty td').removeAttr('data-label');
			$table.prevAll('.mobile-card-list').first().remove();
			$table.nextAll('.mobile-card-list').first().remove();
			$table.removeClass('mobile-card-source-hidden');
			$table.nextAll('br').first().removeClass('mobile-card-source-hidden');

			if (!isMobile) {
				if ($actionBox.length) {
					$actionBox.remove();
				}
				return;
			}

			var $registerButtons = $headerRow.find('.red_btn');
			if ($registerButtons.length > 0) {
				if (!$actionBox.length) {
					$actionBox = $('<div class="mobile-card-actions"></div>');
					$table.before($actionBox);
				}
				$actionBox.empty();
				$registerButtons.each(function() {
					var $btn = $(this);
					var $copyTarget = $btn.parent('a').length ? $btn.parent('a') : $btn;
					$actionBox.append($copyTarget.clone(true));
				});
			} else if ($actionBox.length) {
				$actionBox.remove();
			}

			var $cardList = $('<div class="mobile-card-list"></div>');
			$listRows.each(function() {
				var $row = $(this);
				var $cells = $row.children('td');

				if (!$cells.length) {
					return;
				}

				var $card = $('<div class="mobile-card-item"></div>');
				$cells.each(function(index) {
					var label = labels[index] || '';
					var $field = $('<div class="mobile-card-field"></div>');
					var $label = $('<div class="mobile-card-label"></div>').text(label);
					var $value = $('<div class="mobile-card-value"></div>').html($(this).html());
					$field.append($label).append($value);
					$card.append($field);
				});
				$cardList.append($card);
			});

			if ($listRows.length === 0 && $emptyRows.length > 0) {
				$emptyRows.each(function() {
					var txt = $.trim($(this).text()).replace(/\s+/g, ' ');
					if (!txt) {
						return;
					}
					var $emptyCard = $('<div class="mobile-card-item mobile-card-empty-item"></div>');
					$emptyCard.append($('<div class="mobile-card-empty-text"></div>').text(txt));
					$cardList.append($emptyCard);
				});
			}

			if ($cardList.children().length > 0) {
				$table.addClass('mobile-card-source-hidden');
				$table.nextAll('br').first().addClass('mobile-card-source-hidden');
				if ($actionBox.length) {
					$actionBox.after($cardList);
				} else {
					$table.before($cardList);
				}
				hasActiveCard = true;
			}
		});

		$('body').toggleClass('admin-mobile-card-active', isMobile && hasActiveCard);
	}

	$adminMenuList.mouseenter(function(){
		if (isMobileAdminMenu()) {
			return;
		}
		$('.admin_menu_depth2').stop().hide();
		$(this).find('.admin_menu_depth2').stop().slideDown({duration: 500,easing: 'easeOutQuart'});
	});

	$('.admin_menu_depth1').mouseleave(function(){
		if (isMobileAdminMenu()) {
			return;
		}
		$('.admin_menu_depth2').stop().hide();
	});

	$mobileMenuToggle.click(function() {
		if (!isMobileAdminMenu()) {
			return;
		}
		if ($('body').hasClass('admin-mobile-open')) {
			closeMobileAdminMenu();
			$('#bg').hide();
		} else {
			$('body').addClass('admin-mobile-open');
			$mobileMenuToggle.attr('aria-expanded', 'true');
			$('#bg').show();
		}
	});

	$('.admin_menu_depth1>ul>li>a').click(function(e) {
		if (!isMobileAdminMenu()) {
			return;
		}
		var $parent = $(this).parent();
		var $submenu = $parent.find('.admin_menu_depth2');
		if ($submenu.length === 0) {
			closeMobileAdminMenu();
			$('#bg').hide();
			return;
		}

		e.preventDefault();
		if ($submenu.is(':visible')) {
			$submenu.stop(true, true).slideUp(200);
			$parent.removeClass('mobile-open');
		} else {
			$('.admin_menu_depth2').stop(true, true).slideUp(200);
			$('.admin_menu_depth1>ul>li').removeClass('mobile-open');
			$submenu.stop(true, true).slideDown(200);
			$parent.addClass('mobile-open');
		}
	});

	$(window).on('resize.adminMenu', function() {
		if (!isMobileAdminMenu()) {
			closeMobileAdminMenu();
			$('#bg').hide();
		}
		setupAdminListMobileCards();
	});

	setupAdminListMobileCards();
	/*============== 관리자 메뉴 끝 =============*/


	//레이어팝업
	$(document).on('click',' #bg, [id*="popup"] .close ', function () {
		closeMobileAdminMenu();
		$("#bg").fadeOut();
		$('[id*="popup"]').hide();

		//팝업 안 내용이 삭제되어야할때 (수료증,입금확인서)
		$('#popup.popup2').html('');
	});


	$(".emailChange").change(function(){
		var email = $(this).val();

		if (email == ""){
			$(this).parent().find(".email2").val('');
			$(this).parent().find(".email2").focus();
		}else{
			$(this).parent().find(".email2").val(email);
		}
	});

	//자동등록방지코드
	$(".sec_code").click(function(){
		$.ajax({ 
			type: "POST", 
			url: "/include/plugin/kcaptcha/index.php", 
			data: "data=12",
			success: function(data){ 
				if(data){
						$('.sec_code img').attr('src', '/include/plugin/kcaptcha/?' + (new Date).getTime());
				}
			}
		});

	});


	//========================= 휴대폰 인증 ================================
	$("#sms_send").click(function(){//인증번호 발송
		if ($("#hp1").val() == "" || $("#hp2").val() == "" || $("#hp3").val() == "")
		{
			alert("휴대폰 번호를 입력해 주세요.");
			$("#hp1").focus();
			return;
		}

		var hp1 = $("#hp1").val();
		var hp2 = $("#hp2").val();
		var hp3 = $("#hp3").val();

		$("#sms_certify").val('2');
		$.ajax({
			type : "POST" //"POST", "GET"
			, async : true //true, false
			, url : "/member/sms_send.php" //Request URL
			, dataType : "Json" //전송받을 데이터의 타입
			, timeout : 30000 //제한시간 지정
			, cache : false  //true, false
			, data : "hp1="+hp1+"&hp2="+hp2+"&hp3="+hp3 //서버에 보낼 파라메터
			, contentType: "application/x-www-form-urlencoded; charset=UTF-8"
			, error : function(request, status, error) {
			 //통신 에러 발생시 처리
			}
			, success : function(response, status, request) {
			 //통신 성공시 처리
				if (response['result']=="N"){
					alert("인증번호 발송에 실패 했습니다. 휴대폰 번호를 다시 확인해 주세요.");
					console.log(response['error']);
					return;
				}else if(response['result']=="Y"){
					alert("입력하신 번호로 인증번호를 발송했습니다.");
					$("#sms_certify_code").removeAttr("disabled");
					return;
				}else if(response['result']=="D"){
					alert("입력하신 번호는 이미 가입되어있습니다.");
					return;
				}
			}
		});
	});


	$("#sms_certify_chk").click(function(){//인증번호 검증
		if ($("#sms_certify_code").val() == "")
		{
			alert("인증번호를 입력해 주세요.");
			$("#sms_certify_code").focus();
			return;
		}

		var sms_code = $("#sms_certify_code").val();

		$.ajax({
			type : "POST" //"POST", "GET"
			, async : true //true, false
			, url : "/member/sms_chk.php" //Request URL
			, dataType : "Json" //전송받을 데이터의 타입
			, timeout : 30000 //제한시간 지정
			, cache : false  //true, false
			, data : "sms_code="+sms_code //서버에 보낼 파라메터
			, contentType: "application/x-www-form-urlencoded; charset=UTF-8"
			, error : function(request, status, error) {
			 //통신 에러 발생시 처리
			}
			, success : function(response, status, request) {
			 //통신 성공시 처리
				if (response['result']=="N"){
					$("#sms_certify").val("2");
					alert("인증번호가 일치하지 않습니다. 다시 시도해 주세요.");
					$("#sms_certify_code").focus();
					return;
				}else if(response['result']=="Y"){
					$("#sms_certify").val("1");
					alert("인증되었습니다.");
					return;
				}
			}
		});

	});
	//================= 휴대폰 인증 끝 =========================================


	$(document).on('click','#checkAll',function(){//전체 체크 or 해제
		if($(this).prop("checked") === true){
			$(".checkList").each(function(){
				$(this).prop("checked",true);
			});
		}else{
			$(".checkList").each(function(){
				$(this).prop("checked",false);
			});
		}
	});

	//연락처 정규식변환
	$(document).on("keyup", ".phoneNumber", function() { 
		$(this).val( $(this).val().replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-") );
	});

	//=========== 추가 컨텐츠 생성 ==========================
	$(document).on('click','.addContent',function(e){
		const maxList = $(this).data('max');
		const addPosition = $(this).data('position');

		if($(this).parent().parent().find('.addContentList').length >= maxList){
			alert(maxList+'개 까지만 가능합니다.');
			return;
		}

		if(addPosition == 'prepend'){//선택 요소 앞에 추가
			let copyContent = $(this).parents('td').next('td').find('.addContentList:last').html();
			$(this).parents('td').next('td').first().prepend(
				'<div class="addContentList">'+copyContent+'<div class="contentDelete"><button type="button"class="addContentDel gray_icon_btn">-</button></div></div>'
			);
			$(this).parents('td').next('td').find('.addContentList:first-child').find("input,textarea").each(function(i, v) {
				if($(this).is(':checkbox')){
					$(this).prop('checked',false);
				}else{
					$(this).val('');
				}
			});
		}else{//선택 요소 뒤에 추가
			let copyContent = $(this).parents('td').next('td').find('.addContentList').html();
			$(this).parents('td').next('td').last().append(
				'<div class="addContentList">'+copyContent+'<div class="contentDelete"><button type="button"class="addContentDel gray_icon_btn">-</button></div></div>'
			);
			//복사된 요소 초기화
			$(this).parents('td').next('td').find('.addContentList:last-child').find("input,textarea").each(function(i, v) {
				if($(this).is(':checkbox')){
					$(this).prop('checked',false);
				}else{
					$(this).val('');
				}
			});

		}
	});
	//=========== 추가 컨텐츠 생성 끝 ==========================

	//=========== 추가 멀티 컨텐츠 생성 ==========================
	$(document).on('click','.addMultiContent',function(e){
		const maxList = $(this).data('max');
		const ea = $(this).parent().parent().find('.addContentList').length;

		if(ea >= maxList){
			alert(maxList+'개 까지만 가능합니다.');
			return;
		}

		var copyContent = $(this).parents('td').next('td').find('.addContentList').html();
		$(this).parents('td').next('td').last().append(
			'<div class="addContentList">'+copyContent+'<div class="contentDelete"><button type="button"class="addContentDel gray_icon_btn">-</button></div></div>'
		);
		$(this).parents('td').next('td').find('.addContentList:last-child').find("input").each(function(i, v) {
			let inputName = $(this).data("name");
			if(inputName){
				$(this).attr('name',inputName+ea+'[]');
			}
			$(this).val('');
		});
	});
	//=========== 추가 컨텐츠 생성 끝 ==========================


	//========== 추가 컨텐츠 삭제 =================
	$(document).on('click','.addContentDel',function(e){
		if(window.confirm('삭제하시겠습니까?')){
			$(this).closest('.addContentList').remove(); 
			window.dispatchEvent(new Event('resize'));
		}
	});
	//========== 추가 컨텐츠 삭제 끝=================

	/*======================= 테이블 생성 ================================*/
		//=========== 테이블 열 생성 ==========================
			$(document).on('click','.addTr',function(e){
				const maxList = $(this).data('max');

				if($(this).parent().parent().find('.addTrList').length >= maxList){
					alert(maxList+'개 까지만 가능합니다.');
					return;
				}
				let copyContent = $(this).parents('td').next('td').find('.addTrList:eq(1)').html();
				$(this).parents('td').next('td').last().append(
					'<ul class="addTrList"><div class="contentDelete"><button type="button"class="addTrDel gray_icon_btn">-</button></div>'+copyContent+'</ul>'
				);
				
				let trEa = $(this).parent().parent().find('.addTrList').length - 1;


				$(this).parents('td').next('td').find('.addTrList:last-child').find("li input").each(function(key, v) {
					$(this).val('');
					$(this).attr('name','tableTd'+trEa+'[]');
				});
			});

			//========== 테이블 열 삭제 =================
			$(document).on('click','.addTrDel',function(e){
				if(window.confirm('해당 열을 삭제하시겠습니까?')){
					$(this).closest('.addTrList').remove();
					
					$('.addTrList').each(function(key, v) {
						$(this).find('.tableTd').attr('name','tableTd'+key+'[]');
					});
					window.dispatchEvent(new Event('resize'));
				}
			});
			//========== 테이블 열 삭제 끝=================
		//=========== 테이블 열 생성 끝 ==========================

		//=========== 테이블 행 생성 ==========================
			$(document).on('click','.addTd',function(e){
				const maxList = $(this).data('max');

				if($(this).parent().parent().find('.addTrList:eq(0) .addTd').length >= maxList){
					alert(maxList+'개 까지만 가능합니다.');
					return;
				}
				let deleteButton = ''; //삭제버튼
				let addContent = ''; //input box

				$(this).parents('td').next('td').find('.addTrList').each(function(key, v) {
					if(key == 0){
						deleteButton = '<div><button type="button"class="addTdDel gray_icon_btn">-</button></div>'; //삭제버튼
					}else{
						deleteButton = '';
					}
					
					addContent = '<input type="text" name="tableTd'+key+'[]" class="input_text tableTd">';
				
					$(this).last().append(
						'<li class="addTd">'+deleteButton+addContent+'</li>'
					);
				});

				/*
				$(this).parents('td').next('td').find('.addTrList:last-child').find("input").each(function(i, v) {
					$(this).val('');
				});
				*/
			});

			//========== 테이블 행 삭제 =================
			$(document).on('click','.addTdDel',function(e){
				let fieldNumber = $(this).parent().parent().index() - 1;
				if(window.confirm('해당 행을 삭제하시겠습니까?')){
					$('.addTrList').each(function(key, v) {
						$(this).find('.addTd:eq('+fieldNumber+')').remove();
					});
					window.dispatchEvent(new Event('resize'));
				}
			});
			//========== 테이블 행 삭제 끝=================
		//=========== 테이블 행 생성 끝 ==========================

	/*======================= 테이블 생성 끝 ===============================*/
});