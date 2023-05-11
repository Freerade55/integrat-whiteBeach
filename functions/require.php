<?php
require ROOT . "/functions/display-errors.php";
require ROOT . "/vendor/autoload.php";
require ROOT . "/logs/logs.php";
require ROOT . "/functions/saveHook.php";
require ROOT . "/functions/connectToCrm.php";
require ROOT . "/functions/refreshToken.php";
require ROOT . "/functions/crmMethods.php";

const
CRM_ENTITY_LEAD = "lead",
CRM_ENTITY_CONTACT = "contact",
CRM_ENTITY_COMPANY = "company",

CRM_TASK_TYPE_ID = 2779694,

CRM_PIPELINE = 6252066,
CRM_COMPLETE_STATUS = 142,
CRM_REJECT_STATUS = 143,
CRM_DEFAULT_RESPONS_USER = 6616657,

METHOD_POST = "POST",
METHOD_PATCH = "PATCH";

$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();