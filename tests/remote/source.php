<?php

$json = array();
if (isset($_GET['a'])) {
	switch ($_GET['a']) {
		case 'a':
			$json[] = Array(1, 1, 1);
			break;
		case 'b':
			$json[] = Array((float) 0, (float) 0, (float) 0.1);
			break;
		default :
			$json[] = Array(3, 3, 3);
			break;
	}
}
echo json_encode($json);
