
var apiKey			= "";
var deliveryDate	= "<?=$deliveryDate?>";
var locationId		= "<?=$locationId?>";
var meridiemType	= "<?=$meridiemType?>";
var meridiemFlag	= "<?=$meridiemFlag?>";

var initLat = "<?=$initLat?>";
var initLon = "<?=$initLon?>";
var initGuestId;

var resultMapListData		= new Array();
var resultGroupListData		= new Array();
var arrPopup				= new Array();

var resultOrderListData		= new Array();
var formats;

var pr_3857 = new Tmap.Projection("EPSG:3857");
var pr_4326 = new Tmap.Projection("EPSG:4326");

var arrVector;
var arrMarker;

var vectorNoCurrent;
var vectorNoCount=0;
var vectorNoIndexCurrent;
var vectorNoIndexCount=0;

var markerNoCurrent;
var markerNoCount=0;
var markerNoIndexCurrent;
var markerNoIndexCount=0;

var vectorLayer;

var format;

var oldVehicleNo;

var nIndexGeoCoding = 0;	// timedLoopSetGeoCodingListData 에서 사용



function setInitData(apiKey, initGuestId, initLat, initLon, deliveryDate, locationId, meridiemType){

	this.apiKey		= apiKey;
	this.initGuestId = initGuestId;
	this.initLat	= initLat;
	this.initLon	= initLon;
	this.deliveryDate = deliveryDate;
	this.locationId	  = locationId;
	this.meridiemType = meridiemType;

}

$(function() {
	$("#setDate").datepicker({
		dateFormat: 'yy-mm-dd',
		prevText: '이전 달',
		nextText: '다음 달',
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		yearSuffix: ' / ',
		changeMonth : true,
		changeYear : true,
		minDate : 0
	});
});

//초기화 함수
function initTmap(){
	$("#map_div").html("");

	map = new Tmap.Map({div:'map_div',
						width:'100%',
						height:'768px',
						transitionEffect:"resize",
						animation:true
					});

	format = new Tmap.Format.GeoJSON({
											'internalProjection': map.baseLayer.projection,
											'externalProjection': new Tmap.Projection("EPSG:3857")
										});

	//markers = new Tmap.Layer.Markers("MarkerLayer");
	updateFormats();
};

function updateFormats() {

	var in_options4326 = {
		'internalProjection': map.baseLayer.projection,
		'externalProjection': new Tmap.Projection("EPSG:4326")
	};
	var in_options3857 = {
		'internalProjection': map.baseLayer.projection,
		'externalProjection': new Tmap.Projection("EPSG:3857")
	};
	var out_options = {
		'internalProjection': map.baseLayer.projection,
		'externalProjection': new Tmap.Projection("EPSG:3857")
	};
	var kmlOptionsIn = Tmap.Util.extend(
			{extractStyles: true}, in_options4326);
	formats = {
		'in4326': {
			geojson: new Tmap.Format.GeoJSON(in_options4326),
			kml: new Tmap.Format.KML(kmlOptionsIn)
		},
		'in3857': {
			geojson: new Tmap.Format.GeoJSON(in_options3857),
			kml: new Tmap.Format.KML(kmlOptionsIn)
		},
		'out': {
			geojson: new Tmap.Format.GeoJSON(out_options),
			kml: new Tmap.Format.KML(out_options)
		}
	};

}


//주문 데이터 가져오기 - 주소검색해서 좌표데이터로 변환
function previewAjaxGetOrderDataReGeo(memeType, stcode){
	debugger;

	//var vehicleErpUrl = erpUrl;

	$.ajax({
		type : "GET"
		,url : "ajax/baedal_orderlist.ajax.php"
		,async : true		// 동기화처리
		,dataType : "json"
		,crossDomain: true
//		,jsonpCallback: "callback"
		,data : {
				"jum":stcode			// 매장코드
				,"gbn":memeType
		}
		,success : function(data){
			if(data == null || data['item'] == ""){
				alert('주문데이터가 없습니다.');
				$("#loading").modal("hide");
				return;
			}

			//resultOrderListData = data['vehicleGuestOrderDataList'];
			resultOrderListData = data['item'];

			timedLoopSetGeoCodingListData(resultOrderListData, resultOrderListData.length);

			/*
			var dataList = "";
			for(var i=0;i<resultOrderListData.length;i++){

				dataList += "<tr>" +
								"<td colspan='3'>" +
								"고객명 : [" + resultOrderListData[i]['name'] + "]" +
								"</td>" +
							"</tr>" +
							 "<tr height=\"30\">" +
								"<td align=\"center\" style=\"cursor:pointer\">" + (i+1) + "</td>" +
								"<td align=\"left\" onClick=\"onClickAccnoRe('" + resultOrderListData[i]['lat'] +"','" + resultOrderListData[i]['lon'] + "','" + resultOrderListData[i]['juso'] + "')\" style=\"cursor:pointer\">"
									+  resultOrderListData[i]['accno'] +
								"<br>lat:" + resultOrderListData[i]['lat'] +
								"<br>lon:" + resultOrderListData[i]['lon'] +
								"</td>" +
								"<td align=\"left\" style=\"cursor:pointer\">" + resultOrderListData[i]['juso'] + "</td>" +
							 "</tr>";

				// 주소가져오긔

			}
			*/

		}
		,error: function( xhr, status ) {
			alert('error');
		}
		,complete : function(){
		}	// complete Event 호출시 사용
	});
}


function timedLoopSetGeoCodingListData(refData, refLength){

	//refLength = refData.length;
	//alert("sdfsf" + refLength);
	//return;

	setTimeout(function () {

		setSearchGeoCodingListData(refData[nIndexGeoCoding]['juso'], refData[nIndexGeoCoding]['accno'], nIndexGeoCoding, refLength);
		nIndexGeoCoding++;
		if(nIndexGeoCoding < refData.length) {
			timedLoopSetGeoCodingListData(refData, refLength);
		} else {

		}

	}, 200);

}

// 좌표데이터 검색
function setSearchGeoCodingListData(jusoData, accnoData, i, len){

	//alert(jusoData);
	//searchJusoNewRe(jusoData, i);


	searchJusoNewRe(jusoData, accnoData, i, len);



}

//주문 데이터 가져오기 - 미리보기
function previewAjaxGetOrderDataRe(memeType, stcode){

	//alert(erpUrl + " " + memeType);
	//var vehicleErpUrl = erpUrl;

	$.ajax({
		type : "GET"
//		,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php"
//		,url : vehicleErpUrl
		,url : "ajax/baedal_orderlist.ajax.php"
		,async : true		// 동기화처리
		,dataType : "json"
		,crossDomain: true
//		,jsonpCallback: "callback" ( 외부도메인 접속시에는 주석해제 )
		,data : {
				//"jum":"10"			// 11:반야월, 6:월배, 배센:10
				//"jum":"0023"			//
				//"jum":"0003"			//
				"jum":stcode
				,"gbn":memeType
		}
		,success : function(data){

			//alert(data);

			if(data == null){
				alert('주문데이터가 없습니다.');
				$("#loading").modal("hide");
				return;
			}

			//resultOrderListData = data['vehicleGuestOrderDataList'];
			resultOrderListData = data['item'];


			//alert(resultOrderListData.length);

			var dataList = "";
			for(var i=0;i<resultOrderListData.length;i++){
				dataList += "<tr>" +
								"<td colspan='3'>" +
								"고객명 : [" + resultOrderListData[i]['name'] + "]" +
								"</td>" +
							"</tr>" +
							 "<tr height=\"30\">" +
								"<td align=\"center\" style=\"cursor:pointer\">" + (i+1) + "</td>" +
								"<td align=\"left\" onClick=\"onClickAccno('" + resultOrderListData[i]['lat'] +"','" + resultOrderListData[i]['lon'] + "')\" style=\"cursor:pointer\">"
									+  resultOrderListData[i]['accno'] +
								"<br>lat:" + resultOrderListData[i]['lat'] +
								"<br>lon:" + resultOrderListData[i]['lon'] +
								"</td>" +
								"<td align=\"left\" style=\"cursor:pointer\">" + resultOrderListData[i]['juso'] + "</td>" +
							 "</tr>";

				// 주소가져오긔


			}

			var strMeridiemType = "";
			var date = new Date();

			if(memeType == "a"){
				strMeridiemType = date.getFullYear() + "년 " + (date.getMonth()+1) + "월 " + date.getDate() + "일 오전 배송지역";
			} else {
				strMeridiemType = date.getFullYear() + "년 " + (date.getMonth()+1) + "월 " + date.getDate() + "일 오후 배송지역";
			}

			var resultView = "<div class=\"card\">" +
								"<div class=\"cardTitle\">" +
									"<span class=\"titCard\">" + strMeridiemType + " </span>" +
								"</div>" +
								"<div class=\"cardCont setCarList\" style=\"padding:10px; height:778px;\">" +
									"<table class=\"tblBasic table-hover mt10\">" +
										"<colgroup>" +
											"<col width=\"40\">" +
											"<col width=\"90\">" +
											"<col width=\"\">" +
										"</colgroup>" +
										"<thead>" +
										"<tr height=\"30\">" +
											"<th align=\"center\" class=\"text-center\">번호</th>" +
											"<th align=\"center\" class=\"text-center\">주문번호</th>" +
											"<th align=\"left\" class=\"text-center\">주소</th>" +
										"</tr>" +
										"</thead>" +
										"<tbody>" +
										dataList +
										"</tbody>" +
									"</table>" +
								"</div>" +
							"</div>";


			$('#col-md-3').append(resultView);

			//alert(resultView);
			previewDisplayMarkerRe(resultOrderListData);

		}
		,error: function( xhr, status ) {
			//alert('error');
			$("#loading").modal("hide");
		}
		,complete : function(){
		}	// complete Event 호출시 사용
	});
}

