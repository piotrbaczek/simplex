<?php

$error = "";
$msg = "";
$fileElementName = 'fileToUpload';
if (!empty($_FILES[$fileElementName]['error'])) {
    switch ($_FILES[$fileElementName]['error']) {
        case '1':
            $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            break;
        case '2':
            $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            break;
        case '3':
            $error = 'The uploaded file was only partially uploaded';
            break;
        case '4':
            $error = 'No file was uploaded.';
            break;
        case '6':
            $error = 'Missing a temporary folder';
            break;
        case '7':
            $error = 'Failed to write file to disk';
            break;
        case '8':
            $error = 'File upload stopped by extension';
            break;
        case '999':
        default:
            $error = 'No error code avaiable';
    }
} elseif (empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none') {
    $error = 'No file was uploaded..';
} else {
    $filename=md5($_FILES['fileToUpload']['name']);
    $msg .= $filename;
    try {
        copy($_FILES['fileToUpload']['tmp_name'], "../download/" . $filename.'.csv');
    } catch (Exception $e) {
        $error.=$e;
    }
}
$json = Array('error' => $error, 'msg' => $msg);
echo json_encode($json);
?>