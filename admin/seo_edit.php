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


	$DESC = str_replace('"', "&quot;", $DESC);
	foreach ($arLang as $LANG => $L) {
		$arFields = array(
			"OBJECT_TYPE" => "'" . $DB->ForSql($OBJECT_TYPE) . "'",
			"OBJECT_ID" => "'" . $DB->ForSql($OBJECT_ID) . "'",
			"TITLE" => "'" . $DB->ForSql($TITLE) . "'",
			"KEY" => "'" . $DB->ForSql($KEY) . "'",
			"DESC" => "'" . $DB->ForSql($DESC) . "'",
			"H1" => "'" . $DB->ForSql($H1) . "'",
			"LANG" => "'" . $DB->ForSql($LANG) . "'"
		);
		$DB->StartTransaction();
		if ($ID > 0) {
			$DB->Update("n_multilang_seo", $arFields, "WHERE ID='" . $ID . "'", $err_mess . __LINE__);
		} else {
			$ID = $DB->Insert("n_multilang_seo", $arFields, $err_mess . __LINE__);
		}
		$DB->Commit();
	}

	if ($ID > 0) {
		// если сохранение прошло удачно - перенаправим на новую страницу 
		// (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
		if ($apply != "") {
			// если была нажата кнопка "Применить" - отправляем обратно на форму.
			LocalRedirect("/bitrix/admin/nasledie.multilang_seo_edit.php?ID=" . $ID . "&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
		} else {
			// если была нажата кнопка "Сохранить" - отправляем к списку элементов.
			LocalRedirect("/bitrix/admin/nasledie.multilang_seo.php?lang=" . LANG);
		}
	} else {
		// если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
	}
}

$str_ID = false;
$str_OBJECT_TYPE = false;
$str_OBJECT_ID = false;
$str_TITLE = false;
$str_KEY = false;
$str_DESC = false;
$str_H1 = false;
$str_LANG = false;

$ipropValues = false;

if ($ID > 0) {
	$sql = "SELECT * FROM `n_multilang_seo` WHERE `ID`='" . $DB->ForSql($ID) . "'";
	$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
	while ($row = $rsData->Fetch()) {
		$str_ID = $row['ID'];
		$str_OBJECT_TYPE = $row['OBJECT_TYPE'];
		$str_OBJECT_ID = $row['OBJECT_ID'];
		$str_TITLE = $row['TITLE'];
		$str_KEY = $row['KEY'];
		$str_DESC = str_replace('"', "&quot;", $row['DESC']);
		$str_H1 = $row['H1'];
		$str_LANG = $row['LANG'];
	}
	if ($str_OBJECT_TYPE != '' && $str_OBJECT_ID > 0) {
		if ($str_OBJECT_TYPE == 'I') {
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\IblockValues($str_OBJECT_ID);
			$ipropValues = $ipropValues->getValues();
		} elseif ($str_OBJECT_TYPE == 'S') {
			$sql = "SELECT * FROM `b_iblock_section` WHERE `ID`='" . $DB->ForSql($str_OBJECT_ID) . "'";
			$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			while ($row = $rsData->Fetch()) {
				$arElement = $row;
			}
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arElement['IBLOCK_ID'], $str_OBJECT_ID);
			$ipropValues = $ipropValues->getValues();
		} elseif ($str_OBJECT_TYPE == 'E') {
			$sql = "SELECT * FROM `b_iblock_element` WHERE `ID`='" . $DB->ForSql($str_OBJECT_ID) . "'";
			$rsData = $DB->Query($sql, false, __FILE__ . " > " . __LINE__);
			while ($row = $rsData->Fetch()) {
				$arElement = $row;
			}
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arElement['IBLOCK_ID'], $str_OBJECT_ID);
			$ipropValues = $ipropValues->getValues();
		}
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
		<td><span class="required">*</span><? echo GetMessage("TBL_OBJECT_TYPE") ?></td>

		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("TBL_OBJECT_TYPE_I"),
					GetMessage("TBL_OBJECT_TYPE_S"),
					GetMessage("TBL_OBJECT_TYPE_E"),
				),
				"reference_id" => array(
					"I",
					"S",
					"E",
				)
			);
			echo SelectBoxFromArray("OBJECT_TYPE", $arr, $str_OBJECT_TYPE, "", "");
			?>
		</td>
	</tr>
	<tr>
		<td><span class="required">*</span><? echo GetMessage("TBL_OBJECT_ID") ?></td>

		<td>

			<input type="text" name="OBJECT_ID" style="width:100%;" value="<? echo $str_OBJECT_ID; ?>">

		</td>
	</tr>
	<tr>
		<td><span class="required">*</span><? echo GetMessage("TBL_TITLE") ?></td>

		<td>
			<? if ($ipropValues) { ?>
				<textarea readonly style="width:100%;"><?= ($str_OBJECT_TYPE == 'E' ? $ipropValues['ELEMENT_META_TITLE'] : $ipropValues['SECTION_META_TITLE'] ) ?></textarea>
<? } ?>
			<input type="text" name="TITLE" style="width:100%;" value="<? echo $str_TITLE; ?>">

		</td>
	</tr>
	<tr>
		<td><? echo GetMessage("TBL_KEY") ?></td>

		<td>
			<? if ($ipropValues) { ?>
				<textarea readonly style="width:100%;"><?= ($str_OBJECT_TYPE == 'E' ? $ipropValues['ELEMENT_META_KEYWORDS'] : $ipropValues['SECTION_META_KEYWORDS'] ) ?></textarea>
<? } ?>
			<input type="text" name="KEY" style="width:100%;" value="<? echo $str_KEY; ?>">

		</td>
	</tr>
	<tr>
		<td><span class="required">*</span><? echo GetMessage("TBL_DESC") ?></td>

		<td>
			<? if ($ipropValues) { ?>
				<textarea readonly style="width:100%;"><?= ($str_OBJECT_TYPE == 'E' ? $ipropValues['ELEMENT_META_DESCRIPTION'] : $ipropValues['SECTION_META_DESCRIPTION'] ) ?></textarea>
<? } ?>
			<input type="text" name="DESC" style="width:100%;" value="<? echo $str_DESC; ?>">

		</td>
	</tr>
	<tr>
		<td><span class="required">*</span><? echo GetMessage("TBL_H1") ?></td>

		<td>
			<? if ($ipropValues) { ?>
				<textarea readonly style="width:100%;"><?= ($str_OBJECT_TYPE == 'E' ? $ipropValues['ELEMENT_PAGE_TITLE'] : $ipropValues['SECTION_PAGE_TITLE'] ) ?></textarea>
<? } ?>
			<input type="text" name="H1" style="width:100%;" value="<? echo $str_H1; ?>">

		</td>
	</tr>
	<?
	$tabControl->Buttons(
		array(
			"back_url" => "nasledie.multilang_seo.php?lang=" . LANG,
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