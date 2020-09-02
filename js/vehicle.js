

var apiKeyArr;
var apiKey				='';
var vehicleLocationId	='';	// 거점ID
var vehicleDate			='';	// 배송날짜
var vehicleTime			='';	// 배송날짜 오전,오후 값
var vehicleTimeFlag		='';	// 배송날짜 오전,오후 분할값

var allMaxLength=0;
var maxLength=0;

var keyIndex = 0;

var nIndexConvert	= 0;	// timedLoopCheckLatLonConvert 에서 사용
var tmpItem = '';			// timedLoopCheckLatLonConvert 에서 사용

var nIndexDistance	= 0;	// timedLoopSetDistanceListData 에서 사용


function init(apikey , id, date, time){
	apiKeyArr = apikey;
	apiKey = apiKeyArr[keyIndex];
	vehicleLocationId = id;
	vehicleDate = date;
	vehicleTime = time;
	
	if(vehicleTime=='오후'){
		vehicleTime = '1';
	} else {
		vehicleTime = '0';
	}
}
//***********************************************고객 정보 업데이트*******************************************************//

//배차 완료 유무 체크
function vehicleResultCheck(){
	//$("#modal").modal({ backdrop:false } );	// 백그라운드 클릭시 창 안닫히게.
	var deliveryDate = vehicleDate;
	var locationId = vehicleLocationId;
	var meridiemType = vehicleTime;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleAllocateStatusProc.ajax.php"
		,async : true	// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
				}
		,dataType : "json"
		,success : function(result){
			if(result.code=='Y'){
				if(confirm("이미 배차완료를 하셨습니다. \n재배차를 진행하면 기존데이터는 삭제됩니다. \n진행하시겠습니까?")){
					updateMutualDistance();
				} else{
					$("#loading").modal("hide");
					window.location.href="setCarListTest.html?deliveryDate="+deliveryDate+"&meridiemType="+meridiemType+"&locationId="+locationId;
					return;
				}
			} else {
			}
		}
		,complete : function(){}
	});
}

//배차 완료 유무 체크
function vehicleResultCheckSe(){

	//$("#modal").modal({ backdrop:false } );	// 백그라운드 클릭시 창 안닫히게.
	var deliveryDate	= vehicleDate;
	var locationId		= vehicleLocationId;
	var meridiemType	= vehicleTime;
	var meridiemFlag	= vehicleTimeFlag;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleAllocateStatusProcSe.ajax.php"
		,async : true	// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"
		,success : function(result){

			if(result.code=='Y'){

				var strMessage = '';
				if(meridiemType == '0'){
					strMessage += '오전';
				} else {
					strMessage += '오후';
				} 

				strMessage += " " + meridiemFlag + "번째 배차가 이미 완료되었습니다.\n재배차를 하시겠습니까?";

				//if(confirm("이미 배차완료를 하셨습니다. 재배차를 하시겠습니까?")){
				if(confirm(strMessage)){
					updateMutualDistanceSe();	// 
				} else{
					
					// 재배차가 아니면 그냥 빠져나오긔
					//alert('test');
					//$("#loading").modal("hide");
					//window.location.href="setCarListTest.html?deliveryDate="+deliveryDate+"&meridiemType="+meridiemType+"&locationId="+locationId;
					//return;
				}
			} else {
				//alert('error');
			}
		}
		,complete : function(){}

	});
}


//step02. 배차완료 테이블 N 업데이트
function updateMutualDistance(){

	$.ajaxSettings.traditional = true;
	$.ajax({
		 url: "ajax/vehicleAllocateResultDataProc.ajax.php"
		,data: {
					"deliveryDate":vehicleDate
					,"locationId":vehicleLocationId
					,"meridiemType":vehicleTime
					,"vehicleEndStatus":"N"			
				}
		,type: "post"
		,async : true
		,dataType : "json"
		,success: function( json ) {
		}
		,error: function( xhr, status ) { 
			alert("error updateMutualDistance()");
		}
		,complete: function( xhr, status ) { }
	});	
}

