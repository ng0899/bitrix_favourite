<?php

if (!\defined("B_PROLOG_INCLUDED") || \B_PROLOG_INCLUDED !== \true) {
    die;
}
use stepup\Favourite;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

class FavouriteSmall extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        // подключаем модуль
        if (!Loader::includeModule('stepup.favourites')) {
            throw new \Bitrix\Main\LoaderException(Loc::getMessage('MODULE_NOT_INSTALLED'));
        }

        // для ajax
        $this->arResult['_ORIGINAL_PARAMS'] = $arParams;

        return parent::onPrepareComponentParams($arParams);
    }

    public function executeComponent()
    {
        $this->setFrameMode(\true);
        try {
            $this->prepareResult();
            if ($this->arParams['IS_AJAX'] == 'Y') {
                $this->ajaxHandler();
            }
            $this->includeComponentTemplate();
        } catch (\Exception $e) {
            \ShowError($e->getMessage());
        }
        return parent::executeComponent();
    }

    public function ajaxHandler()
    {
        global $APPLICATION;
        $app = \Bitrix\Main\Application::getInstance();
        $request = $app->getContext()->getRequest();

        if (!$request->isAjaxRequest() || $this->arParams['IS_AJAX'] != 'Y') {
            return \true;
        }
        $action = !empty($request->getPost('action')) ? $request->getPost('action') : '';
        $productId = !empty($request->getPost('productId')) ? $request->getPost('productId') : '';
        $this->arResult['SUCCESS'] = false;
        $items = false;

        $APPLICATION->RestartBuffer();
        if($productId != ''){
            $favourite = new Favourite;
            if($action == 'add'){
                $items = $favourite->add($productId);
            }elseif($action == 'delete'){
                $items = $favourite->delete($productId);
            }
            $this->arResult['SUCCESS'] = true;
        }
        if(is_array($items)){
            $this->arResult['ITEMS'] = $items;
            $this->arResult['COUNT_ITEMS'] = count($items);
        }else{
            $this->prepareResult();
        }
        echo json_encode($this->arResult);
        die();
    }

    protected function prepareResult()
    {
        $favourite = new Favourite;
        $items = $favourite->getItems();
        $this->arResult['ITEMS'] = $items;
        $this->arResult['COUNT_ITEMS'] = count($items);
    }

}