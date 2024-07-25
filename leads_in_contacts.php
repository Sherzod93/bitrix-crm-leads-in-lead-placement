<?php
//Подключаем ядро Битрикса
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

//Проверяем, установлен ли модуль и авторизован ли пользователь.
if (!CModule::IncludeModule('crm') || !CCrmSecurityHelper::IsAuthorized())
{
    die();
}

//Подключаем главные скрипты Битрикса
$APPLICATION->ShowHead();

//Проверяем, запрос Виджета
if($options = $_REQUEST["PLACEMENT_OPTIONS"]){
    $options = json_decode($options,1);
}
//Подготавливаем параметры компонента bitrix:crm.lead.list
$Params = [
    "LEAD_COUNT" => 20,
    "PATH_TO_LEAD_SHOW" =>"/crm/lead/show/#lead_id#/",
    "PATH_TO_LEAD_EDIT" => "/crm/lead/edit/#lead_id#/",
    "INTERNAL_FILTER" => [   
        "CONTACT_ID" => $options["ID"] //Фильтр по текущему ID лида
    ],
    
    "INTERNAL_CONTEXT" => Array
    (
        "CONTACT_ID" => $options["ID"]
    ),
    
    "GRID_ID_SUFFIX" => "CONTACT_DETAILS",
    "TAB_ID" => "tab_contact",
    "NAME_TEMPLATE" =>"",
    "ENABLE_TOOLBAR" => false,
    "PRESERVE_HISTORY" => true,
    //"ADD_EVENT_NAME" => "CrmCreateLeadFromContact"
];

//Подготавливаем параметры компонента для AJAX-запросов, например, при настройке полей в Грид таблице
$componentData = \CCrmInstantEditorHelper::signComponentParams(
    $Params,
    'crm.lead.list'
    );

//Подготавливаем параметры компонента для AJAX-запросов, например, при настройке полей в Грид таблице
$ajaxLoaderParams = array(
    'url' => '/bitrix/components/bitrix/crm.lead.list/lazyload.ajax.php?&site='.SITE_ID.'&'.bitrix_sessid_get(),
    'method' => 'POST',
    'dataType' => 'ajax',
    'data' => array('PARAMS' => ["template"=>"","signedParameters"=>$componentData,"TAB_ID"=>"tab_contact"])
);


//Подготавливаем параметры компонента bitrix:crm.lead.list
$componentParams  = $Params;
$componentParams['AJAX_MODE'] = 'Y';
$componentParams['AJAX_OPTION_JUMP'] = 'N';
$componentParams['AJAX_OPTION_HISTORY'] = 'N';
$componentParams['AJAX_LOADER'] = $ajaxLoaderParams;
//$componentParams["IFRAME"] = true;

//Установим компонент для отображения Лидов
$APPLICATION->IncludeComponent('bitrix:crm.lead.list',
    '',
    $componentParams,
    false,
    array('HIDE_ICONS' => 'Y', 'ACTIVE_COMPONENT' => 'Y')
    );


?>