//step02. 배차완료 테이블에 배차완료 상태값을 'N'으로 처리, 'Y'이면 완료상태임
function updateMutualDistanceSe(){

	$.ajaxSettings.traditional = true;
	$.ajax({
		 url: "ajax/vehicleAllocateResultDataProcSe.ajax.php"
		,data: {
					"deliveryDate":vehicleDate
					,"locationId":vehicleLocationId
					,"meridiemType":vehicleTime
					,"meridiemFlag":vehicleTimeFlag
					,"vehicleEndStatus":"N"			
				}
		,type: "post"
		,async : true
		,dataType : "json"
		,success: function( json ) {
		}
		,error: function( xhr, status ) { 
			alert("error updateMutualDistanceSe()");
		}
		,complete: function( xhr, status ) { }
	});	
}


/*
// step01. ajax 외부 장보고 데이터 가져오기.
function ajaxGetJangbogoData(refErpUrl){

	var vehicleErpUrl = refErpUrl;

	if(confirm('고객정보를 업데이트 하시겠습니까?')){
			
		$("#text-step2").text("고객 정보 업데이트 중입니다.");
		var memetype = "";
		if(vehicleTime=='1'){
			memetype = "b";
		} else {
			memetype = "a";
		}
		
		$.ajax({
			type : "GET"
			//,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php"
			,url : vehicleErpUrl
			,async			: false		// 동기화처리
			,dataType		: "jsonp"
			,crossDomain	: true
			,jsonpCallback	: "callback"
			,data : {
						"jum":"6"			// 11:반야월, 6:월배, 배센 :10
						,"gbn":memetype
			}
			,success : function(data){

				var meridiemType	= vehicleTime;

				vehicleTimeFlag	= data['item'][0]['flag']; // 오전,오후 분할 플래그값 발생시점!!!
				//var tmpMeridiemType	= data['item'][0]['meridiemType'];

				// 배차완료채크
				vehicleResultCheckSe();
				
				//------- 경고창 사용하지 않을시 이부분 주석처리 ----------------//

				var strMeridiemType = '';
				var strMeridiemFlag = '';

				if(meridiemType == "1"){
					strMeridiemType = "오후";
				} else {
					strMeridiemType = "오전";
				}

				if(confirm(strMeridiemType + ' ' + vehicleTimeFlag + '번째 주문정보와 고객정보가 확인되었습니다.\n업데이트를 진행하시겠습니까?')){
					ajaxGuestOrderDataUpdate(data);
				} else {
					nowStep -= 1;	// 
					$("#loading").modal("hide");
				}
				//----------------------------------------------------------//

				//ajaxGuestOrderDataUpdate(data, meridiemFlag);

			}
			,complete : function(data){
				//alert(JSON.stringify(data));
			}	// complete Event 호출시 사용
			
		});
		
	} else {
		return false;
	}
}
*/

// step01. ajax 외부 장보고 데이터 가져오기.
function ajaxGetJangbogoData(stcode){


	if(confirm('고객정보를 업데이트 하시겠습니까?')){
			
		$("#text-step2").text("고객 정보 업데이트 중입니다.");
		var memetype = "";
		if(vehicleTime=='1'){
			memetype = "b";
		} else {
			memetype = "a";
		}
		
		$.ajax({
			type : "GET"
			//,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php"
			//,url : vehicleErpUrl
			,url : "ajax/baedal_orderlist.ajax.php"
			,async			: false		// 동기화처리
			,dataType		: "json"
			,crossDomain	: true
			//,jsonpCallback	: "callback"
			,data : {
						//"jum":"6"			// 11:반야월, 6:월배, 배센 :10
						//"jum":"0003"		// 0003:월배
						"jum":stcode
						,"gbn":memetype
			}
			,success : function(data){

				var meridiemType	= vehicleTime;

				vehicleTimeFlag	= data['item'][0]['flag']; // 오전,오후 분할 플래그값 발생시점!!!

				//alert(vehicleTimeFlag);
				//return false;

				//var tmpMeridiemType	= data['item'][0]['meridiemType'];

				// 배차완료채크
				vehicleResultCheckSe();
				
				//------- 경고창 사용하지 않을시 이부분 주석처리 ----------------//

				var strMeridiemType = '';
				var strMeridiemFlag = '';

				if(meridiemType == "1"){
					strMeridiemType = "오후";
				} else {
					strMeridiemType = "오전";
				}

				if(confirm(strMeridiemType + ' ' + vehicleTimeFlag + '번째 주문정보와 고객정보가 확인되었습니다.\n업데이트를 진행하시겠습니까?')){
					ajaxGuestOrderDataUpdate(data);
				} else {
					nowStep -= 1;	// 
					$("#loading").modal("hide");
				}
				//----------------------------------------------------------//

				//ajaxGuestOrderDataUpdate(data, meridiemFlag);

			}
			,complete : function(data){
				//alert(JSON.stringify(data));
			}	// complete Event 호출시 사용
			
		});
		
	} else {
		return false;
	}
}

