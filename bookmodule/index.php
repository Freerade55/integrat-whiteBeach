<?php

const ROOT = __DIR__ . "/..";
require ROOT . "/functions/require.php";

$time = time();
$timestamp = (string)$time;

// Что-то вроде вебхука, в момент бронирования приходит $_POST['rcode'] (id брони) и $_POST['lcode'] (это id личного кабинета)
//$webhook_data = file_get_contents('php://input');
logsBookModule();

//$_POST['rcode'] = 1681979281;
// Если rcode (id брони) не пустой и он есть (т.е. бронирование совершено), то получаем всю информацию по клиенту
if (isset($_POST['rcode']) && !empty($_POST['rcode'])) {
    $token = "wr_573d804a-2da5-4e2d-a25b-48300ebc15ce"; // токен для авторизации и запросов
    $url = 'https://wired.wubook.net/xrws/'; //
    $lcode = "1604759947"; // код личного кабинета в модуле бронирования
    $rcode = $_POST['rcode'];

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
    <methodName>fetch_booking</methodName>
    <params>
        <param>
            <value>' . $token . '</value>
        </param>
        <param>
            <value>' . $lcode . '</value>
        </param>
        <param>
            <value>' . $rcode . '</value>
        </param>
    </params>
</methodCall>',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/xml'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    $xml = simplexml_load_string($response);
    $json = json_encode($xml);
    $array = json_decode($json, TRUE);

    $arr_client = array("id" => "", "name" => "", "surname" => "", "mail" => "", "phone" => "", "comment" => "", "book_nights" => "", "action_offer" => "", "date_arrival" => "", "date_departure" => "", "men" => "", "children" => "", "status" => "", "amount" => "");

    foreach ($array as $arr) {
        foreach ($arr as $arr_1) {
            foreach ($arr_1 as $arr_2) {
                foreach ($arr_2 as $arr_3) {
                    foreach ($arr_3 as $arr_4) {
                        foreach ($arr_4 as $arr_5) {
                            foreach ($arr_5 as $arr_6) {
                                foreach ($arr_6["array"]["data"] as $arr_7) {
                                    $arr_client["id"] = $arr_7["struct"]["member"][1]["value"];
                                    $arr_client["amount"] = $arr_7["struct"]["member"][2]["value"];
                                    $arr_client["status"] = $arr_7["struct"]["member"][11]["value"];
                                    $arr_client["date_arrival"] = $arr_7["struct"]["member"][17]["value"];
                                    $arr_client["date_departure"] = $arr_7["struct"]["member"][18]["value"];
                                    $arr_client["men"] = $arr_7["struct"]["member"][21]["value"];
                                    $arr_client["children"] = $arr_7["struct"]["member"][22]["value"];
                                    $arr_client["name"] = $arr_7["struct"]["member"][26]["value"];
                                    $arr_client["mail"] = $arr_7["struct"]["member"][25]["value"];
                                    $arr_client["surname"] = $arr_7["struct"]["member"][27]["value"];
                                    $arr_client["comment"] = $arr_7["struct"]["member"][28]["value"];
                                    $arr_client["phone"] = $arr_7["struct"]["member"][29]["value"];
                                    $arr_client["book_nights"] = $arr_7["struct"]["member"][34]["value"];
                                    $arr_client["action_offer"] = $arr_7["struct"]["member"][41]["value"];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $arr_client = json_encode($arr_client, JSON_UNESCAPED_UNICODE);
    file_put_contents(ROOT . '/dataForm/data_wubook_' . $timestamp . '.json', $arr_client);
}


?>