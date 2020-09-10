<?php
/*
 * @author  Andi SiBmius <sibmius@gmail.com>
 * @license LGPL 3.0
 * @copyright 2007 RoW-Team
 */

use Bitrix\Main\Loader;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loader::includeModule('iblock');

$MODULE_ID = 'nasledie.multilang';

use \Bitrix\Main\Localization\Loc as Loc;

\Bitrix\Main\Loader::includeModule($MODULE_ID);

Loc::loadMessages(__FILE__);


$bVarsFromForm = false;

$arLang = array();

$aTabs = array();
$resLANG = CLanguage::GetList();
while ($L = $resLANG->Fetch()) {
	if ($L['LID'] == 'en') {
		continue;
	}
	if ($L['LID'] == 'ru') {
		continue;
	}
	$aTabs[] = array("DIV" => "edit" . $L['LID'], "TAB" => $L['LID'], "ICON" => "main_user_edit", "TITLE" => $L['LID']);
	$arLang[$L['LID']] = $L;
}



$tabControl = new CAdminTabControl("tabControl", $aTabs);



$message = null;  // сообщение об ошибке
$bVarsFromForm = false;

if (
	$REQUEST_METHOD == "POST" // проверка метода вызова страницы
	&&
	($save != "" || $apply != "") // проверка нажатия кнопок "Сохранить" и "Применить"
	&&
	check_bitrix_sessid()  // проверка идентификатора сессии
) {
	global $DB;



	foreach ($arLang as $LANG => $L) {
		if ($ENUM_XML_ID !='') {
			// обработка данных формы
			$arFields = array(
				"PROPERTY_ID" => "'" . $DB->ForSql($PROPERTY_ID) . "'",
				"ENUM_XML_ID" => "'" . $DB->ForSql($ENUM_XML_ID) . "'",
				"VALUE" => "'" . $DB->ForSql($VALUE[$LANG]) . "'",
				"LANG" => "'" . $DB->ForSql($LANG) . "'"
			);
			$DB->StartTransaction();
			if ($ID[$LANG] > 0) {
				$DB->Update("n_multilang_enum", $arFields, "WHERE ID='" . $ID[$LANG] . "'", $err_mess . __LINE__);
			} else {
				$DB->Insert("n_multilang_enum", $arFields, $err_mess . __LINE__);
			}
			$DB->Commit();
		}
	}

	if ($ENUM_XML_ID !='') {
		// если сохранение прошло удачно - перенаправим на новую страницу 
		// (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
		if ($apply != "") {
			// если была нажата кнопка "Применить" - отправляем обратно на форму.
			LocalRedirect("/bitrix/admin/nasledie.multilang_enum_edit.php?ENUM_XML_ID=" . $ENUM_XML_ID . "&PROPERTY_ID=".$PROPERTY_ID."&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
		} else {
			// если была нажата кнопка "Сохранить" - отправляем к списку элементов.
			LocalRedirect("/bitrix/admin/nasledie.multilang_enum.php?lang=" . LANG);
		}
	} else {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
	}
}

$str_ID = false;
$str_PROPERTY_ID = false;
$str_ENUM_XML_ID = false;
$str_VALUE = false;
$str_LANG = false;

if ($ENUM_XML_ID !='') {
	$sql = "SELECT * FROM `n_multilang_enum` WHERE `ENUM_XML_ID`='" . $DB->ForSql($ENUM_XML_ID) . "'";
	if($PROPERTY_ID>0){
		$sql.=" AND `PROPERTY_ID`='".$DB->ForSql($PROPERTY_ID)."' ";
	}
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$str_ID[$row['LANG']] = $row['ID'];
		$str_PROPERTY_ID = $row['PROPERTY_ID'];
		$str_ENUM_XML_ID[$row['LANG']] = $row['ENUM_XML_ID'];
		$str_VALUE[$row['LANG']] = $row['VALUE'];
		$str_LANG[$row['LANG']] = $row['LANG'];
	}
	$sql = "SELECT * FROM `b_iblock_property_enum` WHERE `XML_ID`='" . $DB->ForSql($ENUM_XML_ID) . "'";
	if($PROPERTY_ID>0){
		$sql.=" AND `PROPERTY_ID`='".$DB->ForSql($PROPERTY_ID)."' ";
	}
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$arProps = $row;
	}
}











$APPLICATION->SetTitle(GetMessage("IBLOCK_TITLE_EDIT"));



require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


if ($_REQUEST["mess"] == "ok" && $ID > 0) {
	CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage("IBLOCK_OK"), "TYPE" => "OK"));
}

if ($message) {
	echo $message->Show();
} elseif ($error) {
	CAdminMessage::ShowMessage($$error);
}
CModule::IncludeModule("iblock");
?>

<form method="POST" Action="<? echo $APPLICATION->GetCurPage() ?>" ENCTYPE="multipart/form-data" name="post_form">
	<? echo bitrix_sessid_post(); ?>
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<? if ($ENUM_XML_ID > 0 && !$bCopy) { ?>
		<input type="hidden" name="ENUM_XML_ID" value="<?= $ENUM_XML_ID ?>">
	<? } else { ?>
		<div class="adm-detail-block">
			<div class="adm-detail-content-wrap">
				<div class="adm-detail-content" style="padding: 12px 18px 12px 12px;">
					<div class="adm-detail-content-item-block">
						<table class="adm-detail-content-table edit-table">
							<tr>
								<td><span class="required">*</span>XML_ID</td>
								<td>
									<input type="text" name="ENUM_XML_ID" value="<? echo $ENUM_XML_ID; ?>" size="30" maxlength="100">
									<input type="submit" name="apply" value="Загрузить" title="Сохранить и остаться в форме">
								</td>
							</tr>
							<tr>
								<td><span class="required">*</span>PROPERTY_ID</td>
								<td>
									<input type="text" name="PROPERTY_ID" value="<? echo $str_PROPERTY_ID; ?>" size="30" maxlength="100">
									
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	<? }; ?>
	<?
// отобразим заголовки закладок
	$tabControl->Begin();
	?>

	<?php
	foreach ($arLang as $LANG => &$L) {

		$tabControl->BeginNextTab();
		?>
		<tr>
			<td><span class="required">*</span><? echo GetMessage("TBL_VALUE") ?></td>

			<td>
				<textarea readonly  style="width:100%;"><?php echo $arProps['VALUE'] ?></textarea>
				<input type="text" name="VALUE[<?= $LANG ?>]" value="<? echo $str_VALUE[$LANG]; ?>" size="100" maxlength="128">
				<input type="hidden" name="ID[<?= $LANG ?>]" value="<? echo $str_ID[$LANG]; ?>">
			</td>
		</tr>
		<?
	}
	$tabControl->Buttons(
		array(
			"back_url" => "nasledie.multilang_enum.php?lang=" . LANG,
		)
	);
	$tabControl->End();
	?>
</form>
<? echo BeginNote(); ?>
<span class="required">*</span><? echo GetMessage("REQUIRED_FIELDS") ?>
<? echo EndNote(); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>