//step02. ajax 고객배송정보 업데이트
function ajaxGuestOrderDataUpdate(jsonDataGuestOrder){

	jsonDataGuestOrder.deliveryDate		= vehicleDate;
	jsonDataGuestOrder.locationId		= vehicleLocationId;
	jsonDataGuestOrder.meridiemType		= vehicleTime;

	jsonDataGuestOrder.meridiemFlag		= vehicleTimeFlag;	// 오전,오후 분할 플래그 추가


	//alert(vehicleDate);


	//alert(jsonDataGuestOrder);
	//return;

	//alert(JSON.stringify(jsonDataGuestOrder));
	//return;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleGuestOrderDataProc.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(jsonDataGuestOrder)
		,contentType : "application/json"
		,success : function(result){

			//alert(result);
			ajaxGuestOrderDataGet();

		}
		,complete : function(){}
	});
}
//step03. ajax 고객배송정보 가져오긔.
function ajaxGuestOrderDataGet(){

	var deliveryDate	= vehicleDate;
	var locationId		= vehicleLocationId;
	var meridiemType	= vehicleTime;
	var meridiemFlag	= vehicleTimeFlag;		// 오전,오후 배차 분할플래그

	$.ajax({
		type : "POST"
		,url : "ajax/vehicleGuestOrderData.ajax.php"	
		,async : true		// 동기화처리
		//,data : {"deliveryDate":deliveryDate, "locationId":locationId, "meridiemType":meridiemType}
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){
			ajaxGuestInfoUpdate(data);
		}
		,complete : function(){}	// complete Event 호출시 사용
	});
}

//step04. ajax 고객정보 업데이트
function ajaxGuestInfoUpdate(vehicleGuestOrderData){

	vehicleGuestOrderData.deliveryDate	= vehicleDate;			// 배송날짜
	vehicleGuestOrderData.locationId	= vehicleLocationId;	// 거점ID
	vehicleGuestOrderData.meridiemType	= vehicleTime;			// 오전,오후
	vehicleGuestOrderData.meridiemFlag	= vehicleTimeFlag;		// 오전,오후 분할 플래그


	//alert(vehicleDate);
	//alert(vehicleLocationId);
	//alert(vehicleTime);
	//alert(vehicleTimeFlag);


	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleGuestInfoProc.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(vehicleGuestOrderData)
		,dataType : "text"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(result){

			//alert('success');

			//ajaxGuestInfoFailCheck(result["vehicleOrderData"]);

			$("#loading").modal("hide");
			nowStep++;
			changElm(parseInt(nowStep/2), parseInt(nowStep%2));

		}
		,complete : function(){
			//alert(result);
		}
		,error : function(request,status,error){

			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			//alert("code:"+request.status+"\n"+"error:"+error);

		}
	});
}

/*
//step05. 위 경도 주소 확인시 문제 발견
function ajaxGuestInfoFailCheck(vehicleOrderData){
	
	for(var i=0;i<vehicleOrderData.length;i++){
		var item = vehicleOrderData[i];
		checkLatLonConvert(item,i,vehicleOrderData.length);
	}

}
*/

//step05. 위 경도 주소 확인시 문제 발견
function ajaxGuestInfoFailCheck(vehicleOrderData){
	
	// TMap API 초당횟수제한때문에 수정함
	timedLoopCheckLatLonConvert(vehicleOrderData);

}

