<?php

$json = array();
if (isset($_GET['a'])) {
	switch ($_GET['a']) {
		case 1:
			$json[] = Array(1, 1, 1);
			break;
		case 2:
			$json[] = Array(2, 2, 2);
			break;
		default :
			$json[] = Array(3, 3, 3);
			break;
	}
}
echo json_encode($json);
