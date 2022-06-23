<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<!--HTML-->

<?php
// component parameters
$signer = new \Bitrix\Main\Security\Sign\Signer();
$signedParameters = $signer->sign(\base64_encode(\serialize($arParams)));
$signedTemplate = $signer->sign('');
?>

<script>
    Favourite.init({
        sessid: BX.bitrix_sessid(),
        siteId: '<?=SITE_ID?>',
        ajaxUrl: '<?=CUtil::JSEscape($component->getPath().'/ajax.php')?>',
        template: '<?=$signedTemplate?>',
        parameters: '<?=$signedParameters?>',
        elementClass: '<?=$arParams['ELEMENT_CLASS_NAME']?>',
        totalId: '<?=$arParams['ID_TOTAL']?>',
        activeClass: '<?=$arParams['ACTIVE_CLASS']?>',
    });
</script>
