<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Tytuł strony</title>
<script src="js/jquery.js"></script>
<script src="js/jquery.bgiframe.js"></script>
<script src="js/jquery.dimensions.js"></script>
<script src="js/jquery.tooltip.js"></script>
<script>
$(document).ready(function(){
	$('table.result:gt(0) td').tooltip({
		delay:0,
		showURL:false,
		fixPNG:true,
		track:true,
		bodyHandler:function(){
			ss=$(this).attr('data-dane');
			temp=ss.split(",");
			return $("<img/>").attr("src", './sources/Picture.php?a='+temp[0]+'&b='+temp[1]+'&c='+temp[2]+'&d='+temp[3]+'&e='+temp[4]).css({'background-color':'transparent','text-align':'center'});
			}
		
	});
});
</script>
</head>
<body bgcolor="#FF0000">
	<table border="1" class="result">
		<thead>
			<tr>
				<th>Head1</th>
				<th>Head2</th>
				<th>Head3</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>1</td>
				<td>2</td>
				<td>3</td>
			</tr>
		</tbody>
	</table>
	<table border="1" class="result">
		<thead>
			<tr>
				<th>Head1</th>
				<th>Head2</th>
				<th>Head3</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td data-dane="g,1/3,2,3,4">1</td>
				<td data-dane="r,2,3">2</td>
				<td data-dane="c,3,4">3</td>
				<td data-dane="m,1,4">4</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
