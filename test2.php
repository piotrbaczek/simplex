<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body>
	1/4x1+12x2+15x3&lt;=480
	<br /> -1/3x1+10x2+10x3&gt;=-20
	<br /> 3x1+5x2+4x3=6000
	<br />
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<textarea rows="10" cols="10" name="textarea"
			style="width: 300px; height: 300px;"><?php echo isset($_POST['textarea']) ? $_POST['textarea'] : '';?></textarea>
		<br />
		<input type="text" name="targetfunction" value="<?php echo isset($_POST['targetfunction'])? $_POST['targetfunction'] : '';?>"><br/>
		<input type="submit" value="Wyslij" />
	</form>
<?php
include 'classes/Ulamek.class.php';
include 'classes/TextareaProcesser.class.php';
if(isset($_POST['textarea'])){
	$s=new TextareaProcesser($_POST['textarea'],$_POST['targetfunction']);
	echo '<pre>';
	print_r($s->getTargetfunction());
	print_r($s->getSigns());
	print_r($s->getVariables());
	echo '</pre>';
}

?>
</body>
</html>
