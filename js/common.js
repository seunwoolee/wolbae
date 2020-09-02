//IE7~IE8 trim() 함수 만들어 주기
if (typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, '');
	}
}

// 쿠키 생성
function setCookie(cName, cValue, cDay){
	var expire = new Date();
	expire.setDate(expire.getDate() + cDay);
	cookies = cName + '=' + escape(cValue) + '; path=/ '; // 한글 깨짐을 막기위해 escape(cValue)를 합니다.
	if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
	document.cookie = cookies;
}

// 쿠키 가져오기
function getCookie(cName) {
  cName = cName + '=';
  var cookieData = document.cookie;
  var start = cookieData.indexOf(cName);
  var cValue = '';
  if(start != -1){
	   start += cName.length;
	   var end = cookieData.indexOf(';', start);
	   if(end == -1)end = cookieData.length;
	   cValue = cookieData.substring(start, end);
  }
  return unescape(cValue);
}

function getScrollTop() {

	var top1 = document.body.scrollTop;
	var top2 = $("html, body").scrollTop();

	if (top1 < 1) {
		return top2;
	} else {
		return top1;
	}
}

function numberFormat(num) {
	if (num < 10) {
		num = "0" + num;
	} else {
		num = num;
	}

	return num;
}

function getWeek(date) {
	var d = date.split("-");

	newDate = new Date(d[0], eval(d[1]) - 1, d[2]);

	var week = newDate.getDay();
	var weekStr = "일|월|화|수|목|금|토";
	var weeks = weekStr.split("|");

	return weeks[week];
}

Date.prototype.format = function(f) {

    if (!this.valueOf()) return " ";

    var weekName = ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"];
    var week = ["일", "월", "화", "수", "목", "금", "토"];
    var d = this;


    return f.replace(/(yyyy|yy|MM|dd|E|HH|hh|mm|ss|a\/p|AM\/PM)/gi, function($1) {

        switch ($1) {

            case "yyyy": return d.getFullYear();

            case "yy": return (d.getFullYear() % 1000).zf(2);

            case "MM": return (d.getMonth() + 1).zf(2);

            case "dd": return d.getDate().zf(2);

            case "E": return weekName[d.getDay()];
            
            case "e": return week[d.getDay()];

            case "HH": return d.getHours().zf(2);

            case "hh": return ((h = d.getHours() % 12) ? h : 12).zf(2);

            case "mm": return d.getMinutes().zf(2);

            case "ss": return d.getSeconds().zf(2);

            case "a/p": return d.getHours() < 12 ? "오전" : "오후";
            
            case "AM/PM": return d.getHours() < 12 ? "AM" : "PM";

            default: return $1;

        }

    });

};

String.prototype.string = function(len){var s = '', i = 0; while (i++ < len) { s += this; } return s;};
String.prototype.zf = function(len){return "0".string(len - this.length) + this;};
Number.prototype.zf = function(len){return this.toString().zf(len);};



Number.prototype.format = function(){
    if(this==0) return 0;
 
    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');
 
    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');
 
    return n;
};
 
// 문자열 타입에서 쓸 수 있도록 format() 함수 추가
String.prototype.format = function(){
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";
 
    return num.format();
};


//리스트 전체 체크 함수
function checkAll(obj, className) {
	var checked = $(obj).is(":checked");
	$("input[type=checkbox]." + className).prop("checked", checked);
}

//리스트 전체 체크 함수
function uncheckAll(obj, className) {
	var checked = $(obj).is(":checked");
	alert(checked);
	$("input[type=checkbox]." + className).prop("checked", checked);
}


//pupup
function popup() {

	var sw=800;    //띄울 창의 넓이
	var sh=600;    //띄울 창의 높이

	var url = arguments[0];
	var width = arguments[1];
	var height = arguments[2];

	if(width == "max") {
		width = screen.availWidth;
	}

	if(height == "max") {
		height = screen.availHeight;
	}

	var cw=screen.availWidth;     //화면 넓이
	var ch=screen.availHeight;    //화면 높이


	 if(width > 0) {
		var sw=width;
	 }
	 
	 if(width > cw) {
		var sw=cw;
	 }

	 if(height > 0) {
		var sh=height;
	 }
	 
	 if(height > ch) {
		var sh=ch;
	 }


	 var ml=(cw-sw)/2;        //가운데 띄우기위한 창의 x위치
	 var mt=(ch-sh)/2;         //가운데 띄우기위한 창의 y위치

	 var urlList = url.split("/");
	 var page = urlList[urlList.length-1].split(".");

	window.open(url, page[0], 'toolbar=no, status=no, directories=no, scrollbars=yes, location=no, resizable=yes, border=0, menubar=no, left=' + ml + ', top=' + mt + ', width=' + sw + ', height=' + sh);
}


/* 숫자 카운터 */
$.fn.numCounter = function(options) {
	var $obj	= this;
	var config	= $.extend({
		goal	:	""
	},options);

	var count = 0;
	var diff = 0;
	var target_count = parseInt(config.goal);
	var timer;


	var next_view = function () {
		diff = target_count - count;

		if(diff > 0) {
			count += Math.ceil(diff / 5);
		}

		$obj.innerHTML = count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		$obj.html($obj.innerHTML);

		if(count < target_count) {
		} else {
			clearInterval(timer);
		}
	}

	timer = setInterval(next_view, 10);
}