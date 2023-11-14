<?php

//echo dirname(__FILE__);

try {
    if (
        !isset($_FILES['file']['error']) ||
        is_array($_FILES['file']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
	$blacklist = array(".php", ".phtml", ".php3", ".php4", ".html", ".htm", ".sql");
	foreach ($blacklist as $item)
	{
		if(preg_match("/$item\$/i", $_FILES['file']['name']))
		{	
			echo json_encode([
				'status' => 'error',
				'message' => 'Невозможно загрузить данный тип файла!'
			], JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	$whitelist = array('jpg','jpeg','png','gif','txt','doc','docx','pdf','xls','xlsx','rar','zip','dwg','cat','dws','dwt','dxf','rtf','bmp','cdr','7z','odt','tif');
	$fileInfo = pathinfo($_FILES['file']['name']);	
	foreach ($whitelist as $item)
	{		
		if(!in_array(strtolower($fileInfo['extension']), $whitelist))		
		{			
			http_response_code(400);
			echo json_encode([
				'status' => 'error',
				'message' => 'Невозможно загрузить данный тип файла!'
			], JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
    $filepath = sprintf(dirname(__FILE__).'/upload/%s_%s', uniqid(), $_FILES['file']['name']);
    //$filepath = dirname(__FILE__).'/'.$_FILES['file']['name'];

    if (!move_uploaded_file(
        $_FILES['file']['tmp_name'],
        $filepath
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    // All good, send the response
    echo json_encode([
        'status' => 'ok',
		'message' => 'Загрузка завершена!',
        'path' => $filepath
    ], JSON_UNESCAPED_UNICODE);

} catch (RuntimeException $e) {
	// Something went wrong, send the err message as JSON
	http_response_code(400);

	echo json_encode([
		'status' => 'error',
		'message' => $e->getMessage()
	], JSON_UNESCAPED_UNICODE);
}