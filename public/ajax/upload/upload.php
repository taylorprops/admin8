<?php

//$dir = 'C:\wamp64\www\docs\public\ajax\upload\uploads';
$dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'ajax'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'uploads';

if (!empty($_FILES)) {

	$temp_file   = $_FILES['Filedata']['tmp_name'];
	$file_parts = pathinfo($_FILES['Filedata']['name']);
	$file_name = $file_parts['filename'];
	$file_ext = $file_parts['extension'];

	$file_name = $file_name.'_'.date('YmdHis').'.'.$file_ext;
    $target_file = $dir .DIRECTORY_SEPARATOR. $file_name;

	move_uploaded_file($temp_file, $target_file);

}
?>
