// dateUtils — 목록 UTC 날짜를 로컬 타임존으로 변환 (moment.js 연동)
class dateUtils {

    constructor(element, dateType = "date", findElement = "div.date", split = "board") {
        // Element | NodeList | Array 모두 허용
        if (element instanceof Element) {
            this.element = [element];
        } else if (element && typeof element.length === "number") {
            this.element = Array.from(element);
        } else {
            this.element = [];
        }
        this.findElement = findElement;
        this.split = split;
        this.dateType = dateType;
    }

    // UTC 날짜 텍스트 추출
    getUtcDate(item) {
        var splitObj = {
            board_text: "일",
            board_number: 1
        };
        var found = item.querySelectorAll(this.findElement);
        if (this.split % 1 === 0 || this.split === 0) {
            return found[this.split] ? found[this.split].textContent : "";
        }
        var text = found[0] ? found[0].textContent : "";
        return text.split(splitObj[this.split + "_text"])[splitObj[this.split + "_number"]];
    }

    // UTC → 사용자 로컬 시각 포맷
    utcToLocaleDate(utcDate) {
        var dateFormat = {
            date_time: "YYYY-MM-DD HH:mm:ss",
            date: "YYYY-MM-DD"
        };
        var userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        var localDateTime = moment.utc(utcDate).tz(userTimezone).format(dateFormat[this.dateType]);

        return localDateTime;
    }

    // 목록 항목별 날짜 텍스트 교체
    utcDateToLocalDate() {
        var self = this;
        var findEl = this.findElement;

        this.element.forEach(function(item) {
            var utcDate = self.getUtcDate(item);
            var localeDate = self.utcToLocaleDate(utcDate);
            var found = item.querySelectorAll(findEl);

            if (self.split % 1 === 0 || self.split === 0) {
                if (found[self.split]) {
                    found[self.split].textContent = localeDate;
                }
            } else {
                found.forEach(function(el) {
                    el.textContent = localeDate;
                });
            }
        });
    }
}
