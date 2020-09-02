
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

var map;

// 
//var ArrPoint		= new Array();	// 
//var ArrPointData	= new Array();	//



//var ArrPoint = new Array();
//var ArrLayerMarks = new Array();

var CrsArrPoint;
var CrsArrLayerMarks;
var CrsArrLayerRoute;

var CrsTargetLayer;
var CrsTargetLat = '';	// 목적지 lat
var CrsTargetLon = '';	// 목적지 lon
var CrsTargetAddr = '';	// 목적지 lon


var nLoopIndex = 0;


function LoadCrsArrPointSimple(){
}


function getAddrToGeo(){


	CrsTargetAddr = '';

	var strSerialize = '';

	strSerialize += "version=1";
	strSerialize += "&addressFlag=F00";
	strSerialize += "&coordType=WGS84GEO";
	strSerialize += "&callback=";
	strSerialize += "&appKey=" + apiKey;

	if($('#searchCity_do').val() != "")strSerialize += "&city_do=" + encodeURIComponent($('#searchCity_do').val());						// city_do
	if($('#searchGu_gun').val() != "")strSerialize += "&gu_gun=" + encodeURIComponent($('#searchGu_gun').val());						// gu_gun
	if($('#searchDong').val() != "")strSerialize += "&dong=" + encodeURIComponent($('#searchDong').val());								// dong
	if($('#searchBunji').val() != "")strSerialize += "&bunji=" + encodeURIComponent($('#searchBunji').val());							// bunji
	if($('#searchDetailAddress').val() != "")strSerialize += "&detailAddress=" + encodeURIComponent($('#searchDetailAddress').val());	// detailAddress

	//alert(strSerialize);

	CrsTargetAddr = $('#searchCity_do').val() + ' ' + $('#searchGu_gun').val() + $('#searchDong').val() + ' ' + $('#searchBunji').val();

	$.ajax({
		type : "GET"
		,url : "https://api2.sktelecom.com/tmap/geo/geocoding"
		//,data : $('#update_OnlineForm').serialize()
		,data : strSerialize
		,dataType : "json"
		,beforeSend : function(xhr){
			//xhr.setRequestHeader("appKey","<?php echo $define_APIKEY; ?>");
            //xhr.setRequestHeader("Content-type","application/xml");
        }
		,success : function(data) {

			if(data != null){

				var jsonData = JSON.stringify(data);
				var objFeatures = data['coordinateInfo'];
				
				if(CrsTargetLayer != undefined && CrsTargetLayer != ''){
					//alert('ffff');
					map.removeLayer(CrsTargetLayer);
				} else {
					//alert('empty');
				}
				CrsTargetLat = '';
				CrsTargetLon = '';
				CrsTargetLayer = '';


				CrsTargetLon = objFeatures['lon'];
				CrsTargetLat = objFeatures['lat'];

				//alert(CrsTargetLon + ',' + CrsTargetLat);

				var markers = new Tmap.Layer.Markers("MarkerLayer");
				map.addLayer(markers);

				//var tmpLonLat = CrsTargetLon + ',' + CrsTargetLat;
				//var tmpLonLat = "lon=128.624514,lat=35.865767";
				//tmpLonLat.transform(pr_4326, pr_3857);

				var lonlat = new Tmap.LonLat(CrsTargetLon, CrsTargetLat).transform(pr_4326, pr_3857);

				//var lonlat = map.getLonLatFromViewPortPx(ddd);
				var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
				var size = new Tmap.Size(26,38);
				var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
				var span="<span style='"+style+"'>D</span>";
				var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_0.png" /></div>', size, offset);
				var marker = new Tmap.Marker(lonlat, iconHtml);
				markers.addMarker(marker);

				CrsTargetLayer = markers;

				alert('목적지가 선택되었습니다.\n' + CrsTargetAddr);



			} else {
				alert('결과데이터가 없습니다.\n다시검색해주세요.');
				return;
			}

		}
		,error: function( xhr, status ) {
			alert('Error :\n결과데이터가 없습니다.\n다시검색해주세요.');
		}

	});
}