function timedLoopCheckLatLonConvert(refData){

	setTimeout(function () {
					tmpItem = refData[nIndexConvert];
					checkLatLonConvert(tmpItem, nIndexConvert, refData.length);
					nIndexConvert++;
					if(nIndexConvert < refData.length) {
						timedLoopCheckLatLonConvert(refData);
					}
				}, 100);
}

/*
//위도 경도 변환
function checkLatLonConvert(item,i,length){
	var strSerialize = '';
	
	strSerialize += "version=1&format=json";
	strSerialize += "&lat="+item['guestLat'];
	strSerialize += "&lon="+item['guestLon'];
	strSerialize += "&addressType="+"A00";
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";
	var result;
	$.ajax({
		type: "GET"
		,url: "https://apis.skplanetx.com/tmap/geo/reversegeocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			var city_do = data.addressInfo.city_do;
			if(city_do=='대구광역시' || city_do=='경상북도'){
				
			} else {
				ajaxGuestInfoFailUpdate(item['guestId'], item['guestJusoSubId'], item['isShop']);
			}
		}
		,error: function( xhr, status ) {
			if(xhr.status=='400'){
				ajaxGuestInfoFailUpdate(item['guestId'], item['guestJusoSubId'], item['isShop']);
			}
		}
		,complete : function(){
			if(i==length-1){
				$("#loading").modal("hide");
				nowStep++;
				changElm(parseInt(nowStep/2), parseInt(nowStep%2));
			}
		}
	});
}
*/

//위도 경도 변환 - TMAP API 이관처리사유로 해당함수 수정
function checkLatLonConvert(item, i, length){
	
	var strSerialize = '';
	
	strSerialize += "version=1&format=json";	// param version
	strSerialize += "&lat="+item['guestLat'];	// param Lat
	strSerialize += "&lon="+item['guestLon'];	// param Lon
	strSerialize += "&addressType="+"A00";		// param addressType
	strSerialize += "&appKey="+apiKey;			// param 
	strSerialize += "&coordType=WGS84GEO";
	var result;
	$.ajax({
		type: "GET"
		,url : "https://api2.sktelecom.com/tmap/geo/reversegeocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			var city_do = data.addressInfo.city_do;
			if(city_do=='대구광역시' || city_do=='경상북도'){
				
			} else {
				ajaxGuestInfoFailUpdate(item['guestId'], item['guestJusoSubId'], item['isShop']);
			}
		}
		,error: function( xhr, status ) {
			if(xhr.status=='400'){
				ajaxGuestInfoFailUpdate(item['guestId'], item['guestJusoSubId'], item['isShop']);
			}
		}
		,complete : function(){
			if(i==length-1){
				$("#loading").modal("hide");
				nowStep++;
				changElm(parseInt(nowStep/2), parseInt(nowStep%2));
			}
		}
	});

	//clearInterval(setIntervalHandle);
}

//step06. 위 경도 주소 확인시 문제 발견 체크 업데이트
function ajaxGuestInfoFailUpdate(guestId, guestJusoSubId, isShop){
	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/checkLatLonUpdate.ajax.php"
		,async : true	// 동기화처리
		,data : {	"deliveryDate":vehicleDate
					,"locationId":vehicleLocationId
					,"meridiemType":vehicleTime
					,"meridiemFlag":vehicleTimeFlag
					,"guestId":guestId
					,"guestJusoSubId":guestJusoSubId
					,"isShop":isShop
					,"isAddVehicle":"N"
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(result){}
		,complete : function(){}
	});
}
//***********************************************배송 거리 업데이트*******************************************************//
//step01. ajax 고객배송정보 배송완료 유무 확인
function GuestOrderDataProc(){

	if(confirm('배송지 좌표를 업데이트 하시겠습니까?')){
		ajaxGuestMutualDistanceDataGet();
	} else {
		return false;
	}		
}

