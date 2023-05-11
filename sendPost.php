<?php


$token = "wr_573d804a-2da5-4e2d-a25b-48300ebc15ce"; // токен для авторизации и запросов
$url = 'https://wired.wubook.net/xrws/'; //
$lcode = "1604759947"; // код личного кабинета в модуле бронирования
$sendUrl = "https://hub.integrat.pro/api/WhiteBeach/bookmodule/index.php";
$te = "test= 1";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '<?xml version="1.0"?>
<methodCall>
    <methodName>push_url</methodName>
    <params>
        <param>
            <value>' . $token . '</value>
        </param>
        <param>
            <value>' . $lcode . '</value>
        </param>

    </params>
</methodCall>',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/xml'
    ),
));
$httpCode = curl_getinfo($curl);

$response = curl_exec($curl);
curl_close($curl);
print_r($response);