function ReverseTargetLatLon(refLat, refLon){

	var startY = refLat;
	var startX = refLon;

	var urlStr = '';

	//var endX = 128.714732;
	//var endY = 35.866793;

	urlStr += "version=1&format=json";					// param version
	//urlStr += "&callback=";							// param callback
	urlStr += "&lon="+startX;							// param Lon
	urlStr += "&lat="+startY;							// param Lat
	urlStr += "&addressType="+"A00";					// param addressType
	urlStr += "&appKey="+apiKey;						// param api
	urlStr += "&coordType=WGS84GEO";

	$.ajax({
		type: "GET"
		,url : "https://api2.sktelecom.com/tmap/geo/reversegeocoding"
		,data : urlStr
		,async: true
		,dataType : "json"
		,beforeSend : function(xhr){
		}
		,success: function(data){

			//alert('ok');
			//alert(data.addressInfo.fullAddress);
			//fullAddress=대구광역시 수성구 범어4동,대구광역시 수성구 범어동 263-1

			CrsTargetAddr = data.addressInfo.fullAddress;
			alert('목적지 : ' + CrsTargetAddr);

			/*
			var city_do = data.addressInfo.city_do;
			if(city_do=='대구광역시' || city_do=='경상북도'){
				alert(city_do);
			} else {
				alert(city_do);
			}
			*/
		}
		,error: function( xhr, status ) {
			alert('error');
			if(xhr.status=='400'){
			}
		}
		,complete : function(){

		}
	});


}


function TimedLoopSearch(){

	setTimeout(function () {
					SearchProc(nLoopIndex);
					nLoopIndex++;
					if(nLoopIndex < CrsArrPoint.length) {
						TimedLoopSearch();
					} else {
						//
					}
				}, 600);
}

function SearchStart(){

	if(CrsArrLayerMarks == undefined || CrsArrLayerMarks == ''){
		alert('지점을 선택하세요.');
		return;
	}

	if(CrsTargetLat == '' || CrsTargetLon == ''){
		alert('목적지를 선택하세요.\n지도상에 마우스 왼쪽 버튼을 클릭하거나, 목적지검색을 이용하세요.');
		return;
	}


	// 기존 경로가 있으면 삭제
	// 경로지우기
	if(CrsArrLayerRoute != null){
		for(i=0;i<CrsArrLayerRoute.length;i++){
			map.removeLayer(CrsArrLayerRoute[i]);
		}

		DelResult();
	}

	nLoopIndex = 0;
	TimedLoopSearch();
}


function SearchProc(nSearchIndex){

	//return;

	var deguestLat = CrsTargetLat;
	var deguestLon = CrsTargetLon;

	var guestLat = "";
	var guestLon = "";

	var strSerialize = '';



	/*
	for(i=0;i<CrsArrPoint.length;i++){
		alert('Search point : ' + CrsArrPoint[i]['lat'] + ', ' + CrsArrPoint[i]['lon']);
	}
	*/

	//alert(apiKey);

	var nIndex= 0;

		guestLat = CrsArrPoint[nSearchIndex]['lat'];
		guestLon = CrsArrPoint[nSearchIndex]['lon'];

		strSerialize = '';

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
					
					//alert(result);
					//alert(jsonData);

					//CrsArrLayerRoute[nSearchIndex] = jsonData;
					LoadCrsArrLayerRoute(jsonData, nSearchIndex);

					AddResult(nSearchIndex, strTotalDistance);

					map.removeLayer(CrsArrLayerMarks[nSearchIndex]);
					map.addLayer(CrsArrLayerMarks[nSearchIndex]);

					// 마지막검색이면, 목적지마크 한번더 그려주긔
					if(nSearchIndex == (CrsArrPoint.length -1)){
						map.removeLayer(CrsTargetLayer);
						map.addLayer(CrsTargetLayer);
					}

				} else {
					alert('Search Error');
				}

			}
			
		});

	//LoadCrsArrLayerRoute();
}

function LoadCrsArrLayerRoute(strData, nIndex){


	var styleMap = new Tmap.StyleMap({'default': new Tmap.Style({
																pointColor: routeColor[nIndex],
																pointRadius: 5,
																//stroke : false,
																strokeColor: routeColor[nIndex],
																strokeWidth: 4,
																strokeOpacity: 4,
																strokeLinecap : "square",
																graphicZIndex: nIndex
															})
								});
	vectorLayer = new Tmap.Layer.Vector("vector", {styleMap:styleMap});
	//vectorLayer.events.register("featuresadded", vectorLayer, onDrawnFeatures); // 그리기 완료 이벤트 생성

	map.addLayer(vectorLayer);
	
	var geoForm = format.read(strData);
	
	// 변환된 데이터를 레이어에 그립니다
	// 그리고 싶은 부분만 발췌해서 그리는 것도 가능합니다
	vectorLayer.addFeatures(geoForm);

	//alert('LoadCrsArrLayerRoute End');

	CrsArrLayerRoute[nIndex] = vectorLayer;

}