//step02. 고객정보 배차거리 관련 가져오기
function ajaxGuestMutualDistanceDataGet(){
	var deliveryDate = vehicleDate;
	var locationId = vehicleLocationId;
	var meridiemType = vehicleTime;
	var meridiemFlag = vehicleTimeFlag;

	//alert(deliveryDate + " " + locationId + " " + meridiemType + " " + meridiemFlag);

	$.ajax({
		type : "POST"
		,url : "ajax/vehicleGuestMutualDistance.ajax.php"	// 현재 배송정보를 토대로 주소 중복을 제거합니다.
		,data : {
					 "deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,async : true		// 동기화처리
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){	
			compareJusoData(data);
		}
		,complete : function(){}	// complete Event 호출시 사용
	});
}

//step03. 금일 업데이트된 주소DB와 거리DB 비교
function compareJusoData(vehicleGuestInfoData){
	//vehicleGuestInfoData.deliveryDate		="2017-08-01";
	vehicleGuestInfoData.deliveryDate = vehicleDate;

	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleGuestMutualDistanceCompare.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(vehicleGuestInfoData)
		,dataType : "json"
		,success : function(data){

			/*
			$("#loading").modal("hide");
			nowStep++;
			changElm(parseInt(nowStep/2), parseInt(nowStep%2));
			return;
			*/
	
			if(data['vehicleGuestDistanceData']==null){
				$("#loading").modal("hide");
				nowStep++;
				changElm(parseInt(nowStep/2), parseInt(nowStep%2));
				return;
			}
			//alert(data['vehicleGuestDistanceData'].length);
			initDistanceListData(data['vehicleGuestDistanceData']);

			//$("#loading").modal("hide");	// 이놈은 삭제 해야됨
			
		}
		,complete : function(){}
	});
}

/*
// step04. 주소지점사이 거리값 구하기
function initDistanceListData(vehicleGuestDistanceData){

	if(allMaxLength==0){
		allMaxLength = parseInt(vehicleGuestDistanceData.length/5000)+1;
	}
	maxLength = allMaxLength - parseInt(vehicleGuestDistanceData.length/5000);
	var dateLength = vehicleGuestDistanceData.length;

	if(dateLength > 5000){
		dateLength = 5000;
	}

	for(i=0;i<dateLength;i++){
		setDistanceListData(vehicleGuestDistanceData[i], i, dateLength);
	}	
}
*/

// step04. 주소지점사이 거리값 구하기 - TMap API 초당호출횟수 제한에 때문에 수정합니다.
function initDistanceListData(vehicleGuestDistanceData){

	if(allMaxLength==0){
		allMaxLength = parseInt(vehicleGuestDistanceData.length/1000)+1;
	}
	maxLength = allMaxLength - parseInt(vehicleGuestDistanceData.length/1000);
	var dateLength = vehicleGuestDistanceData.length;

	//if(dateLength > 1000){
	//	dateLength = 1000;
	//}

	//if(dateLength > 2048){
	//	dateLength = 2048;
	//}

	/*
	for(i=0;i<dateLength;i++){
		setDistanceListData(vehicleGuestDistanceData[i], i, dateLength);
	}
	*/
	timedLoopSetDistanceListData(vehicleGuestDistanceData, dateLength);

}

function timedLoopSetDistanceListData(refData, refLength){

	setTimeout(function () {
					setDistanceListData(refData[nIndexDistance], nIndexDistance, refLength);
					nIndexDistance++;
					if(nIndexDistance < refData.length) {
						timedLoopSetDistanceListData(refData, refLength);
					}
				}, 100);
}

// 로딩 텍스트 변경
function setLoadingNum(i,len){

	$("#loadingDataNum").text(i+" / "+len+" ("+maxLength+"/"+allMaxLength+")");

	if(i==len-1){
		if(len==5000){
			ajaxGuestMutualDistanceDataGet();
		} else {
			$("#loadingDataNum").text("배송지 좌표정보 업데이트 완료하였습니다.");
			$("#loading").modal("hide");
			nowStep++;
			changElm(parseInt(nowStep/2), parseInt(nowStep%2));
		}
	}
}




