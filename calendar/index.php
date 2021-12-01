<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Календарь");
?>
<?$APPLICATION->IncludeComponent( "local:calendar", "", Array( "IBLOCK_ID"=>1 ) );?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>