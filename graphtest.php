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
					[{"label": "S1", "data": [[0, 6], [15, 0]]},
						{"label": "S2", "data": [[0, 8.6666666666667], [13, 0]]},
						{"label": "S3", "data": [[0, 5], [15, 5]]},
						{"label": "gradient", "data": [[0, 0], [3.75, 11.25]]},
						{label: "A", data: [[2, 3]], points: {show: true}, color: get_random_color()}
					]];
				$.plot($("#placeholder1"), data[1]);
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