//지도 데이터 가져오기
function previewAjaxGetOrderData(erpUrl, memeType){

	var vehicleErpUrl = erpUrl;

	$.ajax({
		type : "GET"
//		,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php"
		,url : vehicleErpUrl
		,async : false		// 동기화처리
		,dataType : "jsonp"
		,crossDomain: true
		,jsonpCallback: "callback"
		,data : {
					"jum":"6"			// 11:반야월, 6:월배, 배센:10
					,"gbn":memeType
		}
		,success : function(data){

			if(data == null){
				alert('주문데이터가 없습니다.');
				$("#loading").modal("hide");
				return;
			}

			resultOrderListData = data;

			var dataList = "";
			for(var i=0;i<resultOrderListData['item'].length;i++){
				dataList += "<tr height=\"30\">" +
								"<td align=\"center\" style=\"cursor:pointer\">" + (i+1) + "</td>" +
								"<td align=\"left\" onClick=\"onClickAccno('" + resultOrderListData['item'][i]['lat'] +"','" + resultOrderListData['item'][i]['lon'] + "')\" style=\"cursor:pointer\">"
									+  resultOrderListData['item'][i]['accno'] +
								"<br>lat:" + resultOrderListData['item'][i]['lat'] +
								"<br>lon:" + resultOrderListData['item'][i]['lon'] +
								"</td>" +
								"<td align=\"left\" style=\"cursor:pointer\">" + resultOrderListData['item'][i]['juso'] + "</td>" +
							 "</tr>";
			}

			var strMeridiemType = "";
			var date = new Date();

			if(memeType == "a"){
				strMeridiemType = date.getFullYear() + "년 " + (date.getMonth()+1) + "월 " + date.getDate() + "일 오전 배송지역";
			} else {
				strMeridiemType = date.getFullYear() + "년 " + (date.getMonth()+1) + "월 " + date.getDate() + "일 오후 배송지역";
			}

			var resultView = "<div class=\"card\">" +
								"<div class=\"cardTitle\">" +
									"<span class=\"titCard\">" + strMeridiemType + " </span>" +
								"</div>" +
								"<div class=\"cardCont setCarList\" style=\"padding:10px; height:778px;\">" +
									"<table class=\"tblBasic table-hover mt10\">" +
										"<colgroup>" +
											"<col width=\"40\">" +
											"<col width=\"90\">" +
											"<col width=\"\">" +
										"</colgroup>" +
										"<thead>" +
										"<tr height=\"30\">" +
											"<th align=\"center\" class=\"text-center\">번호</th>" +
											"<th align=\"center\" class=\"text-center\">주문번호</th>" +
											"<th align=\"left\" class=\"text-center\">주소</th>" +
										"</tr>" +
										"</thead>" +
										"<tbody>" +
										dataList +
										"</tbody>" +
									"</table>" +
								"</div>" +
							"</div>";


			$('#col-md-3').append(resultView);
			previewDisplayMarker(data);

		}
		,error: function( xhr, status ) {
		}
		,complete : function(){
		}	// complete Event 호출시 사용
	});
}


function onClickAccnoRe(strLat, strLon, strJuso){


	searchJusoNewRe(strJuso, nIndex);

	//map.setCenter(new Tmap.LonLat(strLon,strLat).transform(pr_4326, pr_3857), 19);
}

function onClickAccno(strLat, strLon){
	map.setCenter(new Tmap.LonLat(strLon,strLat).transform(pr_4326, pr_3857), 18);
}

//지도 데이터 가져오기
function previewAjaxGetOrderDataList(refData){

	$.ajax({
		type : "GET"
		,url : "ajax/previewVehicleList.ajax.php"
		,async : true		// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){

			//initTmap();
			if(data.vehicleAllocateResultList==null){
				displayRouteCompanyMarker(initLon, initLat);
				map.setCenter(new Tmap.LonLat(initLon,initLat).transform(pr_4326, pr_3857), 13);
				$("#loading").modal("hide");
				alert("배차가 없습니다11.");
				return;
			}
			resultMapListData = data['vehicleAllocateResultList'];
			displayRouteStart();

		}
		,error: function( xhr, status ) {
 			displayRouteCompanyMarker(initLon, initLat);
			alert("배차가 없습니다22.");
			$("#loading").modal("hide");
		}
		,complete : function(){

		}	// complete Event 호출시 사용
	});
}

//배차지역 미리보기 마크 그리기
function previewDisplayMarkerRe(refData){

	displayRouteCompanyMarker(initLon, initLat);
	arrMarker = new Array();
	var nIndex = 1;

	for(i=0;i<refData.length;i++){
		if(refData[i]['lat'] > 0 && refData[i]['lon'] > 0){
			previewDisplayRouteMarker(refData[i], i);
			nIndex++;
		} else {

		}
	}

	map.setCenter(new Tmap.LonLat(initLon, initLat).transform(pr_4326, pr_3857), 13);

	$("#loading").modal("hide");

}

//배차지역 미리보기 마크 그리기
function previewDisplayMarker(refData){

	displayRouteCompanyMarker(initLon, initLat);
	arrMarker = new Array();

	var nIndex = 1;
	for(i=0;i<refData['item'].length;i++){
		if(refData['item'][i]['lat'] > 0 && refData['item'][i]['lon'] > 0){
			previewDisplayRouteMarker(refData['item'][i], i);
			nIndex++;
		}
	}

	map.setCenter(new Tmap.LonLat(initLon, initLat).transform(pr_4326, pr_3857), 13);

	$("#loading").modal("hide");

}

//배차 지도에서 마커 그리기
function previewDisplayRouteMarker(list, varSeq){

	varSeq += 1;

	//마커
	var markers = new Tmap.Layer.Markers("MarkerLayer");
	map.addLayer(markers);


	/*******************************************마커 커스텀 시작*******************************************************/

	var size = new Tmap.Size(26,38);
	var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
	var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
	var number = parseInt(list['vehicleNoIndex']);
	number += 1;

	if(list['vehicleNoIndex'] > 9){
		style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
	}
	var span="<span style='"+style+"'>"+varSeq+"</span>";
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="images/icon/marker_0.png" /></div>', size, offset);

	/*******************************************마커 커스텀 끝*******************************************************/

	var lon = list['lon'];
	var lat = list['lat'];
	var marker = new Tmap.Marker(new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857), iconHtml);
	var popup;
	var html = list['guestName']+"</br>"+list['Juso']+"</br>"+list['guestTel'];
				"<div style='display:none'><a href=''>자세히보기</a></div>";
	popup = new Tmap.Popup(list['deguestLat']+","+list['deguestLon']+","+list['vehicleNoIndex'],
							new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857),
							new Tmap.Size(300, 80),
							html,
							true
							);

	arrPopup[varSeq] = popup;
	map.addPopup(popup);
	popup.hide();

	//marker.events.register("click", popup, onOverMarker);
	//marker.events.register("mouseover", popup, onOverMarker);
	//marker.events.register("mouseout", popup, onOutMarker);
	markers.addMarker(marker);
	//arrMarker[markerNoCount][markerNoIndexCount] = markers;

}


