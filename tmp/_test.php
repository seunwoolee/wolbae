<? 
include "inc_html/header.html";
include "inc/drivingLog.inc.php";
include "inc/paging.inc.php";
include "inc/user.inc.php";



?>

<link rel="stylesheet" type="text/css" media="all" href="css/jquery.jqplot.css" />
<script type="text/javascript" src="js/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/counter.js"></script>
<script type="text/javascript" src="js/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot.logAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot.highlighter.min.js"></script>

<script>

$(document).ready(function() {

0,1
1,1
2,1
0,2
1,2
2,2
	
	var data = [['1-1','1-2','1-3'],['2-1','2-2','2-3','2-4','2-5'],['3-1','3-2','3-3','3-4','3-5'],['4-1','4-2','4-3','4-4','4-5']];

	for( var z = 1; z <data.length; z++){
		var rowLength = data[z].length;
		for(var i = 2; i < rowLength; i++){
			z = rowLength;
			console.log(data[z][i]) ;

			
		}
	}

	/*
	for ( var i = 2; i < data[1].length; i++) 
				{
					datas = data[i];
					for (var j = 1; j < data.length; j++) 
					{ 
						//costtypedistance = data[j][i];
						//aaaa = costtypedistance;
						console.log(data[j][i]) ;
						//console.log(j+","+i);
						
					}
					//console.log(datas);
				}
		*/	

});

</script>
<? include "inc_html/footer.html"; ?>
