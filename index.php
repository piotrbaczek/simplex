<?php
include 'classes/activity.class.php';
$ss = activity::isactivated2('activity/active.xml') == 'true' ? true : false;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="noindex,nofollow"/>
        <title>Simplex &copy; 2013</title>
        <link rel="icon" type="image/x-icon" href="images/icon32.png" />
        <link rel="stylesheet"
              href="js/development-bundle/themes/smoothness/jquery-ui-1.8.16.custom.css" />
        <link rel="stylesheet" href="css/simplex.css" />
        <script src="js/excanvas.js"></script>
        <script src="js/js/jquery-1.6.2.min.js"></script>
        <script src="js/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="js/jquery.bgiframe.js"></script>
        <script src="js/jquery.dimensions.js"></script>
        <script src="js/jquery.tooltip.js"></script>
        <script src="js/jquery.flot.js"></script>
        <script src="js/jquery.validate.js"></script>
        <script src="js/jquery.blockui.js"></script>
        <script src="js/ajaxfileupload.js"></script>
        <script src="js/simplex.js"></script>
        <script src="js/CanvasXpress.min.js"></script>
    </head>
    <body>
        <img id="bg" alt="Background image" src="images/back.jpg" />
        <div id="content">
            <div id="wrapper"></div>
            <div id="header">
                <img src="images/logo_header_min.png" id="header_logo" alt="icon" />
                <div class="right">
                    <button id="firstButton">Ulubione</button>
                </div>
            </div>
			<?php if ($ss) { ?>
				<div id="tabs">
					<ul>
						<li><a href="#tabs-1">Rozwiąż!</a></li>
						<li><a href="#tabs-2">Załaduj dane</a></li>
						<li><a href="sources/whatitcando.html">Co to jest Simpleks?</a></li>
					</ul>
					<div id="tabs-1">
						<div id="defaultdiv" style="width: 100%; height: 500px;">
							<div id="lewydiv" class="left" style="width: 49%;">
								<div style="width: 542px; margin: 0px auto;">
									<img src="images/logo_header.png" alt="logo" />
								</div>
							</div>
							<div class="right" style="width: 49%;">
								<div class="left" style="width: 49%; text-align: right;">
									<div class="label">Ekstremum funkcji celu:</div>
									<div class="label">Algorytm Gomorry'ego:</div>
									<div class="label">Wpisz funkcję celu:</div>
									<div class="label">Wpisz zagadnienie optymalizacyjne:</div>
								</div>
								<div class="right" style="width: 49%;">
									<form id="solvethisform">
										<select name="funct" id="funct">
											<option value="true">Maksymalizacja</option>
											<option value="false">Minimalizacja</option>
										</select> <br /> <select name="gomorryf" id="gomorryf">
											<option value="false">Nie</option>
											<option value="true">Tak</option>
										</select> <br /> <input type="text" name="targetfunction"
																id="targetfunction" value="2x1+6x2"><br />
										<textarea rows="10" name="textarea" id="textarea"
												  style="width: 100%; resize: none;">2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15</textarea>
										<button id="solvethis">Rozwiąż!</button>
									</form>
								</div>
							</div>
						</div>
						<div id="resultdiv3" class="hidden">
							<div id="loadbackbutton3">
								<button id="backbutton3">Wróć</button>
							</div>
							<div id="resultdiv2" style="width: 960px;"></div>
						</div>
					</div>
					<div id="tabs-2">
						<div id="fileloader">
							<p>
								Tutaj można załadować plik *.csv z danymi.<br /> <a
									href="download/Simplex.example.csv"> Przykład</a> poprawnego
								pliku z danymi:<br /> max;false;2;6<br /> 2;5;&lt;=;30<br />
								2;3;&lt;=;26<br /> 0;5;&lt;=;15<br />
							</p>
							<form name="form" action="index.php" method="POST"
								  enctype="multipart/form-data">
								<input type="file" id="fileToUpload" name="fileToUpload"
									   accept="text/csv" /><br /> <input type="text" class="fake" />
								<button id="loadfile">Wczytaj</button>
							</form>
						</div>
						<div id="result" class="hidden">
							<div id="loadbackdiv">
								<button id="loadback">Wróć</button>
							</div>
							<div id="result2" style="width: 960px;"></div>
						</div>
					</div>
				</div>
			<?php
			} else {
				activity::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!');
			}
			?>
            <div id="footer">
                <a href="mailto:pgolasz@gmail.com">Piotr Gołasz</a> &copy; 2013 <a
                    href="admin/index.php">Panel Administratora</a>
            </div>
        </div>
    </body>
</html>
