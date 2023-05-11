<?php


header('Content-Type: text/html; charset=utf-8');

const ROOT = __DIR__ . "/..";

require ROOT . "/functions/require.php";




$time = time();
$timestamp = (string)$time;

$webhook_data = file_get_contents( 'php://input');

logsJivosite($webhook_data);
$webhook_data_decode = json_decode($webhook_data, true);
$webhook_data_type = $webhook_data_decode["event_name"];

if($webhook_data_type == "client_updated") {


    if(!empty($webhook_data_decode["status"])) {

        $webhook_data_status = $webhook_data_decode["status"]["title"];

        if($webhook_data_status === "бронирование оформлено") {
            file_put_contents(ROOT . '/data/data_jivosite_'.$timestamp.'_chat_booking'.'.json', $webhook_data);
        }


    }


}












