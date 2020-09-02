<? 
include "inc_html/header.html";
?>


<div class="row">
	<div class="col-md-9">
		<div class="card">
			<div class="cardTitle">
				<span class="titCard">
					
					<a href="javascript:ajaxGetJsonData();" class="btn btn-sm btn-new-ok pull-right">json Data Update</a>
				</span>
			</div>
			<div id="map" class="cardCont" style="padding:0;">
			</div>
		</div>
	</div>
	
	
	<script>


	var dataJsonArray = new Array();




	// step02. ajax 고객배송정보 가져오긔.
	function ajaxGetJsonData(){



		$.ajax({
			type : "GET"
			,url : "api/vehicleJson.php"
			,async : false		// 동기화처리
			,dataType : "json"	// 응답의 결과로 반환되는 데이터의 종류
			,success : function(data){

				if(data != null){
					alert(data.length);
					alert(data[0]['mid']);

					$('#map').append('test1111<br>');
					$('#map').append('test2222');

					var nIndex = 0;

					alert('111');

					for(i=0;i<data.length;i++){

						if(data[i]['market'] == "월배점" || data[i]['market'] == "진천점"){

							dataJsonArray[nIndex] = new Array();

							//alert(data[i]['mid']);
							dataJsonArray[nIndex]['guestId']	= data[i]['mid'];
							dataJsonArray[nIndex]['lat']		= data[i]['pointY'];
							dataJsonArray[nIndex]['lon']		= data[i]['pointX'];
							nIndex++;

						}

					}

					alert('222');

					for(i=0;i<dataJsonArray.length;i++){
						$('#map').append(i+1);	// no
						$('#map').append(',');	// no
						$('#map').append(dataJsonArray[i]['guestId']);
						$('#map').append(',');	// no
						$('#map').append(dataJsonArray[i]['lat']);
						$('#map').append(',');	// no
						$('#map').append(dataJsonArray[i]['lon']);
						$('#map').append('<br>');
					}

					alert('test');
		
				}

			}
			,complete : function(){}	// complete Event 호출시 사용
		});
	}


	</script>
</div>

<div class="copy">
	<? include "inc_html/copy.html"; ?>
</div>

</body>
</html>