<?php
global $db, $allowFuncs, $url, $core;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$allowFuncs = array_merge($allowFuncs, ['selectCode2']);

function selectCode2() {

    require_once ROOT . '/vendor/autoload.php';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . ROOT . '/8f715c9461e6.json');
    $client = new Google_Client;
    $client->useApplicationDefaultCredentials();
    $client->setApplicationName("Something to do with my representatives");
    $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds']);
    if ($client->isAccessTokenExpired()) {
        $client->refreshTokenWithAssertion();
    }
    $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
    ServiceRequestFactory::setInstance(
        new DefaultServiceRequest($accessToken)
    );
    $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)
        ->getSpreadsheetFeed()
        ->getByTitle('copy_btp');
    $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
    $worksheet = $worksheets[6];

    foreach ($_POST as $key => $value) {
        $$key = (!empty($value) ? str_replace('.', ',', $value) : 0);
    }
    global $core, $db;
    $cellFeed = $worksheet->getCellFeed();
    $batchRequest = new Google\Spreadsheet\Batch\BatchRequest();

    $batchRequest->addEntry($cellFeed->createCell(14, 1, $block_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 1, $block_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 1, $block_3));

    $batchRequest->addEntry($cellFeed->createCell(14, 3, $n_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 3, $n_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 3, $n_3));
    $batchRequest->addEntry($cellFeed->createCell(17, 3, $n_gvs));

    $batchRequest->addEntry($cellFeed->createCell(13, 14, $t_1));
    $batchRequest->addEntry($cellFeed->createCell(14, 14, $t_11));
    $batchRequest->addEntry($cellFeed->createCell(15, 14, $t_12));
    $batchRequest->addEntry($cellFeed->createCell(16, 14, $t_13));
    $batchRequest->addEntry($cellFeed->createCell(17, 14, $t_3));

    $batchRequest->addEntry($cellFeed->createCell(13, 18, $t_2));
    $batchRequest->addEntry($cellFeed->createCell(14, 18, $t_21));
    $batchRequest->addEntry($cellFeed->createCell(15, 18, $t_22));
    $batchRequest->addEntry($cellFeed->createCell(16, 18, $t_23));
    $batchRequest->addEntry($cellFeed->createCell(17, 18, $b_1));

    $batchRequest->addEntry($cellFeed->createCell(13, 23, $p_1));
    $batchRequest->addEntry($cellFeed->createCell(13, 29, $p_2));

    $batchRequest->addEntry($cellFeed->createCell(14, 26, $pot_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 26, $pot_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 26, $pot_3));
    $batchRequest->addEntry($cellFeed->createCell(17, 26, $dav_xvs));

    $batchRequest->addEntry($cellFeed->createCell(20, 3, $block_bv_uzel));
    $batchRequest->addEntry($cellFeed->createCell(20, 9, $block_bv_podpit));
    $batchRequest->addEntry($cellFeed->createCell(20, 15, $block_bv_reg));
    $batchRequest->addEntry($cellFeed->createCell(20, 18, $block_bv_reg_d));
    $batchRequest->addEntry($cellFeed->createCell(20, 23, $block_bv_ogr));
    $batchRequest->addEntry($cellFeed->createCell(20, 26, $block_bv_ogr_d));

    $batchRequest->addEntry($cellFeed->createCell(24, 3, $block_bov_1_schema));
    $batchRequest->addEntry($cellFeed->createCell(24, 9, $block_bov_1_pump));
    $batchRequest->addEntry($cellFeed->createCell(24, 15, $block_bov_1_klapan));
    $batchRequest->addEntry($cellFeed->createCell(24, 21, $block_bov_1_control));
    $batchRequest->addEntry($cellFeed->createCell(24, 27, $block_bov_1_auto));

    $batchRequest->addEntry($cellFeed->createCell(28, 3, $block_bov_2_schema));
    $batchRequest->addEntry($cellFeed->createCell(28, 9, $block_bov_2_pump));
    $batchRequest->addEntry($cellFeed->createCell(28, 15, $block_bov_2_klapan));
    $batchRequest->addEntry($cellFeed->createCell(28, 21, $block_bov_2_control));
    $batchRequest->addEntry($cellFeed->createCell(28, 27, $block_bov_2_auto));

    $batchRequest->addEntry($cellFeed->createCell(32, 3, $block_bov_3_schema));
    $batchRequest->addEntry($cellFeed->createCell(32, 9, $block_bov_3_pump));
    $batchRequest->addEntry($cellFeed->createCell(32, 15, $block_bov_3_klapan));
    $batchRequest->addEntry($cellFeed->createCell(32, 21, $block_bov_3_control));
    $batchRequest->addEntry($cellFeed->createCell(32, 27, $block_bov_3_auto));

    $batchRequest->addEntry($cellFeed->createCell(36, 3, $block_bgv_schema));
    $batchRequest->addEntry($cellFeed->createCell(36, 9, $block_bgv_pump));
    $batchRequest->addEntry($cellFeed->createCell(36, 15, $block_bgv_klapan));
    $batchRequest->addEntry($cellFeed->createCell(36, 21, $block_bgv_control));
    $batchRequest->addEntry($cellFeed->createCell(36, 27, $block_bgv_auto));

    $batchResponse = $cellFeed->insertBatch($batchRequest);

    $cellFeed = $worksheet->getCellFeed();
    echo 'Блок ввода БВ <br>';
    for ($i = 2; $i < 21; $i++) {
        $topLeftCornerCell = $cellFeed->getCell(21, $i);
        echo $topLeftCornerCell->getContent();
    }

    echo '<br>Блок отоп. вент. БОВ-1<br>';
    for ($i = 2; $i < 25; $i++) {
        $topLeftCornerCell = $cellFeed->getCell(25, $i);
        echo $topLeftCornerCell->getContent();
    }

    echo '<br>Блок отоп. вент. БОВ-2<br>';
    for ($i = 2; $i < 25; $i++) {
        $topLeftCornerCell = $cellFeed->getCell(29, $i);
        echo $topLeftCornerCell->getContent();
    }

    echo '<br>Блок отоп. вент. БОВ-3<br>';
    for ($i = 2; $i < 25; $i++) {
        $topLeftCornerCell = $cellFeed->getCell(33, $i);
        echo $topLeftCornerCell->getContent();
    }

    echo '<br>Блок ГВС БГВ<br>';
    for ($i = 2; $i < 31; $i++) {
        $topLeftCornerCell = $cellFeed->getCell(37, $i);
        echo $topLeftCornerCell->getContent();
    }





}