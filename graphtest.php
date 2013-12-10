<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Points testing</title>
		<script src="js/jquery.js"></script>
		<script src="js/jquery.flot.js"></script>
		<script>
			$(document).ready(function() {
				function get_random_color() {
					var letters = '0123456789ABCDEF'.split('');
					var color = '#';
					for (var i = 0; i < 6; i++) {
						color += letters[Math.round(Math.random() * 15)];
					}
					return color;
				}
				var data = [123,
					[{"label": "S1", data: [[0, 6], [15, 0]]},
						{label: "S2", data: [[0, 8.6666666666667], [13, 0]]},
						{label: "S3", data: [[0, 5], [15, 5]]},
						{label: "gradient", data: [[0, 0], [3.75, 11.25]]},
						{label: "A", data: [[2.5, 5]], points: {show: true}, color: get_random_color()}
					]];
				var data2=[2,"max 2x<sub>1<\/sub>+6x<sub>2<\/sub><br\/>2x<sub>1<\/sub>+5x<sub>2<\/sub>+1x<sub>3<\/sub>+0x<sub>4<\/sub>+0x<sub>5<\/sub><=30<br\/>2x<sub>1<\/sub>+3x<sub>2<\/sub>+0x<sub>3<\/sub>+1x<sub>4<\/sub>+0x<sub>5<\/sub><=26<br\/>0x<sub>1<\/sub>+3x<sub>2<\/sub>+0x<sub>3<\/sub>+0x<sub>4<\/sub>+1x<sub>5<\/sub><=15<br\/>x<sub>1<\/sub>&ge;0<br\/>x<sub>2<\/sub>&ge;0<br\/>x<sub>3<\/sub>&ge;0<br\/>x<sub>4<\/sub>&ge;0<br\/>x<sub>5<\/sub>&ge;0<br\/><br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(0)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><td class=\"center\">0<\/td><td>2<\/td><td>5<\/td><td>1<\/td><td>0<\/td><td>0<\/td><td>30<\/td><td data-dane=\"dc,30,5,6\">6<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td>2<\/td><td>3<\/td><td>0<\/td><td>1<\/td><td>0<\/td><td>26<\/td><td data-dane=\"dc,26,3,26\/3\">26\/3<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><td class=\"center\">0<\/td><td>0<\/td><td class=\"mainelement\">3<\/td><td>0<\/td><td>0<\/td><td>1<\/td><td>15<\/td><td data-dane=\"dc,15,3,5\">5<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td>-2<\/td><td>-6<\/td><td>0<\/td><td>0<\/td><td>0<\/td><td>0<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>1<\/sub>=[0,0,30,26,15]<br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(1)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><td class=\"center\">0<\/td><td class=\"mainelement\" data-dane=\"g,2,5,0,3\">2<\/td><td data-dane=\"c,5,3\">0<\/td><td data-dane=\"g,1,5,0,3\">1<\/td><td data-dane=\"g,0,5,0,3\">0<\/td><td data-dane=\"g,0,5,1,3\">-5\/3<\/td><td data-dane=\"g,30,5,15,3\">5<\/td><td data-dane=\"dc,5,2,5\/2\">5\/2<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td data-dane=\"g,2,3,0,3\">2<\/td><td data-dane=\"c,3,3\">0<\/td><td data-dane=\"g,0,3,0,3\">0<\/td><td data-dane=\"g,1,3,0,3\">1<\/td><td data-dane=\"g,0,3,1,3\">-1<\/td><td data-dane=\"g,26,3,15,3\">11<\/td><td data-dane=\"dc,11,2,11\/2\">11\/2<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><td class=\"center\">6<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"m,1,3\">1<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"r,1,3\">1\/3<\/td><td data-dane=\"r,15,3\">5<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td data-dane=\"g,-2,-6,0,3\">-2<\/td><td data-dane=\"c,-6,3\">0<\/td><td data-dane=\"g,0,-6,0,3\">0<\/td><td data-dane=\"g,0,-6,0,3\">0<\/td><td data-dane=\"g,0,-6,1,3\">2<\/td><td data-dane=\"g,0,-6,15,3\">30<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>2<\/sub>=[0,5,5,11,0]<br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(2)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><td class=\"center\">2<\/td><td data-dane=\"m,1,2\">1<\/td><td data-dane=\"r,0,2\">0<\/td><td data-dane=\"r,1,2\">1\/2<\/td><td data-dane=\"r,0,2\">0<\/td><td data-dane=\"r,-5\/3,2\">-5\/6<\/td><td data-dane=\"r,5,2\">5\/2<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td data-dane=\"c,2,2\">0<\/td><td data-dane=\"g,0,2,0,2\">0<\/td><td data-dane=\"g,0,2,1,2\">-1<\/td><td data-dane=\"g,1,2,0,2\">1<\/td><td data-dane=\"g,-1,2,-5\/3,2\">2\/3<\/td><td data-dane=\"g,11,2,5,2\">6<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><td class=\"center\">6<\/td><td data-dane=\"c,0,2\">0<\/td><td data-dane=\"g,1,0,0,2\">1<\/td><td data-dane=\"g,0,0,1,2\">0<\/td><td data-dane=\"g,0,0,0,2\">0<\/td><td data-dane=\"g,1\/3,0,-5\/3,2\">1\/3<\/td><td data-dane=\"g,5,0,5,2\">5<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td data-dane=\"c,-2,2\">0<\/td><td data-dane=\"g,0,-2,0,2\">0<\/td><td data-dane=\"g,0,-2,1,2\">1<\/td><td data-dane=\"g,0,-2,0,2\">0<\/td><td data-dane=\"g,2,-2,-5\/3,2\">1\/3<\/td><td data-dane=\"g,30,-2,5,2\">35<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>3<\/sub>=[2.5,5,0,6,0]<br\/>x<sub>1<\/sub>=5\/2 (2.5)<br\/>x<sub>2<\/sub>=5<br\/>x<sub>3<\/sub>=0<br\/>x<sub>4<\/sub>=6<br\/>x<sub>5<\/sub>=0<br\/>W=35",[{"label":"S1","data":[[0,6],[15,0]]},{"label":"S2","data":[[0,8.6666666666667],[13,0]]},{"label":"S3","data":[[0,5],[15,5]]},{"label":"gradient","data":[[0,0],[3.75,11.25]]},{"label":"A1","data":[[0,0]],"points":{"show":true}},{"label":"A2","data":[[0,5]],"points":{"show":true}},{"label":"A3","data":[[2.5,5]],"points":{"show":true}}],false];
				$.plot($("#placeholder1"), data2[2]);
			});
		</script>
		<style>
			#placeholder1{
				width: 480px;
				height: 360px;
			}
		</style>
    </head>
    <body>
		<div id="placeholder1"></div>
	</body>
</html>
