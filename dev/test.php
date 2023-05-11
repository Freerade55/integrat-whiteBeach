<?php

const ROOT = __DIR__ . "/..";

require ROOT . "/functions/require.php";





$hooksFolder = scandir(ROOT."/data");

$hooksSortedFolder = [];





foreach ($hooksFolder as $value) {

    if(substr_count($value, ".json")) {
        $hooksSortedFolder[] = $value;

    }
}



$hooksArrayNames = [];

switch (true) {

    case count($hooksSortedFolder) === 0:
        die;

    case count($hooksSortedFolder) <= 10:


        foreach ($hooksSortedFolder as $value) {

            if(substr_count($value, ".json")) {
                $hooksArrayNames[] = $value;

            }
        }

        break;

    case count($hooksSortedFolder) > 10:

        for($i = 0; $i < 10; $i++) {

            if(substr_count($hooksSortedFolder[$i], ".json")) {
                $hooksArrayNames[] = $hooksSortedFolder[$i];

            }

        }


}


$hooksArrayData = [];

foreach ($hooksArrayNames as $value) {


    $data = file_get_contents(ROOT . "/data/$value");
    $data = json_decode($data, true);




    if(!empty($data["visitor"])) {


        if(!empty($data["visitor"]["name"] && !empty($data["visitor"]["phone"]))) {


            $phone_to_search = preg_replace("/[^\d]/siu", "", $data["visitor"]["phone"]);

            $phone_to_search = preg_replace("/^[8]/", "7", $phone_to_search, 1);

            $description = $data["visitor"]["description"] ?? " ";




//            ищет контакт по телефону
            $contactRes = searchEntity(CRM_ENTITY_CONTACT, $phone_to_search);


            if(!empty($contactRes)) {


                if (!empty($contactRes["_embedded"]["contacts"][0]["_embedded"]["leads"])) {

//                  проверка на подходящую сделку по воронке и этапам
                    $leadConfirm = null;

                    $leads = $contactRes["_embedded"]["contacts"][0]["_embedded"]["leads"];

                    foreach ($leads as $lead) {


                        $getLead = getEntity(CRM_ENTITY_LEAD, $lead["id"]);


                        if($getLead["pipeline_id"] == CRM_PIPELINE) {

                            if($getLead["status_id"] != CRM_COMPLETE_STATUS && $getLead["status_id"] != CRM_REJECT_STATUS) {

                                $leadConfirm = true;


                            }

                        }

                    }

                // это если сделки есть, но подходящей по параметрам нет

                    if(!isset($leadConfirm)) {

                        addLead($contactRes["_embedded"]["contacts"][0]["id"], $contactRes["_embedded"]["contacts"][0]["name"], $contactRes["_embedded"]["contacts"][0]["responsible_user_id"]);



                    }



                } else {

//                    если контакт есть, но сделок у него нет

                    addLead($contactRes["_embedded"]["contacts"][0]["id"], $contactRes["_embedded"]["contacts"][0]["name"], $contactRes["_embedded"]["contacts"][0]["responsible_user_id"]);



                }

                // добавляется примечание и задача в любом случае


                addNote("contacts", $contactRes["_embedded"]["contacts"][0]["id"], $description);


                addTask($contactRes["_embedded"]["contacts"][0]["id"],$contactRes["_embedded"]["contacts"][0]["responsible_user_id"]);

            } else {

//                если вообще ничего нет, то делается контакт, сделки и тд



                $addedUser = addContact($data["visitor"]["name"], $phone_to_search);

                addLead($addedUser["_embedded"]["contacts"][0]["id"], $data["visitor"]["name"]);


                addNote("contacts", $addedUser["_embedded"]["contacts"][0]["id"], $description);


                addTask($addedUser["_embedded"]["contacts"][0]["id"], CRM_DEFAULT_RESPONS_USER);




            }




        }

    }


}



//
//foreach ($hooksArrayNames as $value) {
//
//    unlink(ROOT . "/data/$value");
//
//
//
//}
//
//
//
//
//
//
//
//









