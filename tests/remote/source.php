<?php

$json = array();
if (isset($_GET['a'])) {
	switch ($_GET['a']) {
		case 'a':
			$json[] = Array(1, 1, 1);
			break;
		case 'b':
			$json[] = Array(2, 2, 2);
			break;
		default :
			$json[] = Array(3, 3, 3);
			break;
	}
}
echo json_encode($json);
