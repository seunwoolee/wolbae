<? 
include "inc_html/header.html";
?>


<div class="row">
	<div class="col-md-9">
		<div class="card">
			<div class="cardTitle">
				<span class="titCard">
					테스트 고객정보 업데이트
					<a href="javascript:GuestInfoProc();" class="btn btn-sm btn-new-ok pull-right">고객정보 업데이트</a>
				</span>
			</div>
			<div id="map" class="cardCont" style="padding:0;">
			</div>
		</div>
	</div>
	
	
	<script>
	//높이 맞추기
	var matchH;
	var matchHresize;
	$(".matchHeight").each(function(){
		var thisHeight = $(this).outerHeight();
		if (matchH){
			if(thisHeight > matchH){
				matchH = thisHeight;
			}
		} else {
			matchH = thisHeight;
		}
	});
	$(window).load(function(){
		var windowH = $(window).outerHeight();
		var headerH = $("#header").outerHeight();
		var copyH = $(".copy").outerHeight();
		var contH = windowH - ( headerH + copyH );
		//alert($("#lnb").outerHeight());
		if($("body").hasClass("pc")) {
			$("#map").height(contH - 235);
			$(".setCarList").height(contH - 505);
		}
	});


	// step01. 고객정보업데이트 모달open
	function GuestInfoProc(){

		if(confirm('고객정보를 업데이트 하시겠습니까?')){
			//$("#modal").modal({ backdrop:false } );	// 백그라운드 클릭시 창 안닫히게.
			ajaxGuestInfoGet();
		} else {
			return false;
		}		
	}

	// step02. ajax 고객정보 가져오긔.
	function ajaxGuestInfoGet(){

		$.ajax({
			type : "GET"
			,url : "http://www.jangboja.com/shop/partner/baedal_orderlist.php?jum=10&gbn=a"	// ERP서버측 URL로 변경해야됩니다.(현재 테스트용이므로, 로컬서버경로를 입력하여 테스트중입니다.)
			//,url : "http://aceone76.cafe24.com/json.php?a=1&b=1"	// ERP서버측 URL로 변경해야됩니다.(현재 테스트용이므로, 로컬서버경로를 입력하여 테스트중입니다.)
			,async : false		// 동기화처리
			,dataType : "jsonp"	// 응답의 결과로 반환되는 데이터의 종류
			,crossDomain: true
			,jsonpCallback : "callback"
			,success : function(data){

				alert(data);

				/*
				alert('succ 1');
				var obj = JSON.parse(data);

				$.each(obj, function(key, val){
					alert(obj);
				});
				alert('succ 2');
				ajaxGuestInfoUpdate(data);
				*/
				alert('succ 1');
				ajaxGuestInfoUpdate(data);
				alert('succ 2');
			}
			,done : function(){
				alert('done');
			}
			,complete : function(data){

				ajaxGuestInfoUpdate(data);

			}	// complete Event 호출시 사용
			,error : function(request, status, error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	// step03. ajax 고객정보 업데이트
	function ajaxGuestInfoUpdate(jsonDataGuest){

	
		// ajax 실행부
		$.ajaxSettings.traditional = true;
		$.ajax({
			 type : "POST"
			,url : "ajax/tmpVehicle3.ajax.php"
			,async : false	// 동기화처리
			,data : JSON.stringify(jsonDataGuest)
//			,data : jsonDataGuest
			,contentType : "application/json"
			,success : function(result){
				alert(result);
			}
			,complete : function(){}
		});
	}


	</script>
</div>

</article>

<div class="copy">
	<? include "inc_html/copy.html"; ?>
</div>

<!-- Modal -->
<!-- <form name="submitForm" method="post" action="post/dutyPost.php" class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="newDepLabel" aria-hidden="true"> -->
<div class="modal fade" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true" id="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">고객정보 업데이트 실행중</h4>
        </div>
        <div class="modal-body">
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
          고객정보를 업데이트 중입니다.<br>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

</body>
</html>