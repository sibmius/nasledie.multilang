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
			// обработка данных формы
			$arFields = array(
				"ORIG" => "'" . $DB->ForSql($ORIG) . "'",
				"INDEX" => "'" . $DB->ForSql(md5($ORIG)) . "'",
				"TRANSLATE" => "'" . $DB->ForSql($TRANSLATE) . "'",
				"LANG" => "'" . $DB->ForSql($LANG) . "'"
			);
			$DB->StartTransaction();
			if ($ID > 0) {
				$DB->Update("n_multilang_value", $arFields, "WHERE ID='" . $ID . "'", $err_mess . __LINE__);
			} else {
				$ID=$DB->Insert("n_multilang_value", $arFields, $err_mess . __LINE__);
			}
			$DB->Commit();
	}

	if ($ID > 0) {
		// если сохранение прошло удачно - перенаправим на новую страницу 
		// (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
		if ($apply != "") {
			// если была нажата кнопка "Применить" - отправляем обратно на форму.
			LocalRedirect("/bitrix/admin/nasledie.multilang_value_edit.php?ID=" . $ID . "&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
		} else {
			// если была нажата кнопка "Сохранить" - отправляем к списку элементов.
			LocalRedirect("/bitrix/admin/nasledie.multilang_value.php?lang=" . LANG);
		}
	} else {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
	}
}

$str_ID = false;
$str_ORIG = false;
$str_TRANSLATE = false;
$str_LANG = false;

if ($ID > 0) {
	$sql = "SELECT * FROM `n_multilang_value` WHERE `ID`='" . $DB->ForSql($ID) . "'";
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$str_ID = $row['ID'];
		$str_ORIG = $row['ORIG'];
		$str_TRANSLATE = $row['TRANSLATE'];
		$str_LANG = $row['LANG'];
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
	<input type="hidden" name="LANG" value="ua">
	<? if ($ID > 0 && !$bCopy) { ?>
		<input type="hidden" name="ID" value="<?= $str_ID ?>">
	<? }; ?>
	<?
// отобразим заголовки закладок
	$tabControl->Begin();
	?>

	<?php

		$tabControl->BeginNextTab();
		?>
		<tr>
			<td><span class="required">*</span><? echo GetMessage("TBL_ORIG") ?></td>

			<td>
				<textarea <?php echo ($str_ORIG!='' ? 'readonly':'');?> name="ORIG" style="width:100%;"><?php echo $str_ORIG ?></textarea>
			</td>
		</tr>
		<tr>
			<td><span class="required">*</span><? echo GetMessage("TBL_TRANSLATE") ?></td>

			<td>
				
				<textarea type="text" name="TRANSLATE" style="width:100%;"><? echo $str_TRANSLATE; ?></textarea>
				
			</td>
		</tr>
		<?
	$tabControl->Buttons(
		array(
			"back_url" => "nasledie.multilang_value.php?lang=" . LANG,
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