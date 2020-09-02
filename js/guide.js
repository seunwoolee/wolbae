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

	$(".Driving dd").eq(0).html("00:00:00");
	$(".Driving dd").eq(1).html("0.0<em>km</em>");

	var next_view = function () {
		diff = target_count - count;

		if(diff > 0) {
			count += Math.ceil(diff / 5);
		}

		$obj.innerHTML = count.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		$(".Driving dd").eq(0).html("00:00:0"+$obj.innerHTML);

		if(count < target_count) {
		} else {
			$(".Driving dd").eq(1).html("1.0<em>km</em>");
			clearInterval(timer);
		}
	}

	timer = setInterval(next_view, 500);
}

var cntIndex;			// 현재 index 설정
var viewLength = 9;		// 총 step 개수
var motionSpeed = 100;


// 전체 모션
function mobileMotion(num){
	if(cntIndex == num){
		return false;
	};
	$("#wrap").attr("class", "thisSlide"+num);
	if(num == "0"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".popup").removeClass("is_act");
		$(".popup .pointer").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").addClass("is_act");
		$(".btnDrive1 .btnAnimate").addClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".menu").removeClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".addCar .carChoose").removeClass("is_act");
		$(".menu .menuAnimation1").removeClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$(".appMessage").removeClass("is_act");
		$(".numDrive").html("<img src='images/guide/img_direct1.png' alt='차량운행거리' />");
		$(".baseMobile").attr("src", "images/guide/bg_app_main1.png");
		$(".Driving").removeClass("toBlock");
		$(".numDrive").removeClass("is_none");
		$(".btnDrive2").removeClass("is_act");
		
		$("body").stop().animate({"opacity":"1"}, 3500, function(){
			$(".numDrive").addClass("is_none").html("<img src='images/guide/img_direct2.png' alt='차량운행거리' />");
			$(".Driving").addClass("toBlock").numCounter({goal:5});
			$(".btnDrive1").removeClass("is_block");
			$(".btnDrive2").addClass("is_block");
			$(".btnDrive2").addClass("is_act");
			
			$("body").stop().animate({"opacity":"1"}, 3000, function(){
				$(".btnDrive1").removeClass("is_act").addClass("is_block");
				$(".btnDrive1 .btnAnimate").removeClass("is_act");
				$(".btnDrive2").removeClass("is_block");
				$(".numDrive").removeClass("is_none");
				$(".Driving").removeClass("toBlock");
				$(".baseMobile").attr("src", "images/guide/bg_app_main2.png");
			});
		});
	} else if(num == "1"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".bluetooth").addClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".appMessage").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$("body").stop().animate({"opacity":"1"}, 500, function(){
		});
	} else if(num == "2"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".menu").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").removeClass("is_act");
		$(".btnDrive1 .btnAnimate").removeClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".menu .menuAnimation1").removeClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".addCar .carChoose").removeClass("is_act");
		$(".scrollMobile").addClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$(".appMessage").removeClass("is_act");
		$(".baseMobile").attr("src", "images/guide/bg_app_main2.png");
		$(".numDrive").html("<img src='images/guide/img_direct2.png' alt='차량운행거리' />");
		$(".numDrive").removeClass("is_none");
		$(".Driving").removeClass("toBlock");
		$("body").stop().animate({"opacity":"1"}, 500, function(){
			$(".appList").addClass("is_act");
		});
	} else if(num == "3"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").addClass("is_act");
		$(".appList").removeClass("is_act");
		$(".appMessage").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$("body").stop().animate({"opacity":"1"}, 500, function(){
		});
	} else if(num == "4"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".popup").removeClass("is_act");
		$(".popup .pointer").removeClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").removeClass("is_act");
		$(".btnDrive1 .btnAnimate").removeClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".addCar .carChoose").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".menu").addClass("is_act");
		$(".menu .menuAnimation1").addClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$(".appMessage").removeClass("is_act");
		$(".numDrive").removeClass("is_none");
		$(".Driving").removeClass("toBlock");
		$(".numDrive").html("<img src='images/guide/img_direct1.png' alt='차량운행거리' />");
		$(".slideMenu").attr("src", "images/guide/img_menu1.png");
		$("body").stop().animate({"opacity":"1"}, 3500, function(){
		});
	} else if(num == "5"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".popup").addClass("is_act");
		$(".appMessage").removeClass("is_act");
		$(".popup .pointer").addClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").removeClass("is_act");
		$(".btnDrive1 .btnAnimate").removeClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".menu").addClass("is_act");
		$(".addCar .carChoose").removeClass("is_act");
		$(".menu .menuAnimation1").removeClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$(".baseMobile").attr("src", "images/guide/bg_app_main2.png");
		$(".numDrive").html("<img src='images/guide/img_direct2.png' alt='차량운행거리' />");
		$(".numDrive").removeClass("is_none");
		$(".Driving").removeClass("toBlock");
		$("body").stop().animate({"opacity":"1"}, 3500, function(){
		});
	} else if(num == "6"){
		$(".logDown").addClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".menu").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".popup").removeClass("is_act");
		$(".appMessage").removeClass("is_act");
		$(".popup .pointer").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$("body").stop().animate({"opacity":"1"}, 500, function(){
			$(".logMessage").addClass("is_act");
		});
	} else if(num == "7"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".menu").removeClass("is_act");
		$(".popup").removeClass("is_act");
		$(".popup .pointer").removeClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").removeClass("is_act");
		$(".btnDrive1 .btnAnimate").removeClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".addCar .carChoose").removeClass("is_act");
		$(".menu .menuAnimation1").removeClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").removeClass("micro");
		$(".appMessage").addClass("is_act");
		$(".baseMobile").attr("src", "images/guide/bg_app_main2.png");
		$(".numDrive").html("<img src='images/guide/img_direct2.png' alt='차량운행거리' />");
		$(".numDrive").removeClass("is_none");
		$(".Driving").removeClass("toBlock");
		$("body").stop().animate({"opacity":"1"}, 3500, function(){
		});
	} else if(num == "8"){
		$(".logMessage").removeClass("is_act");
		$(".logDown").removeClass("is_act");
		$(".bluetooth").removeClass("is_act");
		$(".driveDetail").removeClass("is_act");
		$(".addCar").removeClass("is_act");
		$(".btnDrive1").addClass("is_block");
		$(".btnDrive1").removeClass("is_act");
		$(".btnDrive1 .btnAnimate").removeClass("is_act");
		$(".btnDrive2").removeClass("is_act");
		$(".btnDrive2").removeClass("is_block");
		$(".addCar .carChoose").removeClass("is_act");
		$(".menu .menuAnimation1").removeClass("is_act");
		$(".menu .menuAnimation2").removeClass("is_act");
		$(".scrollMobile").removeClass("is_act");
		$(".menuIcon .motion").removeClass("is_act");
		$(".appMessage").removeClass("is_act");
		$(".appList").removeClass("is_act");
		$(".mobileImg").addClass("micro");
		$(".baseMobile").attr("src", "images/guide/bg_app_main2.png");
		$(".numDrive").removeClass("is_none");
		$(".Driving").removeClass("toBlock");
		$(".numDrive").html("<img src='images/guide/img_direct2.png' alt='차량운행거리' />");
		$("body").stop().animate({"opacity":"1"}, 3500, function(){
		});
	};
	cntIndex = num;
	textAction(num);
}

