<?php
include '../../classes/dbconn.class.php';
$baza = dbconn::instance();
$result = $baza->query("SELECT idfeedback, ocena, version, opinion, date, ip FROM feedback ORDER BY idfeedback DESC LIMIT 100");
echo '<table class="commenttable">';
echo '<thead>';
echo '<tr><th class="ui-state-default">ID</th><th class="ui-state-default">Ocena</th><th class="ui-state-default">OS</th><th class="ui-state-default">Opinia</th><th class="ui-state-default">Data</th><th class="ui-state-default">Ip</th></tr>';
echo '</thead>';
echo '<tbody>';
while ($row = mysql_fetch_array($result)) {
	echo '<tr><td>'.$row['idfeedback'].'</td><td>'.$row['ocena'].'</td><td>'.$row['version'].'</td><td>'.$row['opinion'].'</td><td>'.$row['date'].'</td><td>'.$row['ip'].'</td></tr>';
}
echo '</tbody>';
echo '</table>';