/*
// step05. 주소지점사이 거리 데이터 업데이트(실제)
function setDistanceListData(vehicleGuestDistanceData, i, len){

	var guestLon = vehicleGuestDistanceData['guestLon'];
	var guestLat = vehicleGuestDistanceData['guestLat'];
	var deguestLon = vehicleGuestDistanceData['deguestLon'];
	var deguestLat = vehicleGuestDistanceData['deguestLat'];

	var guestId = vehicleGuestDistanceData['guestId'];
	var deguestId = vehicleGuestDistanceData['deguestId'];

	var guestJusoSubId = vehicleGuestDistanceData['guestJusoSubId'];
	var deguestJusoSubId = vehicleGuestDistanceData['deguestJusoSubId'];

	var guestDate = vehicleGuestDistanceData['guestDate'];
	var deguestDate = vehicleGuestDistanceData['deguestDate'];

	var guestIsShop = vehicleGuestDistanceData['guestIsShop'];
	var deguestIsShop = vehicleGuestDistanceData['deguestIsShop'];

	var updateDate = vehicleGuestDistanceData['updateDate'];

	var status = vehicleGuestDistanceData['status'];

	var strSerialize = '';
	var strTotalDistance;
	
	if(guestLon==deguestLon && guestLat==deguestLat){
		setEqualLocation(vehicleGuestDistanceData, i, len);
		return;
	}

	strSerialize += "version=1&format=json";
	strSerialize += "&startX="+guestLon;
	strSerialize += "&startY="+guestLat;
	//strSerialize += "&startName="+encodeURIComponent(varSeq);
	strSerialize += "&startName="+encodeURIComponent("출발지");
	strSerialize += "&endX="+deguestLon;
	strSerialize += "&endY="+deguestLat;
	strSerialize += "&endName="+encodeURIComponent("도착지");
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&searchOption=0";			// 
	strSerialize += "&reqCoordType=WGS84GEO";	// 
	strSerialize += "&directionOption=1";		//
	strSerialize += "&roadType=16";				// 

	var result;
	
	$.ajax({
		type: "GET"
		,url: "https://apis.skplanetx.com/tmap/routes"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){

			if(data!=null){
				var jsonData = JSON.stringify(data);
				var objFeatures = data['features'];
				//var jsonData = JSON.stringify(objFeatures[0]['properties']);
				//var jsonData = JSON.stringify(objFeatures);

				strTotalDistance = objFeatures[0]['properties']['totalDistance'];
				result = strTotalDistance;

				$.ajaxSettings.traditional = true;
				$.ajax({
					url: "ajax/vehicleGuestMutualDistanceProc.ajax.php"
					,data: {
								 "guestId":guestId
								, "deguestId":deguestId
								, "guestLon":guestLon
								, "guestLat":guestLat
								, "deguestLon":deguestLon
								, "deguestLat":deguestLat
								, "guestJusoSubId":guestJusoSubId
								, "deguestJusoSubId":deguestJusoSubId
								, "status":status
								, "guestDate":guestDate
								, "deguestDate":deguestDate
								, "updateDate":updateDate
								, "guestIsShop":guestIsShop
								, "deguestIsShop":deguestIsShop
								, "distance":result
								, "jsonData":jsonData
							}
					,type: "POST"
					,async : true
					,dataType : "json"
					,success: function( json ) {
					}
					,error: function( xhr, status ) {
						//alert(guestLon+","+guestLat+","+deguestLon+","+deguestLat+","+guestId+","+deguestId);
					}
					,complete: function( xhr, status ) { 
						setLoadingNum(i, len);
					}
				});	
			}					
		}
		,error: function( xhr, status ) {
			
			if(xhr.status == '400'){
				alert('guestId : ' + guestId + ' guestId : ' + deguestId + '\n두 지점의 좌료를 확인하세요.');
			}

			if(xhr.status=='502'){
				//$("#loading").modal("hide");
				if(i==len-1){
					keyIndex++;
					apiKey = apiKeyArr[keyIndex];
					ajaxGuestMutualDistanceDataGet();
				}
				//setTimeout("errorAlert()", 2000); 
			};
		}
		
	});

	return result;
}
*/

