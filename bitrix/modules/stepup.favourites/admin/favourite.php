<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/stepup.favourites/include.php");

$blogModulePermissions = $APPLICATION->GetGroupRight("stepup.favourites");
if ($blogModulePermissions < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

use \stepup\Favourite\FavouriteTable;

$sTableID = "favourite";

$oSort = new CAdminSorting($sTableID, "ID", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
    "filter_user_id",
    "filter_product_id",
    "filter_id"
);
$USER_FIELD_MANAGER->AdminListAddFilterFields("FAVOURITE", $arFilterFields);

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
if ($filter_user_id <> '')
    $arFilter["USER_ID"] = $filter_user_id;
if ($filter_product_id <> '')
    $arFilter["PRODUCT_ID"] = $filter_product_id;
if ($filter_id <> '')
    $arFilter["ID"] = $filter_id;


$USER_FIELD_MANAGER->AdminListAddFilter("FAVOURITE", $arFilter);

if (($arID = $lAdmin->GroupAction()) && $blogModulePermissions >= "W")
{
    if ($_REQUEST['action_target']=='selected')
    {
        $arID = Array();
        $dbResultList = FavouriteTable::GetList(
            array($by => $order),
            $arFilter,
            false,
            false,
            array("ID")
        );
        while ($arResult = $dbResultList->Fetch())
            $arID[] = $arResult['ID'];
    }

    foreach ($arID as $ID)
    {
        if ($ID == '')
            continue;

        switch ($_REQUEST['action'])
        {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();

                if (!FavouriteTable::Delete($ID))
                {
                    $DB->Rollback();

                    if ($ex = $APPLICATION->GetException())
                        $lAdmin->AddGroupError($ex->GetString(), $ID);
                    else
                        $lAdmin->AddGroupError(GetMessage("FV_DELETE_ERROR"), $ID);
                }

                $DB->Commit();

                break;
        }
    }
}

$arHeaders = array(
    array("id"=>"ID", "content"=>"ID", "sort"=>"ID", "default"=>true),
    array("id"=>"USER_ID", "content"=>GetMessage("FV_USER_ID"), "sort"=>"USER_ID", "default"=>true),
    array("id"=>"PRODUCT_ID", "content"=>GetMessage("FV_PRODUCT_ID"), "sort"=>"PRODUCT_ID", "default"=>true),
    array("id"=>"DATE_CREATE", "content"=>GetMessage('FV_DATE_CREATE'), "sort"=>"DATE_CREATE", "default"=>true),
);
$USER_FIELD_MANAGER->AdminListAddHeaders("FAVOURITE", $arHeaders);
$lAdmin->AddHeaders($arHeaders);

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$arSelectedFields = array("ID", "USER_ID", "PRODUCT_ID", "DATE_CREATE");

foreach($arVisibleColumns as $val)
    if(!in_array($val, $arSelectedFields))
        $arSelectedFields[] = $val;


$dbResultList = FavouriteTable::GetList([
    'order' => array($by => $order),
    'filter' => $arFilter,
    'select' => $arSelectedFields,
    //array("nPageSize"=>CAdminResult::GetNavSize($sTableID)),

]);

$dbResultList = new CAdminResult($dbResultList, $sTableID);
$dbResultList->NavStart();

$lAdmin->NavText($dbResultList->GetNavPrint(GetMessage("BLB_GROUP_NAV")));

while ($arFavourite = $dbResultList->NavNext(true, "f_"))
{
    $row =& $lAdmin->AddRow($f_ID, $arFavourite, "/bitrix/admin/blog_blog_edit.php?ID=".$f_ID."&lang=".LANGUAGE_ID, GetMessage("BLB_UPDATE_ALT"));

    $row->AddField("ID", '<a href="/bitrix/admin/blog_blog_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID.'" title="'.GetMessage("BLB_UPDATE_ALT").'">'.$f_ID.'</a>');
    $row->AddField("USER_ID", "<a href=\"\">".$f_USER_ID."</a>");
    $row->AddField("PRODUCT_ID", "<a href=\"\">".$f_PRODUCT_ID."</a>");
    $row->AddField("DATE_CREATE", $f_DATE_CREATE);


    $USER_FIELD_MANAGER->AddUserFields("FAVOURITE", $arFavourite, $row);

    $arActions = Array();
    $arActions[] = array("ICON"=>"edit", "TEXT"=>GetMessage("FV_UPDATE_ALT"), "ACTION"=>$lAdmin->ActionRedirect("favorite_edit.php?ID=".$f_ID."&lang=".LANG."&".GetFilterParams("filter_").""), "DEFAULT"=>true);
    if ($blogModulePermissions >= "U")
    {
        $arActions[] = array("SEPARATOR" => true);
        $arActions[] = array("ICON"=>"delete", "TEXT"=>GetMessage("FV_DELETE_ALT"), "ACTION"=>"if(confirm('".GetMessage('BLB_DELETE_CONF')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete"));
    }

    $row->AddActions($arActions);
}

$lAdmin->AddFooter(
    array(
        array(
            "title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
            "value" => $dbResultList->SelectedRowsCount()
        ),
        array(
            "counter" => true,
            "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"),
            "value" => "0"
        ),
    )
);

$lAdmin->AddGroupActionTable(
    array(
        "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
    )
);

//if ($blogModulePermissions >= "W")
//{
//    $aContext = array(
//        array(
//            "TEXT" => GetMessage("FV_ADD_NEW"),
//            "ICON" => "btn_new",
//            "LINK" => "blog_blog_edit.php?lang=".LANG,
//            "TITLE" => GetMessage("BLB_ADD_NEW_ALT")
//        ),
//    );
//    $lAdmin->AddAdminContextMenu($aContext);
//}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle(GetMessage("FV_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
        <?
        $oFilter = new CAdminFilter(
            $sTableID."_filter",
            array(
                GetMessage("FV_USER_ID"),
                GetMessage("FV_PRODUCT_ID"),
                GetMessage("FV_DATE_CREATE"),
                "ID"
            )
        );

        $oFilter->Begin();
        ?>
        <tr>
            <td><?echo GetMessage("FV_USER_ID")?>:</td>
            <td><input type="text" name="filter_user_id" value="<?echo htmlspecialcharsbx($filter_user_id)?>" size="40"><?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?echo GetMessage("FV_PRODUCT_ID")?>:</td>
            <td><input type="text" name="filter_product_id" value="<?echo htmlspecialcharsbx($filter_product_id)?>" size="40"></td>
        </tr>
        <tr>
            <td>ID:</td>
            <td><input type="text" name="filter_id" value="<?echo htmlspecialcharsbx($filter_id)?>" size="40"></td>
        </tr>
        <?
        $USER_FIELD_MANAGER->AdminListShowFilter("FAVOURITE");

        $oFilter->Buttons(
            array(
                "table_id" => $sTableID,
                "url" => $APPLICATION->GetCurPage(),
                "form" => "find_form"
            )
        );
        $oFilter->End();
        ?>
    </form>

<?
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>