//지도 데이터 가져오기
function ajaxGetMapRouteData(){

	//alert("[" + meridiemType + "]");

	$.ajax({
		type : "GET"
		,url : "ajax/vehicleAllocateResultMap.ajax.php"
		,async : true		// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){
			//initTmap();
			if(data.vehicleAllocateResultList==null){
				displayRouteCompanyMarker(initLon, initLat);
				map.setCenter(new Tmap.LonLat(initLon,initLat).transform(pr_4326, pr_3857), 13);
				$("#loading").modal("hide");
				alert("배차가 없습니다33.");
				return;
			}
			debugger;
			resultMapListData = data['vehicleAllocateResultList'];
			displayRouteStart();

		}
		,error: function( xhr, status ) {
 			displayRouteCompanyMarker(initLon, initLat);
			alert("배차가 없습니다44.");
			$("#loading").modal("hide");
		}
		,complete : function(){

		}	// complete Event 호출시 사용
	});
}
//지도 데이터 가져오기
function tmpAjaxGetMapRouteData(){

	$.ajax({
		type : "GET"
		,url : "ajax/ZtmpAddrToDistanceTotalSe_View.ajax.php"
		,async : true		// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){

			if(data.vehicleAllocateResultList==null){
				displayRouteCompanyMarker(initLon, initLat);
				map.setCenter(new Tmap.LonLat(initLon,initLat).transform(pr_4326, pr_3857), 13);
				$("#loading").modal("hide");
				alert("배차가 없습니다55.");
				return;
			}
			resultMapListData = data['vehicleAllocateResultList'];
			displayRouteStart();

		}
		,error: function( xhr, status ) {
 			displayRouteCompanyMarker(initLon, initLat);
			alert("배차가 없습니다66.");
			$("#loading").modal("hide");
		}
		,complete : function(){

		}	// complete Event 호출시 사용
	});
}
//지도 전체 보기
function allMapView(strArr){

	var strArrSplit;

	var isVisible = true;
	if(strArr==''){
		isVisible = false;
	} else {
		strArrSplit = strArr.split(',');
	}

	// 경로선지우긔
	for(var i=0; i<arrMarker.length; i++){

		for(var j=0; j<arrMarker[i].length;j++){
			arrMarker[i][j].setVisibility(false);
		}
	}

	// 마크지우긔
	for(var i=0; i<arrVector.length; i++){

		for(var j=0; j<arrVector[i].length;j++){
			arrVector[i][j].setVisibility(false);
		}
	}

	if(strArr != ''){

		// 지도경로선 제거 채크박스가 채크해제되어 있으면, 지도경로선 표시안하긔
		if(!bCheckBoxLineRemove){
			for(var i=0;i<strArrSplit.length;i++){

				for(var j=0;j<arrVector[strArrSplit[i]].length;j++){
					arrVector[strArrSplit[i]][j].setVisibility(true);
				}
			}
		}
		for(var i=0;i<strArrSplit.length;i++){

			for(var j=0;j<arrMarker[strArrSplit[i]].length;j++){
				arrMarker[strArrSplit[i]][j].setVisibility(true);
			}
		}
	}

}
//지도 단일 보기
function singleMapView(num, is){
	var lineIs = $("#check-line").is(":checked");

	//alert(arrVector[num].length);
	//alert(arrMarker[num].length);

	for(var i=0; i<arrVector[num].length;i++){
		if(lineIs){
			arrVector[num][i].setVisibility(false);
		} else {
			arrVector[num][i].setVisibility(is);
		}
	}

	for(var j=0; j<arrMarker[num].length;j++){
		arrMarker[num][j].setVisibility(is);
	}
}
//라인 체크 보기
function lineMapView(strArr, is){
	var arr = strArr.split(",");
	var isView;
	if(is){
		isView = false;
	} else {
		isView = true;
	}
	for(var i=0;i<arr.length;i++){
		var vehicleNum = arr[i];
		for(var j=0;j<arrVector[vehicleNum].length;j++){
			arrVector[vehicleNum][j].setVisibility(isView);
		}
	}
}



//배차 출력 시작(라인)
function displayRouteStart(){
	//배차 지도에서 선 그리기
	arrVector = new Array();
	if(checkLine==false){
		for(j=0;j<resultMapListData.length;j++){
			arrVector[j] = new Array();
			displayRouteLoad(resultMapListData[j]['jsonData'],routeColor[Number(resultMapListData[j]['vehicleNo'])],j,resultMapListData[j]['vehicleNo'],resultMapListData[j]['vehicleNoIndex']);
			//ajaxTmapData(resultMapListData[j-1]['guestId'],resultMapListData[j]['guestId'],routeColor[resultMapListData[j]['vehicleNo']+5], j, resultMapListData[j]['vehicleNoIndex'],'num');
		}
	}
	setTimeout("displayMarkerDelay()",100);
}



// 배차 지도에서 선 그리기
function displayRouteLoad(data, color, num, vehicleNo, vehicleNoIndex){
	debugger
	// 지도상에 그려질 스타일을 설정합니다
	// null일때 값이 없더라도 빈 레이어라도 그려줘야됨...중요
	if(data==null){
		return;
	}
	if(vectorNoCurrent==null){
		vectorNoCurrent = parseInt(vehicleNo);
	}
	if(vectorNoIndexCurrent==null){
		vectorNoIndexCurrent = parseInt(vehicleNoIndex);
	}

	if(vectorNoIndexCurrent < parseInt(vehicleNoIndex)){
		vectorNoIndexCurrent = parseInt(vehicleNoIndex);
		vectorNoIndexCount++;
	}
	if(vectorNoCurrent < parseInt(vehicleNo)){
		vectorNoCurrent = parseInt(vehicleNo);
		vectorNoCount++;
		vectorNoIndexCount = 0;
		vectorNoIndexCurrent = 0;
	}

	var styleMap = new Tmap.StyleMap({'default': new Tmap.Style({
																pointColor: color,
																pointRadius: 5,
																//stroke : false,
																strokeColor: color,
																strokeWidth: 4,
																strokeOpacity: 4,
																strokeLinecap : "square",
																graphicZIndex: num
															})
								});
	vectorLayer = new Tmap.Layer.Vector("vector", {styleMap:styleMap});
	vectorLayer.events.register("featuresadded", vectorLayer, onDrawnFeatures); // 그리기 완료 이벤트 생성

	map.addLayer(vectorLayer);

	var geoForm = format.read(data);

	// 변환된 데이터를 레이어에 그립니다
	// 그리고 싶은 부분만 발췌해서 그리는 것도 가능합니다
	vectorLayer.addFeatures(geoForm);
	//for(i=0;i<geoForm.length;i++){
	//	if(geoForm[i]){
	//		vectorLayer.addFeatures(geoForm[i]);
	//		//vectorLayer.removeFeatures(geoForm[i]);
	//	}
	//}
	arrVector[vectorNoCount][vectorNoIndexCount] = vectorLayer;

}

// 마크 삭제
function removeMarker(vehicleNo, vehicleNoIndex){
	//arrMarker[markerNoCount][markerNoIndexCount]
	//map.removeMarker(arrMarker[0][1]);
	//markers.removeMarker(arrMarker[0][1]);
	//map.removeLayer(arrMarker[vehicleNo][vehicleNoIndex]);
}


// 맵지우고, 관련배열 초기화
function removeRoute(){

	var nCntVehicleNo = arrVector.length;
	for(i=0;i<nCntVehicleNo;i++){
		var nCntVehicleNoIndex = arrVector[i].length;
		for(j=0;j<nCntVehicleNoIndex;j++){
			map.removeLayer(arrVector[i][j]);
		}
	}

	var nCntMarkNo = arrMarker.length;
	for(i=0;i<nCntMarkNo;i++){
		var nCntMarkNoIndex = arrMarker[i].length;
		for(j=0;j<nCntMarkNoIndex;j++){
			map.removeLayer(arrMarker[i][j]);
		}
	}

	vectorNoCurrent = null;
	vectorNoCount=0;
	vectorNoIndexCurrent = null;
	vectorNoIndexCount=0;

	markerNoCurrent = null;
	markerNoCount=0;
	markerNoIndexCurrent = null;
	markerNoIndexCount=0;

	simpleAjaxGetMapRouteData();

	return;

	//map.removeLayer(arrVector[0][0]);
	//map.removeLayer(arrVector[0][1]);
	//map.removeLayer(arrVector[0][2]);
	//map.removeLayer(arrVector[0][3]);

}