function setDistanceListData(vehicleGuestDistanceData, i, len){

	var guestLon = vehicleGuestDistanceData['guestLon'];
	var guestLat = vehicleGuestDistanceData['guestLat'];
	var deguestLon = vehicleGuestDistanceData['deguestLon'];
	var deguestLat = vehicleGuestDistanceData['deguestLat'];

	var guestId = vehicleGuestDistanceData['guestId'];
	var deguestId = vehicleGuestDistanceData['deguestId'];

	var guestJusoSubId = vehicleGuestDistanceData['guestJusoSubId'];
	var deguestJusoSubId = vehicleGuestDistanceData['deguestJusoSubId'];

	var guestDate = vehicleGuestDistanceData['guestDate'];
	var deguestDate = vehicleGuestDistanceData['deguestDate'];

	var guestIsShop = vehicleGuestDistanceData['guestIsShop'];
	var deguestIsShop = vehicleGuestDistanceData['deguestIsShop'];

	var updateDate = vehicleGuestDistanceData['updateDate'];

	var status = vehicleGuestDistanceData['status'];

	var strSerialize = '';
	var strTotalDistance;
	
	if(guestLon==deguestLon && guestLat==deguestLat){
		setEqualLocation(vehicleGuestDistanceData, i, len);
		return;
	}

	if(status == ""){
		return;
	}

	strSerialize += "version=1&format=json";
	strSerialize += "&startX="+guestLon;
	strSerialize += "&startY="+guestLat;
	strSerialize += "&startName="+encodeURIComponent("출발지");
	strSerialize += "&endX="+deguestLon;
	strSerialize += "&endY="+deguestLat;
	strSerialize += "&endName="+encodeURIComponent("도착지");
	strSerialize += "&appKey="+apiKey;

	strSerialize += "&searchOption=10";			// 0:교통최적+추천(기본값), 1:교통최적+무료우선, 2:교통최적+최소시간, 3:교통최적+초보, 4:교통최적+고속도로우선, 10:최단
	strSerialize += "&reqCoordType=WGS84GEO";	// 
	strSerialize += "&resCoordType=EPSG3857";

	strSerialize += "&directionOption=0";		// 0:주행방향 비우선(기본값), 1:주행방향우선
	strSerialize += "&roadType=16";				// 32:가까운도로(기본값), 16:일반도로, 8:지하도, 4:고가도, 2:도시 고속도로, 1:고속도로, 0:미선택 

	var result;
	
	$.ajax({
		type: "GET"
		,url: "https://api2.sktelecom.com/tmap/routes"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){

			if(data!=null){

				var jsonData = JSON.stringify(data);
				var objFeatures = data['features'];

				strTotalDistance = objFeatures[0]['properties']['totalDistance'];
				result = strTotalDistance;

				$.ajaxSettings.traditional = true;
				$.ajax({
					url: "ajax/vehicleGuestMutualDistanceProc.ajax.php"
					,data: {
								 "guestId":guestId
								, "deguestId":deguestId
								, "guestLon":guestLon
								, "guestLat":guestLat
								, "deguestLon":deguestLon
								, "deguestLat":deguestLat
								, "guestJusoSubId":guestJusoSubId
								, "deguestJusoSubId":deguestJusoSubId
								, "status":status
								, "guestDate":guestDate
								, "deguestDate":deguestDate
								, "updateDate":updateDate
								, "guestIsShop":guestIsShop
								, "deguestIsShop":deguestIsShop
								, "distance":result
								, "jsonData":jsonData
							}
					,type: "POST"
					,async : true
					,dataType : "json"
					,success: function( json ) {
					}
					,error: function( xhr, status ) {
						//alert(guestLon+","+guestLat+","+deguestLon+","+deguestLat+","+guestId+","+deguestId);
					}
					,complete: function( xhr, status ) { 
						setLoadingNum(i, len);
					}
				});	
			}					
		}
		,error: function( xhr, status ) {
			
			if(xhr.status == '400'){
				alert('guestId : ' + guestId + ' guestId : ' + deguestId + '\n두 지점의 좌표를 확인하세요.\n이 메시지가 보이면 경로찾기 오류입니다. 관리자에게 guestID를 보내주세요.');
			}

			if(xhr.status=='502'){
				//$("#loading").modal("hide");
				if(i==len-1){
					keyIndex++;
					apiKey = apiKeyArr[keyIndex];
					ajaxGuestMutualDistanceDataGet();
				}
				//setTimeout("errorAlert()", 2000); 
			};
		}
		
	});

	return result;
}

