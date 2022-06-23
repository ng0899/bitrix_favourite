<?php

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$menu = array(
    array(
        'parent_menu' => 'global_menu_services',
        'sort' => 400,
        'text' => Loc::getMessage('MYMODULE_MENU_TITLE'),
        'title' => Loc::getMessage('MYMODULE_MENU_TITLE'),
        'items_id' => 'menu_favourites',
        'items' => array(
            array(
                'text' => Loc::getMessage('MYMODULE_SUBMENU_TITLE'),
                'url' => 'favourite_favourite.php?lang=' . LANGUAGE_ID,
                'more_url' => array(),
                'title' => Loc::getMessage('MYMODULE_SUBMENU_TITLE'),
            ),
        ),
    ),
);

return $menu;