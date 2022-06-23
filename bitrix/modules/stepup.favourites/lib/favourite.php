<?php

namespace stepup;

use stepup\Favourite\FavouriteTable;
use \Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class Favourite
{
    protected $isAuth = false;
    protected $user = null;
    protected $context;

    public function __construct()
    {
        global $USER;
        if($USER->IsAuthorized()){
            $this->isAuth = true;
            $this->user = $USER;
        }
        $app = Application::getInstance();
        $this->context = $app->getContext();
    }

    public function getItems()
    {
        $arItems = [];
        if($this->isAuth){
            $arItems = $this->getItemsFromDB($this->user->GetID());

        }
        return array_merge($arItems, $this->getItemsFromCookie());
    }

    protected function getItemsFromDB($userId)
    {
        $result = [];
        $resFavorites = FavouriteTable::GetList([
            'filter' => ["USER_ID" => $userId]
        ]);
        while($arFavorites = $resFavorites->fetch()){
            $result[] = $arFavorites['PRODUCT_ID'];
        }
        return $result;
    }

    protected function getItemsFromCookie()
    {
        $request = $this->context->getRequest();

        $result = $request->getCookie('FAVOURITES');
        if(!$result){
            return [];
        }else{
            return unserialize($result);
        }
    }

    protected function addCookie($arItems)
    {
        $cookie = new Cookie("FAVOURITES", serialize($arItems), time() + 60*60*24*7);
        $cookie->setDomain($this->context->getServer()->getHttpHost());
        $cookie->setHttpOnly(false);

        $this->context->getResponse()->addCookie($cookie);
        $this->context->getResponse()->writeHeaders("");
    }

    public function add($productId)
    {
        if(intval($productId) <= 0){
            return false;
        }

        if($this->isAuth){
            $arItems = $this->getItemsFromDB($this->user->GetID());
            if(in_array($productId, $arItems)){
                return false;
            }
            $arFields = [
                "USER_ID" => $this->user->GetID(),
                "PRODUCT_ID" => $productId
            ];
            $result = FavouriteTable::add($arFields);
            if ($result->isSuccess())
            {
                $arItems[] = $result->getId();
                return $arItems;
            }
        }else{
            $arItems = $this->getItemsFromCookie();
            $arItems[] = $productId;
            $this->addCookie($arItems);
            return $arItems;
        }

        return false;
    }

    public function delete($productId)
    {
        $arNewItems = [];
        if($this->isAuth){
            $resFavorites = FavouriteTable::GetList([
                'filter' => ["USER_ID" => $this->user->GetID(), 'PRODUCT_ID' => $productId]
            ]);
            while($arFavorites = $resFavorites->fetch()){
                FavouriteTable::delete($arFavorites['ID']);
            }
            $arNewItems = $this->getItemsFromDB($this->user->GetID());
        }

        $arItems = $this->getItemsFromCookie();
        foreach($arItems as $item){
            if($item != $productId){
                $arNewItems[] = $item;
            }
        }

        $this->addCookie($arNewItems);
        return $arNewItems;
    }
}