function AddResult(nIndex, strDistance){

	var field_idObj		= "obj_Account";
	var field_tr_cnt	= 0;
	var tr_cnt			= $("tr[id^=" + field_idObj + "]").length;
    if(typeof($('#'+field_idObj).find('tr:last').attr('id')) != "undefined") {
    	field_tr_cnt	= $('#'+field_idObj).find('tr:last').attr('id').replace(field_idObj,'');
    }
	var field_no = parseInt(tr_cnt + 1);
	var add_html = "";

	add_html += '<tr id="' + field_idObj + field_no +'">';
		add_html += '<td>' + $('#arrName' + String(nIndex)).val() + '</td>';
		add_html += '<td>' + CrsArrPoint[nIndex]['lat'] + '</td>';
		add_html += '<td>' + CrsArrPoint[nIndex]['lon'] + '</td>';
		add_html += '<td>' + CrsTargetAddr + '</td>';
		add_html += '<td>' + (parseInt(strDistance) * 0.001) + 'km</td>';
	add_html += '</tr>';

	$('#' + field_idObj).append(add_html);

	//alert('test11');
}

function DelResult(){

	var field_idObj		= "obj_Account";
	var tr_cnt			= $("tr[id^=" + field_idObj + "]").length;

	for(i=0;i<tr_cnt;i++){
		$('#' + field_idObj + (i+1) +' *').remove();
	}



}

// 
function LoadCrsArrPoint(){

	//alert(CrsArrLayerMarks);

	if(CrsArrLayerMarks != null){
		//RemoveCrsArrPoint();
		for(i=0;i<CrsArrLayerMarks.length;i++){
			map.removeLayer(CrsArrLayerMarks[i]);
		}
	}

	if(CrsArrLayerRoute != null){
		for(i=0;i<CrsArrLayerRoute.length;i++){
			map.removeLayer(CrsArrLayerRoute[i]);
		}
	}

	CrsArrPoint = new Array();
	CrsArrLayerMarks = new Array();
	CrsArrLayerRoute = new Array();


	//alert($('input:checkbox[name=checkArr]:checked').length);


	//alert($('input:checkbox[name=checkArr]:checked').length  + '/' + $('input:checkbox[name=checkArr]').length);

	if($('input:checkbox[name=checkArr]:checked').length == 0){
		//alert('지점을 선택하세요.');
		return
	}

	//ArrPoint = new Array();
	nIndex = 0;
	$('input:checkbox[name=checkArr]:checked').each(function(i, elements){

		CrsArrPoint[nIndex] = new Array();
		index = $(elements).index('input:checkbox[name=checkArr]');
		//alert($('#arrlon' + index).val());
		CrsArrPoint[nIndex]['lat'] = $('#arrlat' + index).val();
		CrsArrPoint[nIndex]['lon'] = $('#arrlon' + index).val();
		CrsArrPoint[nIndex]['index'] = String(index);
		nIndex++;
	});

	// 지도에 뿌려보자규
	for(i=0;i<CrsArrPoint.length;i++){
		//alert(ArrPoint[i]['lon'] + ',' + ArrPoint[i]['lat']);
		DisplayCrsArrPoint(CrsArrPoint[i]['lat'], CrsArrPoint[i]['lon'], i, CrsArrPoint[i]['index']);
	}
}

function DisplayCrsArrPoint(varLat, varLon, nIndex, strIndex){

	//마커
	var markers = new Tmap.Layer.Markers("MarkerLayer");
	map.addLayer(markers);

	/*******************************************마커 커스텀 시작*******************************************************/
	var size = new Tmap.Size(26,38);
	var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
	var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";

	//var span="<span style='"+style+"'>S</span>";
	var span="<span style='"+style+"'>" + (parseInt(strIndex)+1) + "</span>";
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_green.png" /></div>', size, offset);
		
	/*******************************************마커 커스텀 끝*******************************************************/
	var lat = varLat;	
	var lon = varLon;
	var marker = new Tmap.Marker(new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857), iconHtml);
	var popup;
	var html = "" + "<div style='display:none'><a href=''>자세히보기</a></div>";
	popup = new Tmap.Popup("0",
				new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857),
				new Tmap.Size(300, 80),
				html,
				true
				); 

	arrPopup[0] = popup;
	map.addPopup(popup);
	popup.hide();

	//marker.events.register("click", popup, onOverMarker);
	markers.addMarker(marker);

	CrsArrLayerMarks[nIndex] = markers;


}

