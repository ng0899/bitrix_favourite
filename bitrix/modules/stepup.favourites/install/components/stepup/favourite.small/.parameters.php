<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
    "GROUPS" => array(
        "HTML" => array(
            "NAME" => GetMessage("PARAMS_HTML")
        ),
    ),
    "PARAMETERS" => array(
        'CACHE_TIME' => array('DEFAULT' => 120),
        "ELEMENT_CLASS_NAME" => array(
            "PARENT" => "HTML",
            "NAME" => GetMessage("ELEMENT_CLASS_NAME"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "favourite"
        ),
        "ID_TOTAL" => array(
            "PARENT" => "HTML",
            "NAME" => GetMessage("ID_TOTAL"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "f_total"
        ),
        "ACTIVE_CLASS" => array(
            "PARENT" => "HTML",
            "NAME" => GetMessage("ACTIVE_CLASS"),
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "DEFAULT" => "active"
        ),
    ),

);