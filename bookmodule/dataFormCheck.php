<?php

const ROOT = __DIR__ . "/..";

require ROOT . "/functions/require.php";


sleep(5);

$hooksFolder = scandir(ROOT."/dataForm");

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


foreach ($hooksArrayNames as $value) {


    $data = file_get_contents(ROOT . "/dataForm/$value");
    $data = json_decode($data, true);


    $formArray = [];

    $description = "";

    $contactInfo = [];





    foreach ($data as $key => $fieldValueArray) {

        foreach ($fieldValueArray as $fieldValue) {


            if($key == "name") {

                $contactInfo["name"] = $fieldValue;

            }

            else if($key == "surname") {

                $contactInfo["surname"] = $fieldValue;
            }


            else if($key == "phone") {

                $contactInfo["phone"] = $fieldValue;
            }


            else if($key == "action_offer") {

            if(!empty($fieldValue)) {

                $description = "$description акция - {$fieldValue["member"][0]["value"]["string"]} \n";

            }



            } else if($key == "mail") {

                $contactInfo["mail"] = $fieldValue;

            } else if($key == "comment") {

                if(!empty($fieldValue)) {

                    $description = "$description комментарий - $fieldValue \n";

                }



            } else if($key == "status") {

                if($fieldValue == "1") {

                    $description = "$description статус заявки - подтвержденный \n";

                } else {

                    $description = "$description статус заявки - в процессе одобрения \n";

                }

            }

            else if($key == "id") {

                $description = "$description айди брони - $fieldValue \n";

            }

            else {
                $formArray[$key] = $fieldValue;

            }


        }

    }




    $phone_to_search = preg_replace("/[^\d]/siu", "", $data["phone"]["string"]);

    $phone_to_search = preg_replace("/^[8]/", "7", $phone_to_search, 1);





//            ищет контакт по телефону
    $contactRes = searchEntity(CRM_ENTITY_CONTACT, $phone_to_search);




    if(!empty($contactRes)) {


        if (!empty($contactRes["_embedded"]["contacts"][0]["_embedded"]["leads"])) {

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


            if(!isset($leadConfirm)) {

                addLead($contactRes["_embedded"]["contacts"][0]["id"], $contactRes["_embedded"]["contacts"][0]["name"], $contactRes["_embedded"]["contacts"][0]["responsible_user_id"], $formArray);


            }





            // добавляется примечание и задача в любом случае
//            $description = $data["visitor"]["description"] ?? " ";

            addNote("contacts", $contactRes["_embedded"]["contacts"][0]["id"], $description);


            addTask($contactRes["_embedded"]["contacts"][0]["id"],$contactRes["_embedded"]["contacts"][0]["responsible_user_id"]);



        } else {

            addLead($contactRes["_embedded"]["contacts"][0]["id"], $contactRes["_embedded"]["contacts"][0]["name"], $contactRes["_embedded"]["contacts"][0]["responsible_user_id"], $formArray);

        }


    } else {


        $addedUser = addContact($contactInfo["name"] . " " . $contactInfo["surname"], $phone_to_search, $contactInfo);


        addLead($addedUser["_embedded"]["contacts"][0]["id"], $contactInfo["name"] . " " .$contactInfo["surname"], CRM_DEFAULT_RESPONS_USER, $formArray);




        addNote("contacts", $addedUser["_embedded"]["contacts"][0]["id"], $description);

        addTask($addedUser["_embedded"]["contacts"][0]["id"], CRM_DEFAULT_RESPONS_USER);




    }









}



foreach ($hooksArrayNames as $value) {

    unlink(ROOT . "/dataForm/$value");



}







