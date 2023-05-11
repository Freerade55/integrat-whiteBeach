<?php


//  Выводит по id сущность, можно передать любую. Сделку, компанию и тд
function getEntity(string $entity_type, int $id): array
{
    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts/$id?with=leads";
            break;
        case CRM_ENTITY_LEAD:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads/$id?with=contacts";
            break;
        case CRM_ENTITY_COMPANY:
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies/$id?with=contacts";
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }


}





//  Ищет сущность по строке, можно передать любую. Сделку, компанию и тд.
function searchEntity(string $entity_type, string $search): array
{


    switch ($entity_type) {
        case CRM_ENTITY_CONTACT:
            $query = [
                "with" => "leads",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts?" . http_build_query($query);
            break;
        case CRM_ENTITY_LEAD:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads?" . http_build_query($query);
            break;
        case CRM_ENTITY_COMPANY:
            $query = [
                "with" => "contacts",
                "query" => $search
            ];
            $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/companies?" . http_build_query($query);
            break;
    }


    $result = json_decode(connect($link), true);

    if (empty($result)) {
        return [];
    } else {
        return $result;
    }

}





// добавление контакта
function addContact(string $ContactName, $phone, array $formArray = []) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/contacts";



    if(!empty($formArray)) {

        $queryData = array(

            [

                "name" => $ContactName,

                "responsible_user_id" => CRM_DEFAULT_RESPONS_USER,

                "custom_fields_values" => [
                    [
                        "field_id" => 550699,
                        "values" => [
                            [
                                "value" => $phone
                            ]
                        ]
                    ],

                    [
                        "field_id" => 550701,
                        "values" => [
                            [
                                "value" => $formArray["mail"]
                            ]
                        ]
                    ],

                ]



            ]



        );

    } else {

        $queryData = array(

            [

                "name" => $ContactName,

                "responsible_user_id" => CRM_DEFAULT_RESPONS_USER,

                "custom_fields_values" => [
                    [
                        "field_id" => 550699,
                        "values" => [
                            [
                                "value" => $phone
                            ]
                        ]
                    ]

                ]



            ]



        );

    }


    return json_decode(connect($link, METHOD_POST, $queryData), true);




}










// добавление сделки
function addLead(int $contact_Id, string $leadName, int $responseUser = CRM_DEFAULT_RESPONS_USER, array $formArray = []) {

    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/leads";





    if(!empty($formArray)) {

        $formArray["date_arrival"] = strtotime(preg_replace("/\//", ".", $formArray["date_arrival"]));

        $formArray["date_departure"] = strtotime(preg_replace("/\//", ".", $formArray["date_departure"]));


        $queryData = array(

            [

                "name" => $leadName,

                "responsible_user_id" => $responseUser,
                "pipeline_id" => CRM_PIPELINE,
                "custom_fields_values" => [
                    [
                        "field_id" => 555015,
                        "values" => [
                            [
                                "value" => $formArray["children"]
                            ]
                        ]
                    ],

                    [
                        "field_id" => 554871,
                        "values" => [
                            [
                                "value" => $formArray["men"]
                            ]
                        ]
                    ],

                    [
                        "field_id" => 554947,
                        "values" => [
                            [
                                "value" => $formArray["date_arrival"]
                            ]
                        ]
                    ],

                    [
                        "field_id" => 554949,
                        "values" => [
                            [
                                "value" => $formArray["date_departure"]
                            ]
                        ]
                    ]






                ],
                "_embedded" => [
                    "contacts" => [
                        [
                            "id" => $contact_Id
                        ]
                    ]
                ]



            ]




        );



    } else {

        $queryData = array(

            [

                "name" => $leadName,

                "responsible_user_id" => $responseUser,
                "pipeline_id" => CRM_PIPELINE,
                "_embedded" => [
                    "contacts" => [
                        [
                            "id" => $contact_Id
                        ]
                    ]
                ]



            ]




        );
    }




    return json_decode(connect($link, METHOD_POST, $queryData), true);




}



// добавление примечания
function addNote(string $common, int $entity_id, string $description) {



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/$common/$entity_id/notes";
    $queryData = array(

        [

            "note_type" => "common",
            "params" => [
                "text" => $description
            ]


        ]




    );

    return json_decode(connect($link, METHOD_POST, $queryData), true);




}



// добавление задачи по дублю контакта
function addTask(int $contactId, int $responsible_user_id)
{



    $link = "https://{$_ENV["SUBDOMAIN"]}.amocrm.ru/api/v4/tasks";


    $timestamp = time() + 60*60;


    $queryData = array(
        [
            "text" => "Добавлено примечание к сделке по Белому пляжу",
            "entity_id" => $contactId,
            "complete_till" => $timestamp,
            "entity_type" => "contacts",
            "task_type_id" => CRM_TASK_TYPE_ID,
            "responsible_user_id" => $responsible_user_id

        ]
    );

    json_decode(connect($link, METHOD_POST, $queryData), true);









}