function CheckCrsArrPoint(){

	$("#checkAll").click(function(){
		if($('#checkAll').prop("checked")){
			$("input:checkbox[name=checkArr]").prop("checked", true);
			LoadCrsArrPoint()();
		} else {
			$("input:checkbox[name=checkArr]").prop("checked", false);
			RemoveCrsArrPoint();
		}
	});


}

function UnCheckCrsArrPoint(){

}

// 지점삭제
function RemoveCrsArrPoint(){
	
	// 지점삭제시 채크해제
	$("input:checkbox[name=checkArr]").prop("checked", false);

	// mark 지우기
	if(CrsArrLayerMarks != null){
		for(i=0;i<CrsArrLayerMarks.length;i++){
			map.removeLayer(CrsArrLayerMarks[i]);
		}
	}

	// 경로지우기
	if(CrsArrLayerRoute != null){
		for(i=0;i<CrsArrLayerRoute.length;i++){
			map.removeLayer(CrsArrLayerRoute[i]);
		}
	}

	// 목적지 레이어 지우긔
	map.removeLayer(CrsTargetLayer);

	CrsArrPoint = new Array();
	CrsArrLayerMarks = new Array();
	CrsArrLayerRoute = new Array();

	CrsTargetLayer = '';
	CrsTargetLat = '';
	CrsTargetLon = '';

	DelResult();

}


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
						height:'568px',
						transitionEffect:"resize",
						animation:true
					}); 

	format = new Tmap.Format.GeoJSON({
											'internalProjection': map.baseLayer.projection,
											'externalProjection': new Tmap.Projection("EPSG:3857")
										});

	//markers = new Tmap.Layer.Markers("MarkerLayer");
	updateFormats();
	map.setCenter(new Tmap.LonLat(128.60174282201098, 35.87143069747235).transform(pr_4326, pr_3857), 13);	// center위치 대구광역시청
	map.events.register("click", map, onClickMap);

};

function onClickMap(e){

	//alert("[" + CrsTargetLayer + "]");

	if(CrsTargetLayer != undefined && CrsTargetLayer != ''){
		//alert('ffff');
		map.removeLayer(CrsTargetLayer);
	} else {
		//alert('empty');
	}

	//alert('ddd');

	// 목적지좌표 초기화
	CrsTargetLat = '';
	CrsTargetLon = '';
	CrsTargetLayer = '';


	var markers = new Tmap.Layer.Markers("MarkerLayer");
	map.addLayer(markers);

	//alert('선택된 좌표지 정보');
	var lonlat = map.getLonLatFromViewPortPx(e.xy);
	var tmpLonLat = map.getLonLatFromViewPortPx(e.xy);

	var style = "position:absolute; z-index:1; color:#000; margin:3px 0 0 0; width:100%; text-align:center; font-weight:bold;";
	var size = new Tmap.Size(26,38);
	var offset = new Tmap.Pixel(-(size.w/2), -(size.h/2));
	var span="<span style='"+style+"'>D</span>";
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_0.png" /></div>', size, offset);
	var marker = new Tmap.Marker(lonlat, iconHtml);
	markers.addMarker(marker);

	CrsTargetLayer = markers;

	//var tmpLonLat = lonlat;
	tmpLonLat.transform(pr_3857, pr_4326);

	var splitLonLat = String(tmpLonLat);
	splitLonLat = splitLonLat.split(',');	//lon=128.63453014500803,lat=35.87198710766598

	splitLon = splitLonLat[0].split('=');
	splitLat = splitLonLat[1].split('=');

	CrsTargetLon = splitLon[1];
	CrsTargetLat = splitLat[1];

	//alert(CrsTargetLat + ',' + CrsTargetLon);

	ReverseTargetLatLon(CrsTargetLat, CrsTargetLon);

}



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
					"jum":"6"			// 11:반야월, 6:월배 and so on....
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
								"<td align=\"center\">" + resultOrderListData['item'][i]['accno'] + "</td>" +
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