function simpleAjaxGetMapRouteData(){

	$.ajax({
		type : "GET"
		,url : "ajax/vehicleAllocateResultMap.ajax.php"
		,async : true		// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){
			resultMapListData = data['vehicleAllocateResultList'];
			simpleDisplayRouteStart();
		}
		,error: function( xhr, status ) {
 			displayRouteCompanyMarker(initLon, initLat);
			alert("배차가 없습니다77.");
			$("#loading").modal("hide");
		}
		,complete : function(){ }
	});
}

//배차 출력 시작(라인)
function simpleDisplayRouteStart(){
	//배차 지도에서 선 그리기
	arrVector = new Array();
	if(checkLine==false){

		for(j=0;j<resultMapListData.length;j++){
			arrVector[j] = new Array();
			simpleDisplayRouteLoad(resultMapListData[j]['jsonData'],routeColor[Number(resultMapListData[j]['vehicleNo'])],j,resultMapListData[j]['vehicleNo'],resultMapListData[j]['vehicleNoIndex']);
		}
	}
	setTimeout("simpleDisplayMarkerDelay()", 0);
}

// 배차 지도에서 선 그리기
function simpleDisplayRouteLoad(data, color, num, vehicleNo, vehicleNoIndex){

	// null일때 값이 없더라도 빈 레이어라도 그려줘야됨...중요
	if(data==null){
		return;
	}
	if(vectorNoCurrent==null){
		vectorNoCurrent = parseInt(vehicleNo);
	}
	if(vectorNoIndexCurrent==null){
		vectorNoIndexCurrent = parseInt(vehicleNoIndex);
	}

	if(vectorNoIndexCurrent < parseInt(vehicleNoIndex)){
		vectorNoIndexCurrent = parseInt(vehicleNoIndex);
		vectorNoIndexCount++;
	}
	if(vectorNoCurrent < parseInt(vehicleNo)){
		vectorNoCurrent = parseInt(vehicleNo);
		vectorNoCount++;
		vectorNoIndexCount = 0;
		vectorNoIndexCurrent = 0;
	}

	var styleMap = new Tmap.StyleMap({'default': new Tmap.Style({
																pointColor: color,
																pointRadius: 5,
																//stroke : false,
																strokeColor: color,
																strokeWidth: 4,
																strokeOpacity: 4,
																strokeLinecap : "square",
																graphicZIndex: num
															})
								});
	vectorLayer = new Tmap.Layer.Vector("vector", {styleMap:styleMap});
	//vectorLayer.events.register("featuresadded", vectorLayer, onDrawnFeatures); // 그리기 완료 이벤트 생성

	map.addLayer(vectorLayer);

	var geoForm = format.read(data);

	// 변환된 데이터를 레이어에 그립니다
	// 그리고 싶은 부분만 발췌해서 그리는 것도 가능합니다
	vectorLayer.addFeatures(geoForm);
	arrVector[vectorNoCount][vectorNoIndexCount] = vectorLayer; // 요기
}

function simpleDisplayMarkerDelay(){

	//배차 지도에서 마커 그리기
	displayRouteCompanyMarker(initLon, initLat);
	arrMarker = new Array();
	var count = 0;
	for(j=0;j<resultMapListData.length;j++){
		if(initGuestId!=resultMapListData[j]['deguestId']){
			arrMarker[count] = new Array();
			displayRouteMarker(resultMapListData[j], j);
			count++;
		}
	}

	$("#markerModal").modal("hide");

	if(typeof strCheckBoxRoute != "undefined"){
		// UI변경은
		allMapView(strCheckBoxRoute.slice(0, -1));
		checkBoxRestore();
	}
}


//배차 출력 시작(마커)
function displayMarkerDelay(){

	//배차 지도에서 마커 그리기
	displayRouteCompanyMarker(initLon, initLat);
	//alert('dd');
	arrMarker = new Array();
	var count = 0;

	//markerNoCurrent = 0;
	//count++;

	for(j=0;j<resultMapListData.length;j++){
		if(initGuestId!=resultMapListData[j]['deguestId']){
			arrMarker[count] = new Array();
			displayRouteMarker(resultMapListData[j], j);
			count++;
		}
	}
	map.setCenter(new Tmap.LonLat(initLon,initLat).transform(pr_4326, pr_3857), 13);
	$("#loading").modal("hide");

}

//배차 지도에서 마커 그리기
function displayRouteMarker(list, varSeq){
	debugger;
	if(markerNoCurrent==null){
		markerNoCurrent = parseInt(list['vehicleNo']);
	}
	if(markerNoIndexCurrent==null){
		markerNoIndexCurrent = parseInt(list['vehicleNoIndex']);
	}
	if(markerNoIndexCurrent < parseInt(list['vehicleNoIndex'])){
		markerNoIndexCurrent = parseInt(list['vehicleNoIndex']);
		markerNoIndexCount++;
	}
	if(markerNoCurrent < parseInt(list['vehicleNo'])){
		markerNoCurrent = parseInt(list['vehicleNo']);
		markerNoCount++;
		markerNoIndexCurrent = 0;
		markerNoIndexCount = 0;
	}

	//마커
	var markers = new Tmap.Layer.Markers("MarkerLayer");
	map.addLayer(markers);

	/*******************************************마커 커스텀 시작*******************************************************/
	var size = new Tmap.Size(26,38);
	var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
	var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
	var number = parseInt(list['vehicleNoIndex']);
	number += 1;

	if(list['errorJusoFlag'] == 'Y'){
		number--;
	}

	if(list['vehicleNoIndex'] > 9){
		style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
	}
	var span="<span style='"+style+"'>"+number+"</span>";
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="images/icon/marker_'+list['vehicleNo']+'.png" /></div>', size, offset);

	/*******************************************마커 커스텀 끝*******************************************************/

	var lon = list['deguestLon'];
	var lat = list['deguestLat'];
	var marker = new Tmap.Marker(new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857), iconHtml);
	var popup;
	var html = list['guestName']+"</br>"+list['Juso']+"</br>"+list['guestTel'];
				"<div style='display:none'><a href=''>자세히보기</a></div>";
	popup = new Tmap.Popup(	list['deguestLat']+","+list['deguestLon']+","+list['vehicleNoIndex']
							,new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857)
							,new Tmap.Size(300, 80)
							,html
							,true);

	arrPopup[varSeq] = popup;
	map.addPopup(popup);
	popup.hide();

	marker.events.register("click", popup, onOverMarker);
	//marker.events.register("mouseover", popup, onOverMarker);
	//marker.events.register("mouseout", popup, onOutMarker);
	markers.addMarker(marker);
	arrMarker[markerNoCount][markerNoIndexCount] = markers;
}

//배차 지도에서 마커 그리기(거점 마커)
function displayRouteCompanyMarker(varLon, varLat){

	//마커
	var markers = new Tmap.Layer.Markers("MarkerLayer");
	map.addLayer(markers);

	/*******************************************마커 커스텀 시작*******************************************************/
	var size = new Tmap.Size(26,38);
	var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
	var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";

	var span="<span style='"+style+"'>S</span>";
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="images/icon/marker_green.png" /></div>', size, offset);

	/*******************************************마커 커스텀 끝*******************************************************/

	var lon = varLon;
	var lat = varLat;
	var marker = new Tmap.Marker(new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857), iconHtml);
	var popup;
	var html = ""+
				"<div style='display:none'><a href=''>자세히보기</a></div>";
	popup = new Tmap.Popup("0",
				new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857),
				new Tmap.Size(300, 80),
				html,true
				);

	arrPopup[0] = popup;
	map.addPopup(popup);
	popup.hide();

	//marker.events.register("click", popup, onOverMarker);
	markers.addMarker(marker);
}

//마커 마우스오버
function onOverMarker(evt){
	markerClick(this.id);
	hideAllPopup();
}

//마커 마우스아웃
function onOutMarker(evt){
	//this.hide();
}

//모든 팝업 숨기기
function hideAllPopup(){
	for(i=0;i<arrPopup.length;i++){
		arrPopup[i].hide();
	}
}
//경로 그리기 후 해당영역으로 줌
function onDrawnFeatures(e){
	map.zoomToExtent(this.getDataExtent());
}