function setEqualLocation(vehicleGuestDistanceData,i,len){

	var guestLon = vehicleGuestDistanceData['guestLon'];
	var guestLat = vehicleGuestDistanceData['guestLat'];
	var deguestLon = vehicleGuestDistanceData['deguestLon'];
	var deguestLat = vehicleGuestDistanceData['deguestLat'];

	var guestId = vehicleGuestDistanceData['guestId'];
	var deguestId = vehicleGuestDistanceData['deguestId'];

	var guestJusoSubId = vehicleGuestDistanceData['guestJusoSubId'];
	var deguestJusoSubId = vehicleGuestDistanceData['deguestJusoSubId'];

	var guestDate = vehicleGuestDistanceData['guestDate'];
	var deguestDate = vehicleGuestDistanceData['deguestDate'];

	var guestIsShop = vehicleGuestDistanceData['guestIsShop'];
	var deguestIsShop = vehicleGuestDistanceData['deguestIsShop'];

	var updateDate = vehicleGuestDistanceData['updateDate'];

	var status = vehicleGuestDistanceData['status'];

	$.ajaxSettings.traditional = true;
	$.ajax({
		 url: "ajax/vehicleGuestMutualDistanceProc.ajax.php"
		,data: {
				"guestId":guestId
				, "deguestId":deguestId
				, "guestLon":guestLon
				, "guestLat":guestLat
				, "deguestLon":deguestLon
				, "deguestLat":deguestLat
				, "guestJusoSubId":guestJusoSubId
				, "deguestJusoSubId":deguestJusoSubId
				, "status":status
				, "guestDate":guestDate
				, "deguestDate":deguestDate
				, "updateDate":updateDate
				, "guestIsShop":guestIsShop
				, "deguestIsShop":deguestIsShop
				, "distance":"0"
				, "jsonData":""
		}
		,type: "post"
		,async : true
		,dataType : "json"
		,success: function( json ) {
		}
		,error: function( xhr, status ) { //alert("아이디 체크 오류!!" + xhr); 
			// alert('error');
		}
		,complete: function( xhr, status ) { 
			setLoadingNum(i,len);
		}
	});	
}


//***********************************************배송 차량 업데이트*******************************************************//
function errorAlert(){
	window.location.reload();
	alert("appKey 일일 사용량을 초과 하였습니다.");
}

function GuestAllocateResult(){
	var deliveryDate = vehicleDate;
	var locationId = vehicleLocationId;
	var meridiemType = vehicleTime;
	var meridiemFlag = vehicleTimeFlag;

	var setNum = $("#setNum").val();
	if(setNum==''){
		alert("차량 배차 대수를 입력하세요.");
		return false;
	}
	if(confirm('차량배차를 하시겠습니까?')){
		$("#lastStep .loadingText").text(setNum+"대가 배차 실행중 입니다.");
		$("#loadingDataNum").text("15분정도 소요될수 있습니다.");
		
		$.ajaxSettings.traditional = true;
		$.ajax({
			url: "process/vehicleProcess.php",
			data: {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
					,"vehicleCount":setNum
			},
			type: "post",
			async : true,
			dataType : "json",
			success: function( json ) {
				if(json.code=='N'){
					$("#loading").modal("hide");
					alert("고객정보, 배송지 좌표정보를 업데이트를 해주세요");
					return;
				}
				$("#loading").modal("hide");
				var meridiemType = $("button[name='select-time']").text();
				window.location.href="setCarListTest.html?deliveryDate="+deliveryDate+"&meridiemType="+meridiemType+"&locationId="+locationId+"&meridiemFlag="+meridiemFlag;

			},
			error: function( xhr, status ) { },
			complete: function( xhr, status ) { }
		});
		
	} else {
		return false;
	}	
}