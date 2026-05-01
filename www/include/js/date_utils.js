// dateUtils 객체 정의
class dateUtils {

    constructor(element, dateType="date", findElement="div.date", split="board") {
        this.element = element;
        this.findElement = findElement;
        this.split = split;
        this.dateType = dateType;
    }

    // UTC 날짜를 가져와서 처리하는 메서드
    getUtcDate (item) {
        var splitObj = {
            'board_text':'일',
            'board_number':1
        };
        if(this.split % 1 === 0 || this.split === 0){
            return $(item).find(this.findElement).eq(this.split).text();
        }
        return $(item).find(this.findElement).text().split(splitObj[this.split+"_text"])[splitObj[this.split+"_number"]];
    }

    // UTC 날짜를 현지 시간으로 변환하여 반환하는 메서드
    utcToLocaleDate (utcDate) {
        var dateFormat = {
            'date_time' : "YYYY-MM-DD HH:mm:ss",
            'date': "YYYY-MM-DD"
        };
        // 사용자의 로컬 타임존 가져오기
        var userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        // Moment.js를 사용하여 시간 변환
        var localDateTime = moment.utc(utcDate).tz(userTimezone).format(dateFormat[this.dateType]);

        return localDateTime; 
    }


    // .list1 li 요소를 처리하는 메서드
    utcDateToLocalDate () {
        const self = this;
        let findEl = this.findElement;

        this.element.each(function(i, item) {
            //요소에서 텍스트를 가져와서 UTC 날짜 추출
            var utcDate = self.getUtcDate(item);

            // UTC 시간을 현지 시간으로 변환
            var localeDate = self.utcToLocaleDate(utcDate);

            // 변환된 시간으로 요소의 텍스트 설정
            if(self.split % 1 === 0 || self.split === 0){
                $(item).find(findEl).eq(self.split).text(localeDate);
            }else{
                $(item).find(findEl).text(localeDate);
            }
            
        });

    }
}