//function onSelectedFeatures(e){
//	alert(this.id);
//}

var simpleDeguestInfoList = new Array();

//마커 클릭 이벤트
function markerClick(latlon){

	var lat = latlon.split(",")[0];
	var lon = latlon.split(",")[1];
	var index = latlon.split(",")[2];		// 배차경로번호	vehicleNoIndex
	index = parseInt(index);

	//var oldVehicleNo = '';

	$.ajax({
		type : "GET"
		,url : "ajax/markerPopupData.ajax.php"
		,async : true		// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
					,"lat":lat
					,"lon":lon
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(data){

			$("#markerDetail").html("");
			var selectView="";
			var vehicleAllocateResultList	= data['vehicleAllocateResultList'];
			simpleDeguestInfoList = data['orderUserList'];

			var vehicleNo = data['orderUserList'][0]['vehicleNo'];
			oldVehicleNo = vehicleNo;

			for(var j=0;j<vehicleAllocateResultList.length;j++){

				var itemResult = vehicleAllocateResultList[j];

				var number = parseInt(itemResult['vehicleNo']);
				number += 1;

				var distance = itemResult["distanceValue"];

				if(distance > 999){
					distance = (distance*0.001).toFixed(1)+"km";
				} else {
					distance = distance+"m";
				}

				if(itemResult['vehicleNo'] == Number(vehicleNo)){
					//selectView += "<option value='"+itemResult['vehicleNo']+"'>"+number+" 경로["+distance+"]</option>";
					selectView += "<option value='"+itemResult['vehicleNo']+"' selected>"+number+"번경로</option>";
				} else {
					selectView += "<option value='"+itemResult['vehicleNo']+"'>"+number+"번경로</option>";
				}

			}

			//if(data['orderUserList'][0]['guestId']!=''){
				index +=1;
			//}

			var vehicleNoIndex = data['orderUserList'][0]['vehicleNoIndex'];
			var nowColor = Number(vehicleNo) + 1;
			$(".nowColor").html("<img src='images/icon/marker_" + vehicleNo + ".png' alt='' /><span style='margin-left:10px'>" + nowColor + "</span>번 경로");
			$("#orderChange").html("");
			$("#orderChange").append("<img src='images/icon/marker_" + vehicleNo + ".png' class='chgColor' /><select id='chgColor' class='form-control ml10 mr10' name=selectChange>"+
										selectView+
									"</select>"+
									"<button type='button' class='btn btn-new-ok' onclick=vehicleIndexChange('"+vehicleNo+"','"+vehicleNoIndex+"','"+meridiemFlag+"')>변경</button>");

			var accnoDupleJuso ='';
			var accnoDupleJusoCopy = '';
			var deguestId = '';
			var deguestIdCopy = '';
			var strDupleJusoFlg = '';

			for(var i=0;i<data['orderUserList'].length;i++){
				var item = data['orderUserList'][i];
				var pay = "0";
				if(item['deguestPay']!=null){
					pay = numberWithCommas(item['deguestPay']);
				}
				/*
				if(item['accnoDupleJuso'] != ""){
					strDupleJusoFlg = "(중첩지역)";
				} else {
					strDupleJusoFlg = "";
				}
				*/

				accnoDupleJuso	= item['accnoDupleJuso'];
				deguestId		= item['deguestId'];
				if(accnoDupleJuso != '' && (accnoDupleJusoCopy == accnoDupleJuso) && (deguestIdCopy != deguestId)){
					strDupleJusoFlg = "(중첩지역)";
				} else {
					accnoDupleJusoCopy = accnoDupleJuso;
					deguestIdCopy = deguestId;
				}

				/*
				var resultView = "<tr height='30'>"+
								"<td align='center'>"+index+" "+ strDupleJusoFlg +"</td>"+
								"<td align='center'>"+item['deguestAccno']+"</td>"+
								"<td align='center'>"+item['deguestName']+"</td>"+
								"<td align='center'>"+item['deguestTel']+"</td>"+
								"<td align='center'>"+item['Juso']+"</td>"+
								"<td align='center'>"+pay+"원</td>"+
							"</tr>";
				*/
				var resultView = "<tr height='30'>"+
								"<td align='center'>"+index+"<br />"+ strDupleJusoFlg +"</td>"+
								"<td align='center'>"+item['deguestAccno']+"</td>"+
								"<td align='center'>"+item['deguestName']+"</td>"+
								"<td align='center'>"+item['deguestTel']+"</td>"+
								"<td align='center'><input type='text' id='deguestJuso"+i+"' name='deguestJuso"+i+"' size='40' value='" + item['Juso'] + "' class='form-control' /></td>"+
								"<td align='center'>"+pay+"원</td>"+
								"<td align='center'><input type='text' id='deguestLat"+i+"' name='deguestLat"+i+"' size='15' value='" + item['deguestLat'] + "' class='form-control ddd' /></td>"+
								"<td align='center'><input type='text' id='deguestLon"+i+"' name='deguestLon"+i+"' size='15' value='" + item['deguestLon'] + "' class='form-control' /></td>"+
								"<td align='center'><button type='button' class='btn btn-sm btn-new-find' onClick=searchJusoNew('" + i + "')>검색</button></td>"+
								"<td align='center'><button type='button' class='btn btn-sm btn-new-find' onClick=guestLocationUpdate('" + item['deguestId'] + "','" + i + "');>좌표업데이트</button></td>"+
								"<input type='hidden' id='guestJusoSubId"+i+"' name='guestJusoSubId"+i+"' value='" + item['deguestJusoSubId'] + "' />" +
								"<input type='hidden' id='guestIsShop"+i+"' name='guestIsShop"+i+"' value='" + item['deguestIsShop'] + "' />" +
								"<input type='hidden' id='guestId"+i+"' name='guestId"+i+"' value='" + item['deguestId'] + "' />" +

								"<input type='hidden' id='guestTel"+i+"' name='guestTel"+i+"' value='" + item['deguestTel'] + "' />" +
								"<input type='hidden' id='guestName"+i+"' name='guestName"+i+"' value='" + item['deguestName'] + "' />" +
								"<input type='hidden' id='accno"+i+"' name='accno"+i+"' value='" + item['deguestAccno'] + "' />" +
								"<input type='hidden' id='guestPay"+i+"' name='guestPay"+i+"' value='" + item['deguestPay'] + "' />" +

								"<input type='hidden' id='accnoDupleJuso"+i+"' name='accnoDupleJuso"+i+"' value='" + item['accnoDupleJuso'] + "' />" +
								"<input type='hidden' id='seq"+i+"' name='seq"+i+"' value='" + item['seq'] + "' />" +
							"</tr>";
				$("#markerDetail").append(resultView);
				strDupleJusoFlg = '';
			}

			//$("#myModalLabel2").text((Number(vehicleNo)+1) + "번경로의 " + (index) + "번째 배송지정보 상세보기");
			$("#markerModal").modal();
		}
		,error: function( xhr, status ) {
			alert('error');
		}
		,complete : function(){
		}
	});
}

