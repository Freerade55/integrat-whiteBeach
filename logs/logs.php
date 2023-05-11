<?php
//
////// логи
//
function logsBookModule()

{


    if (file_exists(ROOT . "/logsBookModule.json")) {
        $log = file_get_contents(ROOT . "/logsBookModule.json");
        $log = json_decode($log, true);
    } else {
        $log = [];
    }

    $t = explode(" ",microtime());
    $log[date("Y-m-d H:i:s", $t[1]).substr((string)$t[0],1,4)] = $_REQUEST;
    $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    file_put_contents(ROOT . "/logsBookModule.json", $log);
}

function logsJivosite($webhook_data)

{

    $webhook_data = json_decode($webhook_data, true);

    if (file_exists(ROOT . "/logsJivosite.json")) {
        $log = file_get_contents(ROOT . "/logsJivosite.json");
        $log = json_decode($log, true);
    } else {
        $log = [];
    }

    $t = explode(" ",microtime());
    $log[date("Y-m-d H:i:s", $t[1]).substr((string)$t[0],1,4)] = $webhook_data;
    $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    file_put_contents(ROOT . "/logsJivosite.json", $log);
}




