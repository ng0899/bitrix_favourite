<?php

namespace stepup\Favourite;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\Validator;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type;
Loc::loadMessages(__FILE__);

class FavouriteTable extends DataManager
{

    public static function getTableName()
    {
        return 'favourite';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true
            )),
            new IntegerField('USER_ID', array()),
            new IntegerField('PRODUCT_ID', array()),
            new DatetimeField('DATE_CREATE',array(
                'required' => true,
                'default_value' => new Type\DateTime
                )),
        );
    }
}