//
function simpleDeguestInfoUpdate() {

	alert(simpleDeguestInfoList.length);
	return;

	for(var i=0;i<simpleDeguestInfoList.length;i++){
		//var selectVehicleNo = $("select[name=order"+i+"]").val();
		var juso = $("#deguestJuso"+i).val();
		var lat = $("#deguestLat"+i).val();
		var lon = $("#deguestLon"+i).val();
		var seq = $("#seq"+i).val();
		var guestJusoSubId = $("#guestJusoSubId"+i).val();
		var guestIsShop = $("#guestIsShop"+i).val();
		var guestId = $("#guestId"+i).val();
		var vehicleNo = $("select[name=selectChange]").val();

		var guestTel = $("#guestTel"+i).val();
		var guestName = $("#guestName"+i).val();
		var accno = $("#accno"+i).val();

		var guestPay = $("#guestPay"+i).val();
		var accnoDupleJuso = $("#accnoDupleJuso"+i).val();

		// 같은 경로는 변경못하게 처리
		if(juso==''){
			alert("주소를 입력하세요.");
			return;
		}
		if(lat=='' || lon=='' || lat==0 || lon==0){
			alert("위도경도를 정확히 입력하세요.");
			return;
		}

		simpleDeguestInfoList[i]['seq']				= seq;
		simpleDeguestInfoList[i]['guestJuso']		= juso;
		simpleDeguestInfoList[i]['guestLat']		= lat;
		simpleDeguestInfoList[i]['guestLon']		= lon;
		simpleDeguestInfoList[i]['guestId']			= guestId;
		simpleDeguestInfoList[i]['guestJusoSubId']	= guestJusoSubId;
		simpleDeguestInfoList[i]['guestIsShop']		= guestIsShop;
		simpleDeguestInfoList[i]['vehicleNo']		= vehicleNo;

		simpleDeguestInfoList[i]['guestTel']		= guestTel;
		simpleDeguestInfoList[i]['guestName']		= guestName;
		simpleDeguestInfoList[i]['accno']			= accno;

		simpleDeguestInfoList[i]['guestPay']		= guestPay;
		simpleDeguestInfoList[i]['accnoDupleJuso']	= accnoDupleJuso;

	}

	//for(var i=0;i<simpleDeguestInfoList.length;i++){
	//alert(simpleDeguestInfoList[i]['seq'] + '\n' + simpleDeguestInfoList[i]['guestJuso'] + '\n' + simpleDeguestInfoList[i]['guestLat'] + '\n' + simpleDeguestInfoList[i]['guestLon'] + '\n' + simpleDeguestInfoList[i]['guestId'] + '\n' + simpleDeguestInfoList[i]['guestJusoSubId'] + '\n' + simpleDeguestInfoList[i]['guestIsShop'] + '\n' + simpleDeguestInfoList[i]['vehicleNo'] + '\n' + simpleDeguestInfoList[i]['guestTel'] + '\n' + simpleDeguestInfoList[i]['guestName'] + '\n' + simpleDeguestInfoList[i]['accno'] + '\n' + simpleDeguestInfoList[i]['guestPay'] + '\n' + simpleDeguestInfoList[i]['accnoDupleJuso']);
	//}

	loadModal();

	var object = new Object();
	object.simpleDeguestInfoList =  simpleDeguestInfoList;
	object.deliveryDate = deliveryDate;
	object.locationId = locationId;
	object.meridiemType = meridiemType;
	object.meridiemFlag = meridiemFlag;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/simpleDeguestInfoList.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(object)
		,contentType : "application/json"
		,success : function(result){
			ajaxGuestMutualDistanceDataGet();
		}
		,complete : function(){}
	});

	return;
}


/***************************************************배송 경로 추가 관련 javascript*********************************************/

//배송 경로 추가 저장
function addSave(){

	//alert(vehicleGuestOrderDataList.length);

	loadModal();

	for(var i=0;i<vehicleGuestOrderDataList.length;i++){
		var selectVehicleNo = $("select[name=order"+i+"]").val();
		vehicleGuestOrderDataList[i]['vehicleNo'] = selectVehicleNo;
	}
	var object = new Object();
	object.vehicleGuestOrderDataList =  vehicleGuestOrderDataList;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/addVehicleUserSave.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(object)
		,contentType : "application/json"
		,success : function(result){
			addVehicleResultData();
		}
		,complete : function(){}
	});

}

//배송 경로 vehicleOrder,vehicleResult 테이블 데이터 추가
function addVehicleResultData(){

	//alert(vehicleGuestOrderDataList.length);

	var object = new Object();
	object.vehicleGuestOrderDataList =  vehicleGuestOrderDataList;
	object.deliveryDate = deliveryDate;
	object.locationId	= locationId;
	object.meridiemType	= meridiemType;
	object.meridiemFlag	= meridiemFlag;

	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({

		 type : "POST"
		,url : "ajax/addVehicleResultUpdate.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(object)
//		,contentType : "application/json"
		,dataType : "json"
		,success : function(result){
			//alert('success');
			//ajaxGuestInfoFailCheck(result["vehicleGuestOrderDataList"]);
			$("#loading").modal("hide");
		}
		,error: function( xhr, status ) {
			//alert('error');
		}
		,complete : function(){
			//alert('complete');
		}
	});



}
//step05. 위 경도 주소 확인시 문제 발견
function ajaxGuestInfoFailCheck(vehicleOrderData){

	for(var i=0;i<vehicleOrderData.length;i++){
		var item = vehicleOrderData[i];
		checkLatLonConvert(item, i, vehicleOrderData.length);
	}
}

/****************************************배송 경로 오류 데이터 처리*************************************************************/

//배송 경로 오류 저장
function errorSave(){

	if(confirm("배송경로 오류건을 수정하시겠습니까?\n입력되지 않은 좌표정보는 반영되지 않습니다. ")){

		checkBoxStatus();

		for(var i=0;i<vehicleErrorUserList.length;i++){
			var selectVehicleNo = $("select[name=order"+i+"]").val();
			var juso = $("#juso"+i).val();
			var lat = $("#lat"+i).val();
			var lon = $("#lon"+i).val();

			//if(juso==''){
			//	alert("주소를 입력하세요.");
			//	return;
			//}
			//if(lat=='' || lon=='' || lat==0 || lon==0){
			//	alert("위도경도를 정확히 입력하세요.");
			//	return;
			//}

			vehicleErrorUserList[i]['vehicleNo'] = selectVehicleNo;
			vehicleErrorUserList[i]['guestJuso'] = juso;
			vehicleErrorUserList[i]['guestLat'] = lat;
			vehicleErrorUserList[i]['guestLon'] = lon;

		}

		loadModal();

		var object = new Object();
		object.vehicleErrorUserList =  vehicleErrorUserList;
		object.deliveryDate = deliveryDate;
		object.locationId = locationId;
		object.meridiemType = meridiemType;
		object.meridiemFlag = meridiemFlag;

		// ajax 실행부
		$.ajaxSettings.traditional = true;
		$.ajax({
			 type : "POST"
			,url : "ajax/errorVehicleUserSave.ajax.php"
			,async : true	// 동기화처리
			,data : JSON.stringify(object)
			,contentType : "application/json"
			,success : function(result){
				$("#alertModal").modal('hide');
				$("#loading").modal("hide");
				removeRoute();
				simpleLoadVehicleList();
			}
			,complete : function(){}
		});

	}

	return;

}

/***************************************************배차 거리 UPDATE********************************************************/
//ajax 금일 업데이트된 주소가져오기.
function ajaxGuestMutualDistanceDataGet(){

	loadModal();
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

//ajax 금일 업데이트된 주소DB와 거리DB 비교
function compareJusoData(vehicleGuestInfoData){

	vehicleGuestInfoData.deliveryDate = deliveryDate;

	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/vehicleGuestMutualDistanceCompare.ajax.php"
		,async : true	// 동기화처리
		,data : JSON.stringify(vehicleGuestInfoData)
		,dataType : "json"
		,success : function(data){

			if(data['vehicleGuestDistanceData']==null){

				removeRoute();
				simpleLoadVehicleList();
				//alert('배송경로 오류건에 정보가 업데이트 되었습니다.');
				//$("#loading").modal("hide");
				return;
			}
			initDistanceListData(data['vehicleGuestDistanceData']);
		}
		,complete : function(){}
	});
}

//주소지점사이 거리값 구하기
function initDistanceListData(vehicleGuestDistanceData){
	for(i=0;i<vehicleGuestDistanceData.length;i++){
		setDistanceListData(vehicleGuestDistanceData[i], i, vehicleGuestDistanceData.length);
	}
}


//주소지점사이 거리 데이터 업데이트(실제)
function setDistanceListData(vehicleGuestDistanceData, i, len){
	debugger;
	var guestLon = vehicleGuestDistanceData['guestLon'];
	var guestLat = vehicleGuestDistanceData['guestLat'];
	var deguestLon = vehicleGuestDistanceData['deguestLon'];
	var deguestLat = vehicleGuestDistanceData['deguestLat'];

	var guestId = vehicleGuestDistanceData['guestId'];
	var deguestId = vehicleGuestDistanceData['deguestId'];

	var guestJusoSubId = vehicleGuestDistanceData['guestJusoSubId'];
	var deguestJusoSubId = vehicleGuestDistanceData['deguestJusoSubId'];

	var guestIsShop = vehicleGuestDistanceData['guestIsShop'];
	var deguestIsShop = vehicleGuestDistanceData['deguestIsShop'];

	var guestDate = vehicleGuestDistanceData['guestDate'];
	var deguestDate = vehicleGuestDistanceData['deguestDate'];

	var updateDate = vehicleGuestDistanceData['updateDate'];

	var status = vehicleGuestDistanceData['status'];

	var strSerialize = '';
	var strTotalDistance;


	if(guestLon==deguestLon && guestLat==deguestLat){
		setEqualLocation(vehicleGuestDistanceData,i,len);
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
			debugger;
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
								,"deguestId":deguestId
								,"guestLon":guestLon
								,"guestLat":guestLat
								,"deguestLon":deguestLon
								,"deguestLat":deguestLat
								,"guestJusoSubId":guestJusoSubId
								,"deguestJusoSubId":deguestJusoSubId
								,"status":status
								,"guestDate":guestDate
								,"deguestDate":deguestDate
								,"updateDate":updateDate
								,"guestIsShop":guestIsShop
								,"deguestIsShop":deguestIsShop
								,"distance":result
								,"jsonData":jsonData
							}
					,type: "POST"
					,async : true
					,dataType : "json"
					,success: function( json ) {
					}
					,error: function( xhr, status ) {
					}
					,complete: function( xhr, status ) {
						setLoadingNum(i, len);
					}
				});
			}
		}

	});
	return result;
}

