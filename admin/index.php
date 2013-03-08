<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Simplex 2012&copy; Logowanie do panelu Administratora</title>
<link rel="icon" type="image/x-icon" href="../images/icon32.png" />
<link rel="stylesheet" href="../css/simplex.css" />
<style>
body{
overflow:hidden;
}
label {
	color: white;
}
</style>
</head>
<body>
	<img id="bg" alt="Background image" src="../images/back.jpg" />
	<div id="content">
		<div id="wrapper"></div>
		<div
			style="width: 500px; height: 400px; margin: 0px auto; margin-top: 15%;text-align:center;">
			<form id="loginform" method="POST" action="response.php">
				<div style="width:219px;margin:0px auto;">
					<a href="../index.php"><img alt="simplexlogo" src="../images/logo_header_min.png"></a>
				</div>
				<br /> <label for="login">Login:</label> <input type="text"
					name="login" /><br /> <label for="password">Hasło:</label> <input
					type="password" name="password" /><br />
				<button>OK</button>
			</form>
			<div><?php echo (isset($_GET['error']) && $_GET['error'] == 1) ? '<span style=\'color:red;background-color:white;\'>Zły login i/lub hasło. Spróbuj ponownie.</span>' : ''; ?></div>
			<div><?php echo (isset($_GET['logout']) && $_GET['logout'] == 1) ? '<span style=\'color:green;background-color:white;\'>Wylogowano pomyślnie.</span>' : ''; ?></div>
		</div>
	</div>
</body>
</html>
