<?php
//подключаем основные классы для работы с модулем
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use stepup\Favourite\FavouriteTable;

Loc::loadMessages(__FILE__);

class stepup_favourites extends CModule
{
    public $MODULE_ID = "stepup.favourites";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME = GetMessage('MYMODULE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MYMODULE_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('MYMODULE_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://stepup24.ru';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallFiles();
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components",
            $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
        DeleteDirFilesEx("/local/components/stepup");
        return true;
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            FavouriteTable::getEntity()->createDbTable();

        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            if (Application::getConnection()->isTableExists(Base::getInstance('\stepup\Favourite\FavouriteTable')->getDBTableName())) {
                $connection = Application::getInstance()->getConnection();
                $connection->dropTable(FavouriteTable::getTableName());
            }
        }
    }
}