//주소지점 사이 위경도 값이 서로 동일한경우 거리 0처리
function setEqualLocation(vehicleGuestDistanceData,i,len){

	var guestLon = vehicleGuestDistanceData['guestLon'];
	var guestLat = vehicleGuestDistanceData['guestLat'];
	var deguestLon = vehicleGuestDistanceData['deguestLon'];
	var deguestLat = vehicleGuestDistanceData['deguestLat'];

	var guestId = vehicleGuestDistanceData['guestId'];
	var deguestId = vehicleGuestDistanceData['deguestId'];

	var guestJusoSubId = vehicleGuestDistanceData['guestJusoSubId'];
	var deguestJusoSubId = vehicleGuestDistanceData['deguestJusoSubId'];

	var guestIsShop = vehicleGuestDistanceData['guestIsShop'];
	var deguestIsShop = vehicleGuestDistanceData['deguestIsShop'];

	var guestDate = vehicleGuestDistanceData['guestDate'];
	var deguestDate = vehicleGuestDistanceData['deguestDate'];

	var updateDate = vehicleGuestDistanceData['updateDate'];

	var status = vehicleGuestDistanceData['status'];

	$.ajaxSettings.traditional = true;
	$.ajax({
		url: "ajax/vehicleGuestMutualDistanceProc.ajax.php"
		,data: {
					"guestId":guestId
					,"deguestId":deguestId
					,"guestLon":guestLon
					,"guestLat":guestLat
					,"deguestLon":deguestLon
					,"deguestLat":deguestLat
					,"guestJusoSubId":guestJusoSubId
					,"deguestJusoSubId":deguestJusoSubId
					,"status":status
					,"guestDate":guestDate
					,"deguestDate":deguestDate
					,"updateDate":updateDate
					,"guestIsShop":guestIsShop
					,"deguestIsShop":deguestIsShop
					,"distance":"0"
					,"jsonData":""
				}
		,type: "post"
		,async : true
		,dataType : "json"
		,success: function( json ) {
		}
		,error: function( xhr, status ) {
		}
		,complete: function( xhr, status ) {
			setLoadingNum(i,len);
		}
	});
}

// 로딩 텍스트 변경
function setLoadingNum(i,len){
	$("#loadingDataNum").text(i+" / "+len);

	if(i==len-1){
		$("#loadingDataNum").text("배송지 좌표정보 업데이트 완료하였습니다.");
		$("#loading").modal("hide");
		window.location.reload();
	}
}


/**************************************************배차 완료 Y,N UPDATE********************************************************/
function updateVehicleAllocateResult(is){

	$.ajaxSettings.traditional = true;
	$.ajax({
		url: "ajax/vehicleAllocateResultDataProcSe.ajax.php"
		,data: {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"meridiemFlag":meridiemFlag
					, "vehicleEndStatus":is
		}
		,type: "post"
		,async : true
		,dataType : "json"
		,success: function( json ) {
			if(is=='Y'){
				alert("배차가 완료 되었습니다.");
				var input = "<button class='btn btn-lg btn-new-find' style='width:100%;'>";
				input += "배차 완료";

				$("#btnComplete").html("");
				$("#btnComplete").append(input);
				$("#btnCompleteDeliveryAdd").remove();
			} else {
				window.location.href = "setCarTest.html?step=6&deliveryDate="+deliveryDate+"&meridiemType="+meridiemType;
			}
		}
		,error: function( xhr, status ) {
			//alert("error" + xhr.status);
		}
		,complete: function( xhr, status ) {  }
	});
}


/* API구버전용
//https://developers.skplanetx.com/develop/self-console/
function jusoConvert(si, gu, dong, bunji, detail, index){

	var strSerialize = '';

	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	strSerialize += "&dong="+dong;
	//strSerialize += "&bunji="+bunji;
	//strSerialize += "&detailAddress="+detail;
	strSerialize += "&addressFlag="+"F01";
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";

	//strSerialize += "version=1&format=json";
	//strSerialize += "&city_do="+"대구광역시";
	//strSerialize += "&gu_gun="+"달서구";
	//strSerialize += "&dong="+"한들로 31";
	//strSerialize += "&detailAddress="+"장기누림타운";
	//strSerialize += "&addressFlag="+"F02";
	//strSerialize += "&appKey="+"06691986-7184-3eca-b90e-ea79adf225cd";
	//strSerialize += "&coordType=WGS84GEO";

	var result;
	$.ajax({
		type: "GET"
		,url: "https://apis.skplanetx.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//alert(JSON.stringify(data));
			//alert(data.coordinateInfo.newLat+","+data.coordinateInfo.newLon);
			resultJusoLatLon(data.coordinateInfo.lat,data.coordinateInfo.lon,index);
		}
		,error: function( xhr, status ) {
			alert(JSON.stringify(xhr));
		}
		,complete : function(){

		}

	});
}
*/

function jusoConvert(si, gu, dong, bunji, detail, index){

	var strSerialize = '';

	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	//strSerialize += "&dong="+dong;
	strSerialize += "&dong=" + dong + ' ' + bunji + ' ' + detail;
	strSerialize += "&addressFlag="+"F00";		// F00:구분없음, F01:구주소(지번),	F02:신주소(도로명주소)
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";

	var result;
	$.ajax({
		type: "GET"
		,url: "https://api2.sktelecom.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//resultJusoLatLon(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			if( (data.coordinateInfo.lat !="") && (data.coordinateInfo.lon != "") ){
				resultJusoLatLon(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			} else {
				resultJusoLatLon(data.coordinateInfo.newLat, data.coordinateInfo.newLon, index);
			}
		}
		,error: function( xhr, status ) {
			alert(JSON.stringify(xhr));
		}
		,complete : function(){

		}

	});
}

/* API구버전용
function jusoConvertNewAddr(si, gu, dong, bunji, detail, index){
	var strSerialize = '';

	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	strSerialize += "&dong="+dong;
	//strSerialize += "&bunji="+bunji;
	//strSerialize += "&detailAddress="+detail;
	strSerialize += "&addressFlag="+"F01";
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";

	//strSerialize += "version=1&format=json";
	//strSerialize += "&city_do="+"대구광역시";
	//strSerialize += "&gu_gun="+"달서구";
	//strSerialize += "&dong="+"한들로 31";
	//strSerialize += "&detailAddress="+"장기누림타운";
	//strSerialize += "&addressFlag="+"F02";
	//strSerialize += "&appKey="+"06691986-7184-3eca-b90e-ea79adf225cd";
	//strSerialize += "&coordType=WGS84GEO";

	var result;
	$.ajax({
		type: "GET"
		,url: "https://apis.skplanetx.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//alert(JSON.stringify(data));
			//alert(data.coordinateInfo.newLat+","+data.coordinateInfo.newLon);
			resultJusoLatLonNew(data.coordinateInfo.lat,data.coordinateInfo.lon,index);

		}
		,error: function( xhr, status ) {
			if(xhr.status == 400){
				alert('정확한 주소를 입력하세요.');
			}
			alert(JSON.stringify(xhr));
		}
		,complete : function(){

		}

	});
}
*/

