<?php

namespace stepup;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'stepup.favourites',
    array(
        'stepup_favourites' => 'install/index.php',
        "\\stepup\\Favourite\\FavouriteTable" => "lib/mysql/favourite.php",
        "\\stepup\\Favourite" => "lib/favourite.php",
    )
);