/*
//지도 데이터 가져오기 로컬 테스트용
function previewAjaxGetOrderData(erpUrl, memeType){
	
	var vehicleErpUrl = erpUrl;

	$.ajax({
		type : "GET"
//		,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php"
//		,url : vehicleErpUrl
		,url			: "api/vehicleBaedalList.php"
		,async : false		// 동기화처리
//		,dataType : "jsonp"
		,dataType : "json"
		,crossDomain: true
//		,jsonpCallback: "callback"
		,data : {
					"jum":"6"			// 11:반야월, 6:월배 and so on....
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
								"<td align=\"center\">" + resultOrderListData['item'][i]['accno'] + "</td>" +
								"<td align=\"left\" style=\"cursor:pointer\">" + resultOrderListData['item'][i]['juso'] + "</td>" +
							 "</tr>";
			}

			var strMeridiemType = "";
			var date = new Date();

			if(memeType == "a"){
				strMeridiemType = date.getFullYear() + "년 " + date.getMonth() + "월 " + date.getDate() + "일 오전 배송지역";
			} else {
				strMeridiemType = date.getFullYear() + "년 " + date.getMonth() + "월 " + date.getDate() + "일 오후 배송지역";
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
*/

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
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_0.png" /></div>', size, offset);
		
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

	// UI변경은 
	allMapView(strCheckBoxRoute.slice(0, -1));	
	checkBoxRestore();
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
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_'+list['vehicleNo']+'.png" /></div>', size, offset);
		
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
	var iconHtml = new Tmap.IconHtml('<div style="text-align:center;">'+span+'<img src="http://dev.jeycorp.com/jangbogo/images/icon/marker_green.png" /></div>', size, offset);
		
	/*******************************************마커 커스텀 끝*******************************************************/

	var lon = varLon;
	var lat = varLat;	
	var marker = new Tmap.Marker(new Tmap.LonLat(lon, lat).transform(pr_4326, pr_3857), iconHtml);
	var popup;
	var html = "" + "<div style='display:none'><a href=''>자세히보기</a></div>";
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
								"<td align='center'><input type='text' id='deguestJuso"+i+"' name='deguestJuso"+i+"' size='60' value='" + item['Juso'] + "' class='form-control' /></td>"+
								"<td align='center'>"+pay+"원</td>"+
								"<td align='center'><input type='text' id='deguestLat"+i+"' name='deguestLat"+i+"' size='20' value='" + item['deguestLat'] + "' class='form-control ddd' /></td>"+
								"<td align='center'><input type='text' id='deguestLon"+i+"' name='deguestLon"+i+"' size='20' value='" + item['deguestLon'] + "' class='form-control' /></td>"+
								"<td align='center'><button type='button' class='btn btn-sm btn-new-find' onClick=searchJusoNew('" + i + "')>검색</button></td>"+
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

/* API구버전용
//위도 경도 변환
function checkLatLonConvert(item, i, length){
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
				ajaxGuestMutualDistanceDataGet();
			}
		}
	});
}
*/

//위도 경도 변환
function checkLatLonConvert(item, i, length){

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
		,url: "https://api2.sktelecom.com/tmap/geo/reversegeocoding"
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
				ajaxGuestMutualDistanceDataGet();
			}
		}
	});
}
//step06. 위 경도 주소 확인시 문제 발견 체크 업데이트
function ajaxGuestInfoFailUpdate(guestId,guestJusoSubId,isShop){
	// ajax 실행부
	$.ajaxSettings.traditional = true;
	$.ajax({
		 type : "POST"
		,url : "ajax/checkLatLonUpdate.ajax.php"
		,async : true	// 동기화처리
		,data : {
					"deliveryDate":deliveryDate
					,"locationId":locationId
					,"meridiemType":meridiemType
					,"guestId":guestId
					,"guestJusoSubId":guestJusoSubId
					,"isShop":isShop
					,"isAddVehicle":"Y"
				}
		,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
		,success : function(result){}
		,complete : function(){}
	});
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

/* API구버전
//주소지점사이 거리 데이터 업데이트(실제)
function setDistanceListData(vehicleGuestDistanceData, i, len){

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
*/

//주소지점사이 거리 데이터 업데이트(실제)
function setDistanceListData(vehicleGuestDistanceData, i, len){

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
	strSerialize += "&dong="+dong;
	strSerialize += "&addressFlag="+"F01";
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
			resultJusoLatLon(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
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

function jusoConvertNewAddr(si, gu, dong, bunji, detail, index){

	var strSerialize = '';
	strSerialize += "version=1&format=json";
	strSerialize += "&city_do="+si;
	strSerialize += "&gu_gun="+gu;
	strSerialize += "&dong="+dong;
	strSerialize += "&addressFlag="+"F02";
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
			resultJusoLatLonNew(data.coordinateInfo.lat, data.coordinateInfo.lon, index);
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