function jusoConvertDelivery(si, gu, dong, bunji, detail, index){

	var strSerialize = '';
	/*
	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	strSerialize += "&dong="+dong;
	strSerialize += "&addressFlag="+"F02";
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";
	*/

	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	//strSerialize += "&dong="+dong;
	strSerialize += "&dong=" + dong + ' ' + bunji + ' ' + detail;
	strSerialize += "&addressFlag="+"F00";		// F00:구분없음, F01:구주소(지번),	F02:신주소(도로명주소)
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";



	var result;
	$.ajax({
		type: "GET"
		,url: "https://api2.sktelecom.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//resultJusoLatLonDelivery(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			if( (data.coordinateInfo.lat !="") && (data.coordinateInfo.lon != "") ){
				resultJusoLatLonDelivery(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			} else {
				resultJusoLatLonDelivery(data.coordinateInfo.newLat, data.coordinateInfo.newLon, index);
			}
		}
		,error: function( xhr, status ) {
			if(xhr.status == 400){
				alert('정확한 주소를 입력하세요.');
			}
			alert(JSON.stringify(xhr));
		}
		,complete : function(){

		}
	});
}


// 주소 경로 검색 - setCarListPreview.html
function searchJusoNewRe(strJuso, strAccno, nIndex, nLength){

	/*
	if($("#deguestJuso"+nIndex).val() == ''){
		alert('주소를 입력하세요.');
		return;
	}
	var arrJuso = $("#deguestJuso"+nIndex).val().split(" ");
	*/

	//alert(strJuso);

	if(strJuso == ''){
		alert('주소정보가 누락되었습니다.');
		return;
	}
	var arrJuso = strJuso.split(" ");


	//for(i=0;i<arrJuso.length;i++){
	//alert(arrJuso[i]);
	//}

	var si		= arrJuso[0];	//
	var gu		= arrJuso[1];
	var dong	= arrJuso[2];
	var bunji	= arrJuso[3];
	var detail	= arrJuso[4] + ' ' + arrJuso[5];

	jusoConvertNewAddrRe(strAccno, si, gu, dong, bunji, detail, nIndex, nLength);


}

//주소 경로 검색
function searchJusoNew(nIndex){

	if($("#deguestJuso"+nIndex).val() == ''){
		alert('주소를 입력하세요.');
		return;
	}
	var arrJuso = $("#deguestJuso"+nIndex).val().split(" ");

	//for(i=0;i<arrJuso.length;i++){
	//alert(arrJuso[i]);
	//}

	var si		= arrJuso[0];	//
	var gu		= arrJuso[1];
	var dong	= arrJuso[2];
	var bunji	= arrJuso[3];
	var detail	= arrJuso[4] + ' ' + arrJuso[5];

	jusoConvertNewAddr(si, gu, dong, bunji, detail, nIndex);

}

function jusoConvertNewAddrRe(accno, si, gu, dong, bunji, detail, index, length){

	var strSerialize = '';
	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	//strSerialize += "&dong="+dong;
	//strSerialize += "&addressFlag="+"F02";
	strSerialize += "&dong=" + dong + ' ' + bunji + ' ' + detail;
	strSerialize += "&addressFlag="+"F00";		// F00:구분없음, F01:구주소(지번),	F02:신주소(도로명주소)
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";

	var result;
	$.ajax({
		type: "GET"
		,url: "https://api2.sktelecom.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//resultJusoLatLonNew(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			if( (data.coordinateInfo.lat !="") && (data.coordinateInfo.lon != "") ){

				//resultJusoLatLonNew(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
				// ERP Update gogogo!!
				//alert(data.coordinateInfo.lat + "," + data.coordinateInfo.lon);
				erpUpdateGeoCoding(accno, data.coordinateInfo.lat, data.coordinateInfo.lon);

			} else {

				//resultJusoLatLonNew(data.coordinateInfo.newLat, data.coordinateInfo.newLon, index);
				erpUpdateGeoCoding(accno, data.coordinateInfo.newLat, data.coordinateInfo.newLon);

			}


		}
		,error: function( xhr, status ) {
			if(xhr.status == 400){
				alert('정확한 주소를 입력하세요.');
			}
			//alert(JSON.stringify(xhr));
		}
		,complete : function(){
			setLoadingNum(index, length);
		}
	});
}


function erpUpdateGeoCoding(accno, lat, lon){

	// 순서안바뀌게 주의!!!
	// DevX : lon
	// DevY : lat


	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/baedal_orderlistGeoUpdate.ajax.php"
		,async : true	// 동기화처리
		,data : {
					"accno":accno
					,"lat":lat
					,"lon":lon
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(result){
		}
		,complete : function(){}
	});

}

function jusoConvertNewAddr(si, gu, dong, bunji, detail, index){

	var strSerialize = '';
	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	//strSerialize += "&dong="+dong;
	//strSerialize += "&addressFlag="+"F02";
	strSerialize += "&dong=" + dong + ' ' + bunji + ' ' + detail;
	strSerialize += "&addressFlag="+"F00";		// F00:구분없음, F01:구주소(지번),	F02:신주소(도로명주소)
	strSerialize += "&appKey="+apiKey;
	strSerialize += "&coordType=WGS84GEO";

	var result;
	$.ajax({
		type: "GET"
		,url: "https://api2.sktelecom.com/tmap/geo/geocoding"
		,data : strSerialize
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){
			//resultJusoLatLonNew(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			if( (data.coordinateInfo.lat !="") && (data.coordinateInfo.lon != "") ){
				resultJusoLatLonNew(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
			} else {
				resultJusoLatLonNew(data.coordinateInfo.newLat, data.coordinateInfo.newLon, index);
			}
		}
		,error: function( xhr, status ) {
			if(xhr.status == 400){
				alert('정확한 주소를 입력하세요.');
			}
			alert(JSON.stringify(xhr));
		}
		,complete : function(){

		}
	});
}

function onlyNumber(obj) {
    $(obj).keyup(function(){
         $(this).val($(this).val().replace(/[^0-9]/g,""));
    });
}



//***********************************************이승우*******************************************************//
function getReRouteByTmap(viaPoints, refDeliveryDate, refMeridiemType, refMeridiemFlag, refVehicleNo) {
	var locations = {};
	for(let i=0; i < viaPoints.length; i++){
		locations[viaPoints[i].viaPointId] = {}
		locations[viaPoints[i].viaPointId].lat = viaPoints[i].viaX
		locations[viaPoints[i].viaPointId].lon = viaPoints[i].viaY
	}

	debugger;

	var start = {lat: "35.929894", lon: "128.539506"};

	var headers = {};
	headers["appKey"] = "0de9ecde-b87c-404c-b7f8-be4ed7b85d4f";

	$.ajax({
		type: "POST",
		headers: headers,
		url: "https://apis.openapi.sk.com/tmap/routes/routeOptimization30?version=1&format=json",
		async: false,
		contentType: "application/json",
		data: JSON.stringify({
			"reqCoordType": "WGS84GEO",
			"resCoordType": "EPSG3857",
			"startName": "출발",
			"startX": start.lon,
			"startY": start.lat,
			"startTime": "201711121314",
			"endName": "도착",
			"endX": start.lon,
			"endY": start.lat,
			"searchOption": "0",
			"viaPoints": viaPoints
		}),
		success: function (response) {
            var jsonData = JSON.stringify(response); // index가 0인 첫번째에 들어감
            var indexs = [];
			var resultFeatures = response.features;
			var totalDistance = response.properties.totalDistance;

			for (var i in resultFeatures) {
				var geometry = resultFeatures[i].geometry;
				var properties = resultFeatures[i].properties;

				if (geometry.type === "Point") {
				    console.log(properties.viaPointId, properties.index);
				    if(locations[properties.viaPointId]){
						temp = {id: properties.viaPointId, index: properties.index};
						indexs.push(temp);
					}
				}
			}

			debugger;

			$.ajax({
				type : "POST"
				,url : "../process/my_insertResult.ajax.php"
				,async : true		// 동기화처리
				,data : {
				    "jsonData": jsonData
				    ,"indexs": indexs
				    ,"totalDistance": totalDistance
                    ,"deliveryDate":refDeliveryDate
					,"locationId":locationId
					,"meridiemType":refMeridiemType
					,"meridiemFlag":refMeridiemFlag
					,"vehicleNo":refVehicleNo
				}
				,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
				,success : function(data){
					removeRoute();
					simpleLoadVehicleList();
					// window.location.reload();
					$("#loading").modal("hide");
				}
				,error: function( xhr, status ) {$("#loading").modal("hide");}
				,complete : function(){ }
			});

		},
		error: function (request, status, error) {
			console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
		}
	});

}
