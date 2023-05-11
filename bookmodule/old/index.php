<?php

header('Content-Type: text/html; charset=utf-8');

const ROOT = __DIR__ . "/JivoSite";

require ROOT . "/functions/require.php";



$time = time();
$timestamp = (string)$time;

$webhook_data = file_get_contents( 'php://input');

logs($webhook_data);



file_put_contents(ROOT . '/dataForm/data_jivosite_'.$timestamp.'_form'.'.json', $webhook_data);
























