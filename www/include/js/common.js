function chkRegPattern(pType, strInput, min, max) {
  //패턴 검증 정규식
  var regPattern;
  var arrResult;

  if (pType != undefined && strInput != undefined) {
    switch (pType) {
      case "id":
        regPattern = /^[A-Za-z]{1}[A-Za-z0-9_]{3,11}$/;
        break; //첫글자는 영문, 영대소문자나숫자 or 특수문자중 _ 만 허용
      case "num":
        regPattern = /^[0-9]+$/;
        break; //숫자만 입력
      case "han":
        regPattern = /^[가-힣]+[가-힣]$/;
        break;
      case "eng":
        regPattern = /^[a-zA-Z]+[a-zA-Z]$/;
        break;
      case "ju1":
        regPattern =
          /^([\d]{2})(0[1-9]{1}|1[0-2]{1})(0[1-9]{1}|[1-2][\d]{1}|3[0-1]{1})$/;
        break;
      case "ju2":
        regPattern = /^([1-8]{1})([\d]{6})$/;
        break;
      case "id":
        regPattern = /^([a-zA-Z]{1})([\w-]{5,14})/;
        break;
      case "pwd":
        regPattern = /^[\w]{6,15}/;
        break;
      case "ans":
        regPattern = /^[가-힣\w\s-]{2,20}/;
        break;
      case "mail1":
        regPattern = /^[a-z0-9_+.-]+$/;
        break;
      case "mail2":
        regPattern = /^[\w.-]+\.[a-zA-Z]{2,5}/;
        break;
      case "mail":
        regPattern =
          /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;
        break; //이메일 풀 필드
      case "con1":
        regPattern = /^[0]{1}[1-7]{1}[\d]{0,1}/;
        break;
      case "con2":
        regPattern = /^[1-9]{1}[\d]{2,3}/;
        break;
      case "con3":
        regPattern = /^[\d]{4}/;
        break;
      case "tel":
        regPattern = /^\d{2,3}-\d{3,4}-\d{4}$/;
        break; //일반 전화번호 정규식
      case "mobile":
        regPattern = /^\d{3}-\d{3,4}-\d{4}$/;
        break; //모바일 정규식

      default:
        return false;
        break;
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

function CheckPass(str, mode, min, max) {
  //패스워드 검증 정규식
  var eng = str.search(/[a-z]/gi);
  var num = str.search(/[0-9]/g);
  var spe = str.search(/[`~!@@#$%^&*|₩₩₩"₩";:₩/?]/gi);
  if (mode == 1) {
    //숫자만
    if (str.length >= min && str.length <= max && eng > -1) {
      return 1;
    }
  } else if (mode == 2) {
    //영문 숫자 조합
    if (str.length >= min && str.length <= max && eng > -1 && num > -1) {
      return 1;
    }
  } else {
    //영문,숫자,특수문자 조합
    if (
      str.length >= min &&
      str.length <= max &&
      eng > -1 &&
      num > -1 &&
      spe > -1
    ) {
      return 1;
    }
  }
}

var checkPasswordValidate = function (val) {
  //연속된 숫자 금지
  return validatePassword(val, {
    length: [6, Infinity],
    lower: 0,
    upper: 0,
    numeric: 0,
    special: 0,
    badWords: ["password"],
    badSequenceLength: 4,
  });
};

// a 태그에서 onclick 이벤트를 사용하지 않기 위해
function winOpen(url, name, option) {
  var popup = window.open(url, name, option);
  popup.focus();
}

//링크
function moveLocation(on) {
  location = on;
}

//입력값 길이 검사
function checkValue(on, str, n) {
  var strValue = on.value;
  strValue = strValue.replace(/ /g, "");
  strValue = strValue.replace(/\r\n/g, "");

  if (strValue.length == 0) {
    alert(str + " 입력하세요.");
    on.value = "";
    on.focus();
    return false;
  } else if (strValue.length < n) {
    alert(str + " " + n + "글자 이상 입력하세요.");
    on.focus();
    return false;
  }
}

//날짜 형식 검사 : 2006-07-08
function checkDate(dateYear, dateMonth, dateDay) {
  if (
    (dateMonth == "02" && dateDay > 28) ||
    (dateMonth == "04" && dateDay > 30) ||
    (dateMonth == "06" && dateDay > 30) ||
    (dateMonth == "09" && dateDay > 30) ||
    (dateMonth == "11" && dateDay > 30)
  ) {
    alert("잘못된 날짜입니다. 다시 선택하세요.");
    return false;
  }
}

//이메일 검사
function verifyEmail(on) {
  var reg =
    /^[A-Za-z0-9_\-]+([.][A-Za-z0-9_\-]+)*[@][A-Za-z0-9_\-]+([.][A-Za-z0-9_\-]+)+$/;
  return reg.test(on);
}

//영문,숫자만 쓰기 경고~!
function check() {
  var form = document.form1;
  var str = form.m_id.value;
  for (var i = 0; i < str.length; i++) {
    if (
      (str.charCodeAt(i) >= 48 && str.charCodeAt(i) <= 57) ||
      (str.charCodeAt(i) >= 65 && str.charCodeAt(i) <= 90) ||
      (str.charCodeAt(i) >= 97 && str.charCodeAt(i) <= 122)
    ) {
    } else {
      alert("영문이나 숫자만 쓸수 있습니다.");
      form.m_id.value = "";
      return false;
    } // 처리
  }
}

//한글,영문만 쓰기 경고~!
function check2() {
  var form = document.form1;
  var str = form.m_nick.value;
  for (var i = 0; i < str.length; i++) {
    if (
      (str.charCodeAt(i) < 65 ||
        (str.charCodeAt(i) <= 127 && str.charCodeAt(i) > 122)) &&
      (str.charCodeAt(i) < 48 || str.charCodeAt(i) > 57)
    ) {
      alert("한글이나 영어만 쓸수 있습니다.");
      form.m_nick.value = "";
      return false;
    } else {
    } // 처리
  }
}

//이미지 파일 확장자 체크
function imgFileCheck(file, max = 3) {
  let filePath = file.value;
  let fileSize = file.files[0].size;
  let reg = /(.*?)\.(jpg|pdf|jpeg|png|gif)$/i;
  // 허용되지 않은 확장자일 경우
  if (
    filePath != "" &&
    (filePath.match(reg) == null || reg.test(filePath) == false)
  ) {
    file.value = "";
    alert("이미지 및 PDF 파일만 업로드 가능합니다.");
    return;
  }

  //용량 체크
  let maxSize = max * 1024 * 1024; // x MB 사이즈 제한
  // 파일 크기 제한 확인
  if (fileSize > maxSize) {
    file.value = "";
    alert("파일 첨부 사이즈는 " + max + "MB 이내로 가능합니다.");
    return;
  }
}

//첨부 파일 확장자 체크
function attachFileCheck(file, max = 10) {
  let filePath = file.value;
  let fileSize = file.files[0].size;
  let reg =
    /(.*?)\.(jpg|jpeg|gif|bmp|png|wmv|mov|avi|mpg|mpeg|asf|mp3|wma|ppt|pptx|xls|xlsx|doc|docx|hwp|alz|zip|rar|rtf|flv|pdf|mp4)$/i;
  // 허용되지 않은 확장자일 경우
  if (
    filePath != "" &&
    (filePath.match(reg) == null || reg.test(filePath) == false)
  ) {
    file.value = "";
    alert("허용된 확장자 파일만 업로드 가능합니다.");
    return;
  }

  //용량체크
  let maxSize = max * 1024 * 1024; // x MB 사이즈 제한
  // 파일 크기 제한 확인
  if (fileSize > maxSize) {
    file.value = "";
    alert("파일 첨부 사이즈는 " + max + "MB 이내로 가능합니다.");
    return;
  }
}

// slide 애니메이션 — 이전 transitionend가 남아 있으면 열림/닫힘이 뒤집힘
function clearSlideTransition(el) {
  if (!el || !el._slideEnd) return;
  el.removeEventListener("transitionend", el._slideEnd);
  el._slideEnd = null;
  el.style.removeProperty("height");
  el.style.removeProperty("overflow");
  el.style.removeProperty("transition");
}

// slideDown — height transition (easeOutQuart ≈ cubic-bezier)
function slideDown(el, duration, easing) {
  if (!el) return;
  duration = duration || 400;
  easing = easing || "ease";
  clearSlideTransition(el);
  el.style.removeProperty("display");
  var display = window.getComputedStyle(el).display;
  if (display === "none") display = "block";
  el.style.display = display;
  var height = el.scrollHeight;
  el.style.overflow = "hidden";
  el.style.height = "0px";
  el.style.transition = "height " + duration + "ms " + easing;
  el.offsetHeight;
  el.style.height = height + "px";
  function onEnd(e) {
    if (e.target !== el || e.propertyName !== "height") return;
    el.style.removeProperty("height");
    el.style.removeProperty("overflow");
    el.style.removeProperty("transition");
    el.removeEventListener("transitionend", onEnd);
    el._slideEnd = null;
  }
  el._slideEnd = onEnd;
  el.addEventListener("transitionend", onEnd);
}

// slideUp — height transition
function slideUp(el, duration, easing) {
  if (!el) return;
  duration = duration || 400;
  easing = easing || "ease";
  if (window.getComputedStyle(el).display === "none") return;
  clearSlideTransition(el);
  el.style.height = el.scrollHeight + "px";
  el.style.overflow = "hidden";
  el.style.transition = "height " + duration + "ms " + easing;
  el.offsetHeight;
  el.style.height = "0px";
  function onEnd(e) {
    if (e.target !== el || e.propertyName !== "height") return;
    el.style.display = "none";
    el.style.removeProperty("height");
    el.style.removeProperty("overflow");
    el.style.removeProperty("transition");
    el.removeEventListener("transitionend", onEnd);
    el._slideEnd = null;
  }
  el._slideEnd = onEnd;
  el.addEventListener("transitionend", onEnd);
}

// fadeOut — opacity then hide
function fadeOut(el, duration) {
  if (!el) return;
  duration = duration || 400;
  el.style.transition = "opacity " + duration + "ms";
  el.style.opacity = "0";
  setTimeout(function () {
    el.style.display = "none";
    el.style.removeProperty("opacity");
    el.style.removeProperty("transition");
  }, duration);
}

function loginChk(form) {
  var mId = document.getElementById("m_id");
  var mPwd = document.getElementById("m_pwd");
  if (!mId || mId.value == "") {
    alert("이메일 주소를 입력해 주세요.");
    if (mId) mId.focus();
    return false;
  }

  if (!mPwd || mPwd.value == "") {
    alert("비밀번호를 입력해 주세요.");
    if (mPwd) mPwd.focus();
    return false;
  }
}

function findId(form) {
  var mName = document.getElementById("m_name");
  var mMobile = document.getElementById("m_mobile");
  if (!mName || mName.value == "") {
    alert("이름을 입력해 주세요.");
    if (mName) mName.focus();
    return false;
  }

  if (!mMobile || mMobile.value == "") {
    alert("연락처를 입력해 주세요.");
    if (mMobile) mMobile.focus();
    return false;
  }
}

function findPwd(form) {
  var mName = document.getElementById("m_name");
  var mId = document.getElementById("m_id");
  if (!mName || mName.value == "") {
    alert("이름을 입력해 주세요.");
    if (mName) mName.focus();
    return false;
  }

  if (!mId || mId.value == "") {
    alert("이메일 주소를 입력해 주세요.");
    if (mId) mId.focus();
    return false;
  }
}

// 자바스크립트로 PHP number_format 흉내
// 숫자에 , 를 출력
function numberFormat(obj) {
  let data = String(obj);
  let regx = new RegExp(/(-?\d+)(\d{3})/);
  let bExists = data.indexOf(".", 0); //0번째부터 .을 찾는다.
  let strArr = data.split(".");
  while (regx.test(strArr[0])) {
    //문자열에 정규식 특수문자가 포함되어 있는지 체크
    //정수 부분에만 콤마 달기
    strArr[0] = strArr[0].replace(regx, "$1,$2"); //콤마추가하기
  }
  if (bExists > -1) {
    //. 소수점 문자열이 발견되지 않을 경우 -1 반환
    data = strArr[0] + "." + strArr[1];
  } else {
    //정수만 있을경우 //소수점 문자열 존재하면 양수 반환
    data = strArr[0];
  }
  return data; //문자열 반환
}

//콤마 풀기
function uncomma(str) {
  str = "" + str.replace(/,/gi, ""); // 콤마 제거
  str = str.replace(/(^\s*)|(\s*$)/g, ""); // trim()공백,문자열 제거
  return new Number(str); //문자열을 숫자로 반환
}
//input box 콤마달기
function inputNumberFormat(obj) {
  obj.value = comma(obj.value);
}
//input box 콤마풀기 호출
function uncommaCall() {
  let inputValue = document.getElementById("input1");
  inputValue.value = uncomma(inputValue.value);
}

function limitText(max, id) {
  var el = document.getElementById(id);
  if (!el) return;
  var count = el.value.length;
  if (count > max) {
    alert("메시지는 공백 포함 " + max + "글자 까지만 작성하실 수 있습니다.");
    var strlen = el.value.length - 1;
    var conVal = el.value;
    var inputVal = conVal.substring(0, strlen);
    count = strlen;
    document.querySelectorAll("." + id).forEach(function (node) {
      node.textContent = strlen;
    });
    el.value = inputVal;
  }
  document.querySelectorAll("." + id).forEach(function (node) {
    node.textContent = count;
  });
}

//======== 아이디 중복 체크 =============================
function idConfirmReset() {
  var el = document.getElementById("idConfirm");
  if (el) el.value = "2";
}

function idCheck() {
  var memberID = document.getElementById("memberID");
  let m_id = memberID ? memberID.value : "";
  m_id = String(m_id);
  let pattern =
    /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i; //이메일형태
  if (pattern.test(m_id) == false) {
    var idConfirm = document.getElementById("idConfirm");
    if (idConfirm) idConfirm.value = "2";
    alert("이메일 주소를 올바르게 입력해 주세요.");
    return;
  }

  /*
	if (!chkRegPattern("id",m_id)){
		var idConfirmEl = document.getElementById("idConfirm");
		if (idConfirmEl) idConfirmEl.value = "2";
		alert('아이디는 4~16자리의 영문, 숫자와 특수기호 _만 사용하실 수 있습니다.');
		return;
	}
	*/

  var controller = typeof AbortController !== "undefined" ? new AbortController() : null;
  var timeoutId = setTimeout(function () {
    if (controller) controller.abort();
  }, 30000);

  fetch("/member/idChk.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
    },
    body: "m_id=" + encodeURIComponent(m_id),
    cache: "no-store",
    signal: controller ? controller.signal : undefined,
  })
    .then(function (res) {
      return res.json();
    })
    .then(function (response) {
      var idConfirm = document.getElementById("idConfirm");
      if (response["result"] == "N") {
        if (idConfirm) idConfirm.value = "2";
        alert("해당 아이디는 사용하실 수 없습니다.");
        return;
      } else if (response["result"] == "Y") {
        if (idConfirm) idConfirm.value = "1";
        alert("사용 가능한 아이디 입니다.");
        return;
      }
    })
    .catch(function () {
      //통신 에러 발생시 처리
    })
    .finally(function () {
      clearTimeout(timeoutId);
    });
}
//======== 아이디 중복 체크 끝=============================

//연락처 자동 하이픈처리
const autoHyphen = (target) => {
  target.value = target.value
    .replace(/[^0-9]/g, "")
    .replace(/^(\d{2,3})(\d{3,4})(\d{4})$/, `$1-$2-$3`);
};

document.addEventListener("DOMContentLoaded", function () {
  /*============== 관리자 메뉴 =============*/
  var adminMobileBreakPoint = 1024;
  var adminMenuList = document.querySelectorAll(".admin-menu-depth1>ul>li");
  var mobileMenuToggle = document.querySelector(".mobile-gnb-toggle");

  function isMobileAdminMenu() {
    return window.innerWidth <= adminMobileBreakPoint;
  }

  function closeMobileAdminMenu() {
    document.body.classList.remove("admin-mobile-open");
    if (mobileMenuToggle) {
      mobileMenuToggle.setAttribute("aria-expanded", "false");
    }
    document.querySelectorAll(".admin-menu-depth2").forEach(function (el) {
      el.style.display = "none";
    });
  }

  function setupAdminListMobileCards() {
    var isMobile = isMobileAdminMenu();
    var contents = document.getElementById("contents");
    var listTables = [];
    // form 안쪽 중첩 테이블도 포함 — 사업건 등 검색폼+목록 구조 대응
    if (contents) {
      contents
        .querySelectorAll(
          ".contents table.admin-menu-table, .contents table.adminMenuTable",
        )
        .forEach(function (table) {
          if (table.querySelector("tr.bgcol1, tr.bold.col1")) {
            listTables.push(table);
          }
        });
    }
    var hasActiveCard = false;

    listTables.forEach(function (table) {
      var headerRow = table.querySelector("tr.bgcol1, tr.bold.col1");
      var actionBox = null;
      var prev = table.previousElementSibling;
      while (prev) {
        if (prev.classList && prev.classList.contains("mobile-card-actions")) {
          actionBox = prev;
          break;
        }
        prev = prev.previousElementSibling;
      }
      var labels = [];
      var listRows = Array.prototype.filter.call(table.querySelectorAll("tr"), function (row) {
        return row.className && row.className.indexOf("list") !== -1;
      });
      var emptyRows = Array.prototype.filter.call(table.querySelectorAll("tr"), function (row) {
        if (row === headerRow || (row.className && row.className.indexOf("list") !== -1)) {
          return false;
        }
        if (row.querySelector("td.line1, td.line2, td.line3")) {
          return false;
        }
        var tds = Array.prototype.slice.call(row.children).filter(function (c) {
          return c.tagName === "TD";
        });
        return (
          tds.length > 0 &&
          tds.length <= 2 &&
          tds.some(function (td) {
            return td.hasAttribute("colspan");
          })
        );
      });

      if (headerRow) {
        Array.prototype.forEach.call(headerRow.children, function (cell) {
          if (cell.tagName === "TH" || cell.tagName === "TD") {
            labels.push(cell.textContent.trim().replace(/\s+/g, " "));
          }
        });
      }

      table.classList.remove("mobile-card-table");
      table.querySelectorAll("tr.mobile-card-row").forEach(function (row) {
        row.classList.remove("mobile-card-row");
      });
      table.querySelectorAll("tr.mobile-card-empty").forEach(function (row) {
        row.classList.remove("mobile-card-empty");
      });
      table.querySelectorAll('tr[class*="list"] td').forEach(function (td) {
        td.removeAttribute("data-label");
      });
      table.querySelectorAll("tr.mobile-card-empty td").forEach(function (td) {
        td.removeAttribute("data-label");
      });
      var prevCard = table.previousElementSibling;
      while (prevCard) {
        if (prevCard.classList && prevCard.classList.contains("mobile-card-list")) {
          prevCard.remove();
          break;
        }
        prevCard = prevCard.previousElementSibling;
      }
      var nextCard = table.nextElementSibling;
      while (nextCard) {
        if (nextCard.classList && nextCard.classList.contains("mobile-card-list")) {
          nextCard.remove();
          break;
        }
        nextCard = nextCard.nextElementSibling;
      }
      table.classList.remove("mobile-card-source-hidden");
      var nextBr = table.nextElementSibling;
      while (nextBr) {
        if (nextBr.tagName === "BR") {
          nextBr.classList.remove("mobile-card-source-hidden");
          break;
        }
        nextBr = nextBr.nextElementSibling;
      }

      if (!isMobile) {
        if (actionBox) {
          actionBox.remove();
        }
        return;
      }

      // 등록 버튼 — 마크업은 red_btn(underscore), 일부는 red-btn
      var registerButtons = headerRow
        ? headerRow.querySelectorAll(".red-btn, .red_btn")
        : [];
      if (registerButtons.length > 0) {
        if (!actionBox) {
          actionBox = document.createElement("div");
          actionBox.className = "mobile-card-actions";
          table.parentNode.insertBefore(actionBox, table);
        }
        actionBox.innerHTML = "";
        registerButtons.forEach(function (btn) {
          var parentA = btn.parentElement;
          var copyTarget =
            parentA && parentA.tagName === "A" ? parentA : btn;
          actionBox.appendChild(copyTarget.cloneNode(true));
        });
      } else if (actionBox) {
        actionBox.remove();
        actionBox = null;
      }

      var cardList = document.createElement("div");
      cardList.className = "mobile-card-list";
      listRows.forEach(function (row) {
        var cells = Array.prototype.filter.call(row.children, function (c) {
          return c.tagName === "TD";
        });

        if (!cells.length) {
          return;
        }

        var card = document.createElement("div");
        card.className = "mobile-card-item";
        cells.forEach(function (cell, index) {
          var label = labels[index] || "";
          var field = document.createElement("div");
          field.className = "mobile-card-field";
          var labelEl = document.createElement("div");
          labelEl.className = "mobile-card-label";
          labelEl.textContent = label;
          var valueEl = document.createElement("div");
          valueEl.className = "mobile-card-value";
          valueEl.innerHTML = cell.innerHTML;
          field.appendChild(labelEl);
          field.appendChild(valueEl);
          card.appendChild(field);
        });
        cardList.appendChild(card);
      });

      if (listRows.length === 0 && emptyRows.length > 0) {
        emptyRows.forEach(function (row) {
          var txt = row.textContent.trim().replace(/\s+/g, " ");
          if (!txt) {
            return;
          }
          var emptyCard = document.createElement("div");
          emptyCard.className = "mobile-card-item mobile-card-empty-item";
          var emptyText = document.createElement("div");
          emptyText.className = "mobile-card-empty-text";
          emptyText.textContent = txt;
          emptyCard.appendChild(emptyText);
          cardList.appendChild(emptyCard);
        });
      }

      if (cardList.children.length > 0) {
        table.classList.add("mobile-card-source-hidden");
        var brAfter = table.nextElementSibling;
        while (brAfter) {
          if (brAfter.tagName === "BR") {
            brAfter.classList.add("mobile-card-source-hidden");
            break;
          }
          brAfter = brAfter.nextElementSibling;
        }
        if (actionBox) {
          actionBox.insertAdjacentElement("afterend", cardList);
        } else {
          table.parentNode.insertBefore(cardList, table);
        }
        hasActiveCard = true;
      } else if (actionBox) {
        // 카드 미생성 시 테이블 헤더 등록 버튼과 중복되지 않도록 제거
        actionBox.remove();
        actionBox = null;
      }
    });

    document.body.classList.toggle(
      "admin-mobile-card-active",
      isMobile && hasActiveCard,
    );
  }

  adminMenuList.forEach(function (li) {
    li.addEventListener("mouseenter", function () {
      if (isMobileAdminMenu()) {
        return;
      }
      document.querySelectorAll(".admin-menu-depth2").forEach(function (el) {
        el.style.display = "none";
      });
      var submenu = li.querySelector(".admin-menu-depth2");
      if (submenu) {
        slideDown(submenu, 500, "cubic-bezier(0.165, 0.84, 0.44, 1)");
      }
    });
  });

  var adminMenuDepth1 = document.querySelector(".admin-menu-depth1");
  if (adminMenuDepth1) {
    adminMenuDepth1.addEventListener("mouseleave", function () {
      if (isMobileAdminMenu()) {
        return;
      }
      document.querySelectorAll(".admin-menu-depth2").forEach(function (el) {
        el.style.display = "none";
      });
    });
  }

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", function () {
      if (!isMobileAdminMenu()) {
        return;
      }
      var bg = document.getElementById("bg");
      if (document.body.classList.contains("admin-mobile-open")) {
        closeMobileAdminMenu();
        if (bg) bg.style.display = "none";
      } else {
        document.body.classList.add("admin-mobile-open");
        mobileMenuToggle.setAttribute("aria-expanded", "true");
        if (bg) {
          bg.style.display = "";
          bg.style.opacity = "";
          bg.style.removeProperty("transition");
        }
      }
    });
  }

  document.querySelectorAll(".admin-menu-depth1>ul>li>a").forEach(function (anchor) {
    anchor.addEventListener("click", function (e) {
      if (!isMobileAdminMenu()) {
        return;
      }
      var parent = anchor.parentElement;
      var submenu = parent ? parent.querySelector(".admin-menu-depth2") : null;
      var bg = document.getElementById("bg");
      if (!submenu) {
        closeMobileAdminMenu();
        if (bg) bg.style.display = "none";
        return;
      }

      e.preventDefault();
      var isVisible =
        submenu.style.display !== "none" &&
        window.getComputedStyle(submenu).display !== "none";
      if (isVisible) {
        slideUp(submenu, 200);
        if (parent) parent.classList.remove("mobile-open");
      } else {
        // 대상 메뉴까지 slideUp하면 transitionend가 직후 slideDown을 다시 닫음
        document.querySelectorAll(".admin-menu-depth2").forEach(function (el) {
          if (el !== submenu) {
            slideUp(el, 200);
          }
        });
        document.querySelectorAll(".admin-menu-depth1>ul>li").forEach(function (li) {
          li.classList.remove("mobile-open");
        });
        slideDown(submenu, 200);
        if (parent) parent.classList.add("mobile-open");
      }
    });
  });

  window.addEventListener("resize", function () {
    if (!isMobileAdminMenu()) {
      closeMobileAdminMenu();
      var bg = document.getElementById("bg");
      if (bg) bg.style.display = "none";
    }
    setupAdminListMobileCards();
  });

  setupAdminListMobileCards();
  /*============== 관리자 메뉴 끝 =============*/

  //레이어팝업
  document.addEventListener("click", function (e) {
    var closeTarget = e.target.closest('#bg, [id*="popup"] .close');
    if (!closeTarget) return;

    closeMobileAdminMenu();
    var bg = document.getElementById("bg");
    if (bg) fadeOut(bg);

    document.querySelectorAll('[id*="popup"]').forEach(function (el) {
      el.style.display = "none";
    });

    //팝업 안 내용이 삭제되어야할때 (수료증,입금확인서)
    var popup2 = document.querySelector("#popup.popup2");
    if (popup2) popup2.innerHTML = "";
  });

  document.querySelectorAll(".emailChange").forEach(function (select) {
    select.addEventListener("change", function () {
      var email = select.value;
      var parent = select.parentElement;
      if (!parent) return;
      var email2 = parent.querySelector(".email2");
      if (!email2) return;

      if (email == "") {
        email2.value = "";
        email2.focus();
      } else {
        email2.value = email;
      }
    });
  });

  //자동등록방지코드
  document.querySelectorAll(".sec_code").forEach(function (el) {
    el.addEventListener("click", function () {
      fetch("/include/plugin/kcaptcha/index.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: "data=12",
      })
        .then(function (res) {
          return res.text();
        })
        .then(function (data) {
          if (data) {
            document.querySelectorAll(".sec_code img").forEach(function (img) {
              img.setAttribute(
                "src",
                "/include/plugin/kcaptcha/?" + new Date().getTime(),
              );
            });
          }
        });
    });
  });

  //========================= 휴대폰 인증 ================================
  var smsSend = document.getElementById("sms_send");
  if (smsSend) {
    smsSend.addEventListener("click", function () {
      //인증번호 발송
      var hp1El = document.getElementById("hp1");
      var hp2El = document.getElementById("hp2");
      var hp3El = document.getElementById("hp3");
      if (
        !hp1El ||
        !hp2El ||
        !hp3El ||
        hp1El.value == "" ||
        hp2El.value == "" ||
        hp3El.value == ""
      ) {
        alert("휴대폰 번호를 입력해 주세요.");
        if (hp1El) hp1El.focus();
        return;
      }

      var hp1 = hp1El.value;
      var hp2 = hp2El.value;
      var hp3 = hp3El.value;

      var smsCertify = document.getElementById("sms_certify");
      if (smsCertify) smsCertify.value = "2";

      var controller =
        typeof AbortController !== "undefined" ? new AbortController() : null;
      var timeoutId = setTimeout(function () {
        if (controller) controller.abort();
      }, 30000);

      fetch("/member/sms_send.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body:
          "hp1=" +
          encodeURIComponent(hp1) +
          "&hp2=" +
          encodeURIComponent(hp2) +
          "&hp3=" +
          encodeURIComponent(hp3),
        cache: "no-store",
        signal: controller ? controller.signal : undefined,
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (response) {
          if (response["result"] == "N") {
            alert(
              "인증번호 발송에 실패 했습니다. 휴대폰 번호를 다시 확인해 주세요.",
            );
            console.log(response["error"]);
            return;
          } else if (response["result"] == "Y") {
            alert("입력하신 번호로 인증번호를 발송했습니다.");
            var codeEl = document.getElementById("sms_certify_code");
            if (codeEl) codeEl.removeAttribute("disabled");
            return;
          } else if (response["result"] == "D") {
            alert("입력하신 번호는 이미 가입되어있습니다.");
            return;
          }
        })
        .catch(function () {
          //통신 에러 발생시 처리
        })
        .finally(function () {
          clearTimeout(timeoutId);
        });
    });
  }

  var smsCertifyChk = document.getElementById("sms_certify_chk");
  if (smsCertifyChk) {
    smsCertifyChk.addEventListener("click", function () {
      //인증번호 검증
      var codeEl = document.getElementById("sms_certify_code");
      if (!codeEl || codeEl.value == "") {
        alert("인증번호를 입력해 주세요.");
        if (codeEl) codeEl.focus();
        return;
      }

      var smsCode = codeEl.value;

      var controller =
        typeof AbortController !== "undefined" ? new AbortController() : null;
      var timeoutId = setTimeout(function () {
        if (controller) controller.abort();
      }, 30000);

      fetch("/member/sms_chk.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: "smsCode=" + encodeURIComponent(smsCode),
        cache: "no-store",
        signal: controller ? controller.signal : undefined,
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (response) {
          var smsCertify = document.getElementById("sms_certify");
          if (response["result"] == "N") {
            if (smsCertify) smsCertify.value = "2";
            alert("인증번호가 일치하지 않습니다. 다시 시도해 주세요.");
            codeEl.focus();
            return;
          } else if (response["result"] == "Y") {
            if (smsCertify) smsCertify.value = "1";
            alert("인증되었습니다.");
            return;
          }
        })
        .catch(function () {
          //통신 에러 발생시 처리
        })
        .finally(function () {
          clearTimeout(timeoutId);
        });
    });
  }
  //================= 휴대폰 인증 끝 =========================================

  document.addEventListener("click", function (e) {
    var checkAll = e.target.closest("#checkAll");
    if (!checkAll) return;

    //전체 체크 or 해제
    var checked = checkAll.checked;
    document.querySelectorAll(".checkList").forEach(function (el) {
      el.checked = checked;
    });
  });

  //연락처 정규식변환
  document.addEventListener("keyup", function (e) {
    var phone = e.target.closest(".phoneNumber");
    if (!phone) return;

    phone.value = phone.value
      .replace(/[^0-9]/g, "")
      .replace(
        /(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,
        "$1-$2-$3",
      )
      .replace("--", "-");
  });

  //=========== 추가 컨텐츠 생성 ==========================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".addContent");
    if (!btn) return;

    const maxList = btn.dataset.max;
    const addPosition = btn.dataset.position;
    var grandParent = btn.parentElement && btn.parentElement.parentElement;
    if (!grandParent) return;

    if (grandParent.querySelectorAll(".add-content-list").length >= maxList) {
      alert(maxList + "개 까지만 가능합니다.");
      return;
    }

    var td = btn.closest("td");
    if (!td) return;
    var nextTd = td.nextElementSibling;
    while (nextTd && nextTd.tagName !== "TD") {
      nextTd = nextTd.nextElementSibling;
    }
    if (!nextTd) return;

    if (addPosition == "prepend") {
      //선택 요소 앞에 추가
      var lists = nextTd.querySelectorAll(".add-content-list");
      var lastList = lists[lists.length - 1];
      let copyContent = lastList ? lastList.innerHTML : "";
      nextTd.insertAdjacentHTML(
        "afterbegin",
        '<div class="add-content-list">' +
          copyContent +
          '<div class="content-delete"><button type="button"class="add-content-del gray-icon-btn">-</button></div></div>',
      );
      var firstList = nextTd.querySelector(".add-content-list:first-child");
      if (firstList) {
        firstList.querySelectorAll("input,textarea").forEach(function (input) {
          if (input.type === "checkbox") {
            input.checked = false;
          } else {
            input.value = "";
          }
        });
      }
    } else {
      //선택 요소 뒤에 추가
      var firstAddList = nextTd.querySelector(".add-content-list");
      let copyContent = firstAddList ? firstAddList.innerHTML : "";
      nextTd.insertAdjacentHTML(
        "beforeend",
        '<div class="add-content-list">' +
          copyContent +
          '<div class="content-delete"><button type="button"class="add-content-del gray-icon-btn">-</button></div></div>',
      );
      //복사된 요소 초기화
      var lastChild = nextTd.querySelector(".add-content-list:last-child");
      if (lastChild) {
        lastChild.querySelectorAll("input,textarea").forEach(function (input) {
          if (input.type === "checkbox") {
            input.checked = false;
          } else {
            input.value = "";
          }
        });
      }
    }
  });
  //=========== 추가 컨텐츠 생성 끝 ==========================

  //=========== 추가 멀티 컨텐츠 생성 ==========================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".add-multi-content");
    if (!btn) return;

    const maxList = btn.dataset.max;
    var grandParent = btn.parentElement && btn.parentElement.parentElement;
    if (!grandParent) return;
    const ea = grandParent.querySelectorAll(".add-content-list").length;

    if (ea >= maxList) {
      alert(maxList + "개 까지만 가능합니다.");
      return;
    }

    var td = btn.closest("td");
    if (!td) return;
    var nextTd = td.nextElementSibling;
    while (nextTd && nextTd.tagName !== "TD") {
      nextTd = nextTd.nextElementSibling;
    }
    if (!nextTd) return;

    var firstAddList = nextTd.querySelector(".add-content-list");
    var copyContent = firstAddList ? firstAddList.innerHTML : "";
    nextTd.insertAdjacentHTML(
      "beforeend",
      '<div class="add-content-list">' +
        copyContent +
        '<div class="content-delete"><button type="button"class="add-content-del gray-icon-btn">-</button></div></div>',
    );
    var lastChild = nextTd.querySelector(".add-content-list:last-child");
    if (lastChild) {
      lastChild.querySelectorAll("input").forEach(function (input) {
        let inputName = input.dataset.name;
        if (inputName) {
          input.setAttribute("name", inputName + ea + "[]");
        }
        input.value = "";
      });
    }
  });
  //=========== 추가 컨텐츠 생성 끝 ==========================

  //========== 추가 컨텐츠 삭제 =================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".add-content-del");
    if (!btn) return;

    if (window.confirm("삭제하시겠습니까?")) {
      var list = btn.closest(".add-content-list");
      if (list) list.remove();
      window.dispatchEvent(new Event("resize"));
    }
  });
  //========== 추가 컨텐츠 삭제 끝=================

  /*======================= 테이블 생성 ================================*/
  //=========== 테이블 열 생성 ==========================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".addTr");
    if (!btn) return;

    const maxList = btn.dataset.max;
    var grandParent = btn.parentElement && btn.parentElement.parentElement;
    if (!grandParent) return;

    if (grandParent.querySelectorAll(".addTrList").length >= maxList) {
      alert(maxList + "개 까지만 가능합니다.");
      return;
    }

    var td = btn.closest("td");
    if (!td) return;
    var nextTd = td.nextElementSibling;
    while (nextTd && nextTd.tagName !== "TD") {
      nextTd = nextTd.nextElementSibling;
    }
    if (!nextTd) return;

    var addTrLists = nextTd.querySelectorAll(".addTrList");
    var eq1 = addTrLists[1];
    let copyContent = eq1 ? eq1.innerHTML : "";
    nextTd.insertAdjacentHTML(
      "beforeend",
      '<ul class="addTrList"><div class="content-delete"><button type="button"class="addTrDel gray-icon-btn">-</button></div>' +
        copyContent +
        "</ul>",
    );

    let trEa = grandParent.querySelectorAll(".addTrList").length - 1;

    var lastList = nextTd.querySelector(".addTrList:last-child");
    if (lastList) {
      lastList.querySelectorAll("li input").forEach(function (input) {
        input.value = "";
        input.setAttribute("name", "tableTd" + trEa + "[]");
      });
    }
  });

  //========== 테이블 열 삭제 =================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".addTrDel");
    if (!btn) return;

    if (window.confirm("해당 열을 삭제하시겠습니까?")) {
      var list = btn.closest(".addTrList");
      if (list) list.remove();

      document.querySelectorAll(".addTrList").forEach(function (el, key) {
        el.querySelectorAll(".tableTd").forEach(function (input) {
          input.setAttribute("name", "tableTd" + key + "[]");
        });
      });
      window.dispatchEvent(new Event("resize"));
    }
  });
  //========== 테이블 열 삭제 끝=================
  //=========== 테이블 열 생성 끝 ==========================

  //=========== 테이블 행 생성 ==========================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".addTd");
    if (!btn) return;

    const maxList = btn.dataset.max;
    var grandParent = btn.parentElement && btn.parentElement.parentElement;
    if (!grandParent) return;

    var firstTrList = grandParent.querySelector(".addTrList");
    var addTdCount = firstTrList
      ? firstTrList.querySelectorAll(".addTd").length
      : 0;
    if (addTdCount >= maxList) {
      alert(maxList + "개 까지만 가능합니다.");
      return;
    }

    var td = btn.closest("td");
    if (!td) return;
    var nextTd = td.nextElementSibling;
    while (nextTd && nextTd.tagName !== "TD") {
      nextTd = nextTd.nextElementSibling;
    }
    if (!nextTd) return;

    let deleteButton = ""; //삭제버튼
    let addContent = ""; //input box

    nextTd.querySelectorAll(".addTrList").forEach(function (trList, key) {
      if (key == 0) {
        deleteButton =
          '<div><button type="button"class="addTdDel gray-icon-btn">-</button></div>'; //삭제버튼
      } else {
        deleteButton = "";
      }

      addContent =
        '<input type="text" name="tableTd' +
        key +
        '[]" class="input-text tableTd">';

      trList.insertAdjacentHTML(
        "beforeend",
        '<li class="addTd">' + deleteButton + addContent + "</li>",
      );
    });

    /*
				// last-child addTrList input 초기화 (미사용)
				*/
  });

  //========== 테이블 행 삭제 =================
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".addTdDel");
    if (!btn) return;

    var li = btn.parentElement && btn.parentElement.parentElement;
    let fieldNumber = li ? Array.prototype.indexOf.call(li.parentElement.children, li) - 1 : -1;
    if (window.confirm("해당 행을 삭제하시겠습니까?")) {
      document.querySelectorAll(".addTrList").forEach(function (trList) {
        var addTds = trList.querySelectorAll(".addTd");
        if (addTds[fieldNumber]) {
          addTds[fieldNumber].remove();
        }
      });
      window.dispatchEvent(new Event("resize"));
    }
  });
  //========== 테이블 행 삭제 끝=================
  //=========== 테이블 행 생성 끝 ==========================

  /*======================= 테이블 생성 끝 ===============================*/
});