// text Action
function textAction(num){
	var topText = [
		"'운행기록 시작'을 터치해서<br />차량운행일지를 기록하세요.",
		"별도의 조작없이<br />자동운행 기록도 가능합니다.",
		"기록된 운행기록은<br />리스트에서 확인이 가능합니다.",
		"리스트를 터치하면<br />상세 운행기록 확인이 가능합니다.",
		"국세청 정식 양식<br />운행기록 엑셀저장!",
		"운행기록 구간 선택 후<br />핸드폰에 저장 또는 공유가 가능합니다.",
		"국세청 운행일지<br />정식 양식, 완벽 대응!",
		"카택스는 블루투스 연동을 통한 자동운행<br />및 이동경로를 쉽게 확인 가능합니다.",
		"카택스로 비용절감 및 앞서가는<br />조직 운영을 경험하세요."
	];
	
	$(".txtVisual").stop().animate({"opacity":0}, motionSpeed, function(){
		$(this).html(topText[num]).stop().animate({"opacity":1}, 3*motionSpeed);
	});

	if(num == 8){
		$(".btn").removeClass("btnNext").addClass("btnLast");
	} else {
		$(".btn").removeClass("btnLast").addClass("btnNext");
	}

	$(".menuIcon span").html("박보검");
	$(".spot a").removeClass("on");
	$(".spot a").eq(cntIndex).addClass("on");
}

// 다음 모션
function nextView(){
	if(cntIndex+2 > viewLength) {
		if (confirm("가이드를 종료하시겠습니까?") == true){
			goWrite();
		} else {
			return;
		}
		return;
	} else {
		mobileMotion(cntIndex+1);
	}
}

// 이전 모션
function prevView(){
	if(cntIndex-1 < 0) {
		return;
	} else {
		mobileMotion(cntIndex-1);
	}
}

// 가이드 종료
var isMobile = {
	Android: function () { return navigator.userAgent.match(/Android/i);},
	BlackBerry: function () { return navigator.userAgent.match(/BlackBerry/i);},
	iOS: function () { return navigator.userAgent.match(/iPhone|iPad|iPod/i);},
	Opera: function () { return navigator.userAgent.match(/Opera Mini/i);},
	Windows: function () { return navigator.userAgent.match(/IEMobile/i);},
	any: function () { return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());}
};
var currentOS;
var mobile = (/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase()));

function goWrite(){
	if (mobile) {
		var userAgent = navigator.userAgent.toLowerCase();
		if (userAgent.search("android") > -1) currentOS = "android";
		else if ((userAgent.search("iphone") > -1) || (userAgent.search("ipod") > -1) || (userAgent.search("ipad") > -1)) currentOS = "ios";
		else currentOS = "else";
	}
	if(currentOS=="ios"){
		document.location.href = "sendMessage:endGuide";	
	} else {
		carTax.sendMessage("endGuide");
	}
}

$(document).ready(function(){
	$(".linkSize").on("click", function(){
		nextView();
	});
	
	// skip 버튼 눌렀을때
	$(".btnSkip").on("click", function(){
		if (confirm("가이드를 종료하시겠습니까?") == true){
			goWrite();
		} else {
			return;
		}
	});

	// 버튼 눌렀을 때
	$(".btn").on("click", function(){
		nextView();
	});

	// spot 버튼 눌렀을때
	$(".spot a").on("click", function(e){
		 var target = $(e.currentTarget);
		 var idx = target.index();
		 mobileMotion(idx);
	});
	
});